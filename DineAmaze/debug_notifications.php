<?php
/**
 * Notification System Debugging Script
 * This script helps diagnose issues with the email notification system
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once 'includes/order_notifications.php';

// HTML header
echo "<!DOCTYPE html>
<html>
<head>
    <title>DineAmaze Notification System Debug</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { color: #764ba2; }
        h2 { color: #4a4a4a; margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ddd; }
        th { background-color: #f2f2f2; padding: 10px; text-align: left; }
        td { padding: 10px; }
        .section { margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>DineAmaze Notification System Debug</h1>";

// Database connection check
echo "<div class='section'><h2>1. Database Connection</h2>";
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    echo "<p class='error'>❌ Database connection failed: " . $conn->connect_error . "</p>";
} else {
    echo "<p class='success'>✅ Database connection successful</p>";
}

// Check database tables and columns
echo "</div><div class='section'><h2>2. Database Structure</h2>";

// Check takeout_order_items table
$tableCheck = $conn->query("SHOW TABLES LIKE 'takeout_order_items'");
if ($tableCheck->num_rows == 0) {
    echo "<p class='error'>❌ Table 'takeout_order_items' does not exist</p>";
} else {
    echo "<p class='success'>✅ Table 'takeout_order_items' exists</p>";
    
    // Check required columns
    $columnsCheck = $conn->query("SHOW COLUMNS FROM takeout_order_items LIKE 'preparation_notification_sent'");
    if ($columnsCheck->num_rows == 0) {
        echo "<p class='error'>❌ Column 'preparation_notification_sent' does not exist</p>";
        echo "<p>Run the database update script: <a href='admin/update_database_for_notifications.php'>admin/update_database_for_notifications.php</a></p>";
    } else {
        echo "<p class='success'>✅ Column 'preparation_notification_sent' exists</p>";
    }
    
    $columnsCheck = $conn->query("SHOW COLUMNS FROM takeout_order_items LIKE 'pickup_notification_sent'");
    if ($columnsCheck->num_rows == 0) {
        echo "<p class='error'>❌ Column 'pickup_notification_sent' does not exist</p>";
        echo "<p>Run the database update script: <a href='admin/update_database_for_notifications.php'>admin/update_database_for_notifications.php</a></p>";
    } else {
        echo "<p class='success'>✅ Column 'pickup_notification_sent' exists</p>";
    }
}

// Check takeout_customers table
$tableCheck = $conn->query("SHOW TABLES LIKE 'takeout_customers'");
if ($tableCheck->num_rows == 0) {
    echo "<p class='error'>❌ Table 'takeout_customers' does not exist</p>";
} else {
    echo "<p class='success'>✅ Table 'takeout_customers' exists</p>";
}

// Check for orders in the system
echo "</div><div class='section'><h2>3. Order Status</h2>";
$currentTime = date('Y-m-d H:i:s');
echo "<p>Current time: $currentTime</p>";

// Get all orders
$orderSql = "SELECT o.*, c.full_name, c.email 
             FROM takeout_order_items o 
             LEFT JOIN takeout_customers c ON o.order_group_id = c.order_group_id 
             ORDER BY o.pickup_time DESC LIMIT 10";
$orderResult = $conn->query($orderSql);

if (!$orderResult) {
    echo "<p class='error'>❌ Error querying orders: " . $conn->error . "</p>";
} else if ($orderResult->num_rows == 0) {
    echo "<p class='warning'>⚠️ No orders found in the system</p>";
    echo "<p>You need to place some orders before notifications can be sent.</p>";
} else {
    echo "<p class='success'>✅ Found " . $orderResult->num_rows . " recent orders</p>";
    
    echo "<table>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Status</th>
                <th>Pickup Time</th>
                <th>Prep. Notif.</th>
                <th>Pickup Notif.</th>
                <th>Eligible For</th>
            </tr>";
    
    $tenMinutesFromNow = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    while ($order = $orderResult->fetch_assoc()) {
        $eligibleFor = [];
        
        // Check if eligible for preparation notification
        if ($order['status'] == 'confirmed' && $order['preparation_notification_sent'] == 0 && $order['pickup_time'] > $currentTime) {
            $eligibleFor[] = "Preparation";
        }
        
        // Check if eligible for pickup notification
        if ($order['status'] == 'preparation' && $order['pickup_notification_sent'] == 0 && 
            $order['pickup_time'] <= $tenMinutesFromNow && $order['pickup_time'] > $currentTime) {
            $eligibleFor[] = "Pickup";
        }
        
        $eligibleText = !empty($eligibleFor) ? implode(", ", $eligibleFor) : "None";
        $eligibleClass = !empty($eligibleFor) ? "success" : "";
        
        echo "<tr>
                <td>" . $order['order_id'] . "</td>
                <td>" . htmlspecialchars($order['full_name'] ?? 'N/A') . "</td>
                <td>" . htmlspecialchars($order['email'] ?? 'N/A') . "</td>
                <td>" . $order['status'] . "</td>
                <td>" . $order['pickup_time'] . "</td>
                <td>" . ($order['preparation_notification_sent'] ? 'Yes' : 'No') . "</td>
                <td>" . ($order['pickup_notification_sent'] ? 'Yes' : 'No') . "</td>
                <td class='" . $eligibleClass . "'>" . $eligibleText . "</td>
              </tr>";
    }
    
    echo "</table>";
}

// Check email configuration
echo "</div><div class='section'><h2>4. Email Configuration</h2>";

// Check if PHPMailer is available
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "<p class='error'>❌ PHPMailer is not available</p>";
    echo "<p>Make sure you have installed PHPMailer in the vendor directory.</p>";
} else {
    echo "<p class='success'>✅ PHPMailer is available</p>";
    
    // Check email configuration
    echo "<div class='info'>
            <p><strong>Current Email Configuration:</strong></p>
            <p>SMTP Server: smtp.gmail.com</p>
            <p>SMTP Port: 587</p>
            <p>SMTP Username: sadikshyamunankarmi7@gmail.com</p>
            <p>SMTP Security: STARTTLS</p>
          </div>";
    
    echo "<p>If emails are not being sent, check:</p>
          <ol>
            <li>Make sure the SMTP password is correct</li>
            <li>Verify that the Gmail account has 'Less secure app access' enabled or is using an App Password</li>
            <li>Check if your server allows outgoing SMTP connections</li>
          </ol>";
}

// Manual notification test
echo "</div><div class='section'><h2>5. Manual Notification Test</h2>";
echo "<p>You can test sending notifications manually:</p>";

echo "<form method='post' action=''>
        <p>
            <label for='test_email'>Email address to test:</label>
            <input type='email' name='test_email' id='test_email' value='" . htmlspecialchars($_POST['test_email'] ?? '') . "' required>
        </p>
        <p>
            <label for='test_name'>Name:</label>
            <input type='text' name='test_name' id='test_name' value='" . htmlspecialchars($_POST['test_name'] ?? 'Test User') . "' required>
        </p>
        <p>
            <label for='notification_type'>Notification type:</label>
            <select name='notification_type' id='notification_type'>
                <option value='preparation'>Preparation Started</option>
                <option value='pickup'>Ready for Pickup</option>
            </select>
        </p>
        <p>
            <button type='submit' name='send_test_notification'>Send Test Notification</button>
        </p>
      </form>";

// Process test notification
if (isset($_POST['send_test_notification'])) {
    $testEmail = $_POST['test_email'] ?? '';
    $testName = $_POST['test_name'] ?? 'Test User';
    $notificationType = $_POST['notification_type'] ?? 'preparation';
    $testOrderId = 'TEST-' . rand(1000, 9999);
    $testPickupTime = date('Y-m-d H:i:s', strtotime('+30 minutes'));
    
    $testOrderDetails = [
        [
            'item_name' => 'Test Dish 1',
            'quantity' => 2,
            'price' => 15.99
        ],
        [
            'item_name' => 'Test Dish 2',
            'quantity' => 1,
            'price' => 12.50
        ]
    ];
    
    $result = false;
    
    if ($notificationType == 'preparation') {
        $result = sendOrderPreparationEmail($testEmail, $testName, $testOrderId, $testOrderDetails, $testPickupTime);
    } else {
        $result = sendOrderReadyEmail($testEmail, $testName, $testOrderId, $testOrderDetails, $testPickupTime);
    }
    
    if ($result) {
        echo "<p class='success'>✅ Test notification sent successfully to $testEmail</p>";
    } else {
        echo "<p class='error'>❌ Failed to send test notification</p>";
        echo "<p>Check the error log for more details.</p>";
    }
}

// Task scheduler check
echo "</div><div class='section'><h2>6. Task Scheduler Check</h2>";
echo "<p>Make sure your Windows Task Scheduler is set up correctly:</p>
      <ul>
        <li>Program/script: <code>C:\\xampp\\php\\php.exe</code></li>
        <li>Arguments: <code>C:\\xampp\\htdocs\\dashboard\\Kachuwafyp\\Development\\DineAmaze\\cron\\send_order_notifications.php</code></li>
        <li>Start in: <code>C:\\xampp\\htdocs\\dashboard\\Kachuwafyp\\Development\\DineAmaze</code></li>
        <li>Run whether user is logged on or not</li>
        <li>Run with highest privileges</li>
      </ul>";

// Logs check
echo "</div><div class='section'><h2>7. Log Files</h2>";
$logFile = dirname(__FILE__) . '/logs/notification_cron.log';

if (!file_exists($logFile)) {
    echo "<p class='warning'>⚠️ Log file does not exist yet: $logFile</p>";
} else {
    echo "<p class='success'>✅ Log file exists: $logFile</p>";
    
    // Display last 10 log entries
    $logContent = file_get_contents($logFile);
    $logLines = array_filter(explode("\n", $logContent));
    $lastLogs = array_slice($logLines, -10);
    
    echo "<p><strong>Last log entries:</strong></p>";
    echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto;'>";
    foreach ($lastLogs as $line) {
        echo htmlspecialchars($line) . "\n";
    }
    echo "</pre>";
}

// Close database connection
$conn->close();

echo "</div>
    </div>
</body>
</html>";
?>
