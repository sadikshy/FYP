<?php
/**
 * Order notification functions for DineAmaze
 * Handles notifications for order preparation and ready-for-pickup
 */

// Include mail helper for sending email notifications
require_once 'mail_helper.php';

/**
 * Send notification when order goes into preparation (10 minutes before pickup time)
 * 
 * @param string $email User's email address
 * @param string $name User's name
 * @param int $orderId Order ID
 * @param array $orderDetails Order details
 * @return bool Whether the notification was sent successfully
 */
function sendOrderPreparationNotification($email, $name, $orderId, $orderDetails) {
    $subject = "DineAmaze: Your Order #$orderId is Being Prepared";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #FF6B00; color: white; padding: 15px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
            .button { display: inline-block; background-color: #FF6B00; color: white; padding: 10px 20px; 
                     text-decoration: none; border-radius: 5px; margin-top: 15px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Your Order is Being Prepared!</h2>
            </div>
            <div class='content'>
                <p>Hello $name,</p>
                <p>Great news! Your order #$orderId has gone into preparation and will be ready for pickup soon.</p>
                <p><strong>Order Details:</strong></p>
                <ul>";
    
    // Add order items to the message
    if (isset($orderDetails['items'])) {
        foreach ($orderDetails['items'] as $item) {
            $itemName = isset($item['name']) ? $item['name'] : $item['item_name'];
            $quantity = $item['quantity'];
            $price = isset($item['price']) ? $item['price'] : 0;
            
            $message .= "<li>$itemName x $quantity - Rs. " . number_format($price, 2) . "</li>";
        }
    } else {
        $message .= "<li>" . $orderDetails['item_name'] . " x " . $orderDetails['quantity'] . " - Rs. " . 
                   number_format($orderDetails['price'], 2) . "</li>";
    }
    
    $pickupTime = isset($orderDetails['pickup_time']) ? date('H:i A', strtotime($orderDetails['pickup_time'])) : 'Not specified';
    
    $message .= "
                </ul>
                <p><strong>Pickup Time:</strong> $pickupTime</p>
                <p>We're working on your order right now! It will be ready for pickup at the scheduled time.</p>
                <p>Thank you for choosing DineAmaze!</p>
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " DineAmaze. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // For development environment, save notification to a file instead of sending email
    $notificationsDir = dirname(__DIR__) . '/notifications';
    
    // Create notifications directory if it doesn't exist
    if (!file_exists($notificationsDir)) {
        mkdir($notificationsDir, 0777, true);
    }
    
    // Create a unique filename for this notification
    $filename = $notificationsDir . '/preparation_notification_' . $orderId . '_' . time() . '.html';
    
    // Add email headers to the message for completeness
    $fullMessage = "To: $email\nFrom: DineAmaze Restaurant <noreply@dineamaze.com>\nSubject: $subject\n\n$message";
    
    // Save the notification to a file
    $saved = file_put_contents($filename, $fullMessage);
    
    // Log the result
    if ($saved !== false) {
        error_log("Preparation notification saved to file for order #$orderId");
        return true;
    } else {
        error_log("Failed to save preparation notification for order #$orderId");
        return false;
    }
}

/**
 * Send notification when order is ready for pickup
 * 
 * @param string $email User's email address
 * @param string $name User's name
 * @param int $orderId Order ID
 * @param array $orderDetails Order details
 * @return bool Whether the notification was sent successfully
 */
function sendOrderReadyNotification($email, $name, $orderId, $orderDetails) {
    $subject = "DineAmaze: Your Order #$orderId is Ready for Pickup!";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #4CAF50; color: white; padding: 15px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
            .button { display: inline-block; background-color: #4CAF50; color: white; padding: 10px 20px; 
                     text-decoration: none; border-radius: 5px; margin-top: 15px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Your Order is Ready for Pickup!</h2>
            </div>
            <div class='content'>
                <p>Hello $name,</p>
                <p><strong>Your order #$orderId is now ready for pickup!</strong></p>
                <p><strong>Order Details:</strong></p>
                <ul>";
    
    // Add order items to the message
    if (isset($orderDetails['items'])) {
        foreach ($orderDetails['items'] as $item) {
            $itemName = isset($item['name']) ? $item['name'] : $item['item_name'];
            $quantity = $item['quantity'];
            $price = isset($item['price']) ? $item['price'] : 0;
            
            $message .= "<li>$itemName x $quantity - Rs. " . number_format($price, 2) . "</li>";
        }
    } else {
        $message .= "<li>" . $orderDetails['item_name'] . " x " . $orderDetails['quantity'] . " - Rs. " . 
                   number_format($orderDetails['price'], 2) . "</li>";
    }
    
    $pickupTime = isset($orderDetails['pickup_time']) ? date('H:i A', strtotime($orderDetails['pickup_time'])) : 'Not specified';
    
    $message .= "
                </ul>
                <p><strong>Pickup Time:</strong> $pickupTime</p>
                <p>Your delicious food is ready and waiting for you! Please come to our restaurant to pick up your order.</p>
                <p>Thank you for choosing DineAmaze!</p>
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " DineAmaze. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // For development environment, save notification to a file instead of sending email
    $notificationsDir = dirname(__DIR__) . '/notifications';
    
    // Create notifications directory if it doesn't exist
    if (!file_exists($notificationsDir)) {
        mkdir($notificationsDir, 0777, true);
    }
    
    // Create a unique filename for this notification
    $filename = $notificationsDir . '/preparation_notification_' . $orderId . '_' . time() . '.html';
    
    // Add email headers to the message for completeness
    $fullMessage = "To: $email\nFrom: DineAmaze Restaurant <noreply@dineamaze.com>\nSubject: $subject\n\n$message";
    
    // Save the notification to a file
    $saved = file_put_contents($filename, $fullMessage);
    
    // Log the result
    if ($saved !== false) {
        error_log("Preparation notification saved to file for order #$orderId");
        return true;
    } else {
        error_log("Failed to save preparation notification for order #$orderId");
        return false;
    }
}

/**
 * Update order status and send appropriate notifications
 * 
 * @param int $orderId Order ID
 * @param string $status New status ('preparation' or 'ready')
 * @return bool Whether the update was successful
 */
function updateOrderStatusAndNotify($orderId, $status) {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "dineamaze_database");
    if ($conn->connect_error) {
        return false;
    }
    
    // Get order details
    $sql = "SELECT * FROM takeout_order_items WHERE order_id = '$orderId'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $email = $order['email'];
        
        // Get user's name
        $userSql = "SELECT name FROM user WHERE email = '$email'";
        $userResult = $conn->query($userSql);
        $name = ($userResult && $userResult->num_rows > 0) ? $userResult->fetch_assoc()['name'] : 'Customer';
        
        // Update order status
        $newStatus = ($status == 'preparation') ? 'preparation' : 'ready';
        $updateSql = "UPDATE takeout_order_items SET status = '$newStatus' WHERE order_id = '$orderId'";
        
        if ($conn->query($updateSql)) {
            // Send appropriate notification
            if ($status == 'preparation') {
                // Add preparation_notification_sent column if it doesn't exist
                $checkColumnSql = "SHOW COLUMNS FROM takeout_order_items LIKE 'preparation_notification_sent'";
                $columnResult = $conn->query($checkColumnSql);
                
                if ($columnResult->num_rows == 0) {
                    $addColumnSql = "ALTER TABLE takeout_order_items ADD COLUMN preparation_notification_sent TINYINT(1) DEFAULT 0";
                    $conn->query($addColumnSql);
                }
                
                // Update notification status and send notification
                $updateNotifSql = "UPDATE takeout_order_items SET preparation_notification_sent = 1 WHERE order_id = '$orderId'";
                $conn->query($updateNotifSql);
                
                sendOrderPreparationNotification($email, $name, $orderId, $order);
            } else if ($status == 'ready') {
                // Add ready_notification_sent column if it doesn't exist
                $checkColumnSql = "SHOW COLUMNS FROM takeout_order_items LIKE 'ready_notification_sent'";
                $columnResult = $conn->query($checkColumnSql);
                
                if ($columnResult->num_rows == 0) {
                    $addColumnSql = "ALTER TABLE takeout_order_items ADD COLUMN ready_notification_sent TINYINT(1) DEFAULT 0";
                    $conn->query($addColumnSql);
                }
                
                // Update notification status and send notification
                $updateNotifSql = "UPDATE takeout_order_items SET ready_notification_sent = 1 WHERE order_id = '$orderId'";
                $conn->query($updateNotifSql);
                
                sendOrderReadyNotification($email, $name, $orderId, $order);
            }
            
            $conn->close();
            return true;
        }
    }
    
    $conn->close();
    return false;
}
?>
