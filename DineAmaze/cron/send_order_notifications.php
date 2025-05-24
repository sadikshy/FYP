<?php
/**
 * Automated Order Notification Script
 * This script should be executed by a cron job or scheduled task
 * Recommended schedule: Every 5 minutes
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define the base path
define('BASE_PATH', dirname(__DIR__));

// Include the order notifications helper
require_once BASE_PATH . '/includes/order_notifications.php';

// Log file
$logFile = BASE_PATH . '/logs/notification_cron.log';

// Create logs directory if it doesn't exist
if (!file_exists(dirname($logFile))) {
    mkdir(dirname($logFile), 0777, true);
}

// Log function
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

// Log execution start
logMessage('Starting order notification check');

try {
    // Output directly to browser for debugging
    echo "<h2>Notification Debugging Output</h2>";
    echo "<p>Script started at: " . date('Y-m-d H:i:s') . "</p>";
    
    // Check database connection
    $conn = new mysqli("localhost", "root", "", "dineamaze_database");
    if ($conn->connect_error) {
        echo "<p style='color:red'>Database connection failed: " . $conn->connect_error . "</p>";
        logMessage('Database connection failed: ' . $conn->connect_error);
    } else {
        echo "<p style='color:green'>Database connection successful</p>";
        
        // Check if the required columns exist
        $columnsCheck = $conn->query("SHOW COLUMNS FROM takeout_order_items LIKE 'preparation_notification_sent'");
        if ($columnsCheck->num_rows == 0) {
            echo "<p style='color:red'>Missing column: preparation_notification_sent</p>";
        } else {
            echo "<p style='color:green'>Column exists: preparation_notification_sent</p>";
        }
        
        $columnsCheck = $conn->query("SHOW COLUMNS FROM takeout_order_items LIKE 'pickup_notification_sent'");
        if ($columnsCheck->num_rows == 0) {
            echo "<p style='color:red'>Missing column: pickup_notification_sent</p>";
        } else {
            echo "<p style='color:green'>Column exists: pickup_notification_sent</p>";
        }
        
        // Check for orders that need notifications
        $currentTime = date('Y-m-d H:i:s');
        echo "<p>Current time: $currentTime</p>";
        
        // Check for orders needing preparation notification
        $prepSql = "SELECT COUNT(*) as count FROM takeout_order_items o 
                   JOIN takeout_customers c ON o.order_group_id = c.order_group_id 
                   WHERE o.status = 'confirmed' 
                   AND o.preparation_notification_sent = 0 
                   AND o.pickup_time > '$currentTime'";
        $prepResult = $conn->query($prepSql);
        if ($prepResult) {
            $count = $prepResult->fetch_assoc()['count'];
            echo "<p>Orders needing preparation notification: $count</p>";
        } else {
            echo "<p style='color:red'>Error checking for preparation notifications: " . $conn->error . "</p>";
        }
        
        // Check for orders needing pickup notification
        $tenMinutesFromNow = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $pickupSql = "SELECT COUNT(*) as count FROM takeout_order_items o 
                     JOIN takeout_customers c ON o.order_group_id = c.order_group_id 
                     WHERE o.status = 'preparation' 
                     AND o.pickup_notification_sent = 0 
                     AND o.pickup_time <= '$tenMinutesFromNow' 
                     AND o.pickup_time > '$currentTime'";
        $pickupResult = $conn->query($pickupSql);
        if ($pickupResult) {
            $count = $pickupResult->fetch_assoc()['count'];
            echo "<p>Orders needing pickup notification: $count</p>";
        } else {
            echo "<p style='color:red'>Error checking for pickup notifications: " . $conn->error . "</p>";
        }
        
        $conn->close();
    }
    
    // Check for orders that need notifications
    echo "<p>Calling checkAndSendOrderNotifications()...</p>";
    checkAndSendOrderNotifications();
    echo "<p style='color:green'>Order notification check completed successfully</p>";
    logMessage('Order notification check completed successfully');
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    logMessage('Error during order notification check: ' . $e->getMessage());
}

// Log execution end
logMessage('Finished order notification process');
