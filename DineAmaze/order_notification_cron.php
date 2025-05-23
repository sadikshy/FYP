<?php
/**
 * Order notification cron job
 * This script should be set up to run every 5 minutes via a cron job
 * It checks for orders that need preparation or ready notifications
 */

// Include the file-based notification system
require_once 'includes/file_notifications.php';

// Set timezone
date_default_timezone_set('Asia/Kathmandu');

// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Log file for debugging
$logFile = 'logs/order_notifications_' . date('Y-m-d') . '.log';
$logDir = dirname($logFile);
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

// Function to log messages
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

logMessage("Starting order notification check...");

// Make sure necessary columns exist
$columnsToCheck = [
    'preparation_notification_sent' => 'TINYINT(1) DEFAULT 0',
    'ready_notification_sent' => 'TINYINT(1) DEFAULT 0'
];

foreach ($columnsToCheck as $column => $definition) {
    $sql = "SHOW COLUMNS FROM takeout_order_items LIKE '$column'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        $sql = "ALTER TABLE takeout_order_items ADD COLUMN $column $definition";
        if ($conn->query($sql)) {
            logMessage("Added $column column to takeout_order_items table");
        } else {
            logMessage("Error adding $column column: " . $conn->error);
        }
    }
}

// Current time
$currentTime = time();

// Check for orders that need preparation notifications (10 minutes before pickup time)
$sql = "SELECT * FROM takeout_order_items 
        WHERE (status = 'verified' OR status = 'pending') 
        AND pickup_time IS NOT NULL 
        AND (preparation_notification_sent = 0 OR preparation_notification_sent IS NULL)";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    logMessage("Found " . $result->num_rows . " orders to check for preparation notifications");
    
    while ($order = $result->fetch_assoc()) {
        $orderId = $order['order_id'];
        $pickupTime = strtotime($order['pickup_time']);
        $preparationTime = $pickupTime - (10 * 60); // 10 minutes before pickup
        
        // If it's time to send preparation notification
        if ($currentTime >= $preparationTime) {
            logMessage("Sending preparation notification for order #$orderId");
            
            // Get user's email and name
            $email = $order['email'];
            $userSql = "SELECT name FROM user WHERE email = '$email'";
            $userResult = $conn->query($userSql);
            $name = ($userResult && $userResult->num_rows > 0) ? $userResult->fetch_assoc()['name'] : 'Customer';
            
            // Send notification
            if (sendOrderPreparationNotification($email, $name, $orderId, $order)) {
                // Update order status to preparation
                $updateSql = "UPDATE takeout_order_items SET status = 'preparation', preparation_notification_sent = 1 WHERE order_id = '$orderId'";
                if ($conn->query($updateSql)) {
                    logMessage("Updated order #$orderId status to preparation");
                } else {
                    logMessage("Error updating order #$orderId status: " . $conn->error);
                }
            } else {
                logMessage("Failed to send preparation notification for order #$orderId");
            }
        }
    }
}

// Check for orders that need ready notifications (at pickup time)
$sql = "SELECT * FROM takeout_order_items 
        WHERE status = 'preparation' 
        AND pickup_time IS NOT NULL 
        AND (ready_notification_sent = 0 OR ready_notification_sent IS NULL)";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    logMessage("Found " . $result->num_rows . " orders to check for ready notifications");
    
    while ($order = $result->fetch_assoc()) {
        $orderId = $order['order_id'];
        $pickupTime = strtotime($order['pickup_time']);
        
        // If it's time to send ready notification
        if ($currentTime >= $pickupTime) {
            logMessage("Sending ready notification for order #$orderId");
            
            // Get user's email and name
            $email = $order['email'];
            $userSql = "SELECT name FROM user WHERE email = '$email'";
            $userResult = $conn->query($userSql);
            $name = ($userResult && $userResult->num_rows > 0) ? $userResult->fetch_assoc()['name'] : 'Customer';
            
            // Send notification
            if (sendOrderReadyNotification($email, $name, $orderId, $order)) {
                // Update order status to ready
                $updateSql = "UPDATE takeout_order_items SET status = 'ready', ready_notification_sent = 1 WHERE order_id = '$orderId'";
                if ($conn->query($updateSql)) {
                    logMessage("Updated order #$orderId status to ready");
                } else {
                    logMessage("Error updating order #$orderId status: " . $conn->error);
                }
            } else {
                logMessage("Failed to send ready notification for order #$orderId");
            }
        }
    }
}

logMessage("Order notification check completed");
$conn->close();
?>
