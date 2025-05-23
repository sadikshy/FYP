<?php
/**
 * Script to check for orders that need notifications
 * This will help diagnose why notifications aren't being sent
 */

// Set error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h1>Order Notification Check</h1>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";

// Check for orders that need preparation notifications (10 minutes before pickup time)
$sql = "SELECT * FROM takeout_order_items 
        WHERE (status = 'verified' OR status = 'pending') 
        AND pickup_time IS NOT NULL 
        AND (preparation_notification_sent = 0 OR preparation_notification_sent IS NULL)";

$result = $conn->query($sql);

echo "<h2>Orders that need preparation notifications:</h2>";
if ($result && $result->num_rows > 0) {
    echo "<p>Found " . $result->num_rows . " orders that need preparation notifications.</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Order ID</th><th>Email</th><th>Pickup Time</th><th>Current Time</th><th>Preparation Time</th><th>Time Until Preparation</th></tr>";
    
    while ($order = $result->fetch_assoc()) {
        $orderId = $order['order_id'];
        $email = $order['email'];
        $pickupTime = $order['pickup_time'];
        $pickupTimeStamp = strtotime($pickupTime);
        $preparationTime = $pickupTimeStamp - (10 * 60); // 10 minutes before pickup
        $currentTime = time();
        $timeUntilPreparation = $preparationTime - $currentTime;
        
        echo "<tr>";
        echo "<td>" . $orderId . "</td>";
        echo "<td>" . $email . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $pickupTimeStamp) . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $currentTime) . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $preparationTime) . "</td>";
        echo "<td>" . gmdate('H:i:s', abs($timeUntilPreparation)) . " " . ($timeUntilPreparation < 0 ? "overdue" : "remaining") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No orders found that need preparation notifications.</p>";
}

// Check for orders that need ready notifications (at pickup time)
$sql = "SELECT * FROM takeout_order_items 
        WHERE status = 'preparation' 
        AND pickup_time IS NOT NULL 
        AND (ready_notification_sent = 0 OR ready_notification_sent IS NULL)";

$result = $conn->query($sql);

echo "<h2>Orders that need ready notifications:</h2>";
if ($result && $result->num_rows > 0) {
    echo "<p>Found " . $result->num_rows . " orders that need ready notifications.</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Order ID</th><th>Email</th><th>Pickup Time</th><th>Current Time</th><th>Time Until Pickup</th></tr>";
    
    while ($order = $result->fetch_assoc()) {
        $orderId = $order['order_id'];
        $email = $order['email'];
        $pickupTime = $order['pickup_time'];
        $pickupTimeStamp = strtotime($pickupTime);
        $currentTime = time();
        $timeUntilPickup = $pickupTimeStamp - $currentTime;
        
        echo "<tr>";
        echo "<td>" . $orderId . "</td>";
        echo "<td>" . $email . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $pickupTimeStamp) . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $currentTime) . "</td>";
        echo "<td>" . gmdate('H:i:s', abs($timeUntilPickup)) . " " . ($timeUntilPickup < 0 ? "overdue" : "remaining") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No orders found that need ready notifications.</p>";
}

// Check if the necessary columns exist
echo "<h2>Database Structure Check:</h2>";
$columnsToCheck = [
    'pickup_time',
    'preparation_notification_sent',
    'ready_notification_sent'
];

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Column Name</th><th>Status</th></tr>";

foreach ($columnsToCheck as $column) {
    $sql = "SHOW COLUMNS FROM takeout_order_items LIKE '$column'";
    $result = $conn->query($sql);
    
    echo "<tr>";
    echo "<td>" . $column . "</td>";
    echo "<td>" . ($result->num_rows > 0 ? "Exists" : "Missing") . "</td>";
    echo "</tr>";
}
echo "</table>";

// Close the connection
$conn->close();
?>
