<?php
/**
 * Notification system for DineAmaze
 * Supports both file-based notifications (for development) and email notifications (for production)
 */

// Set this to true to use file-based notifications, false to use real emails
define('USE_FILE_NOTIFICATIONS', true);

// Email configuration
define('SMTP_FROM_EMAIL', 'sadikshyamunankarmi7@gmail.com');
define('SMTP_FROM_NAME', 'DineAmaze Restaurant');

// Base URL for images (change this to your actual domain in production)
define('BASE_URL', 'http://localhost/dashboard/Kachuwafyp/Development/DineAmaze');

/**
 * Send preparation notification (10 minutes before pickup)
 * 
 * @param string $email User's email address
 * @param string $name User's name
 * @param int $orderId Order ID
 * @param array $orderDetails Order details
 * @return bool Whether the notification was saved successfully
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
            $imageUrl = '';
            
            // Get image URL if available
            if (isset($item['image']) && !empty($item['image'])) {
                // Convert relative path to absolute URL
                if (strpos($item['image'], 'http') !== 0) {
                    $imageUrl = BASE_URL . '/' . ltrim($item['image'], '/');
                } else {
                    $imageUrl = $item['image'];
                }
                $message .= "<li><img src=\"$imageUrl\" alt=\"$itemName\" style=\"width:50px; height:auto;\"> $itemName x $quantity - Rs. " . number_format($price, 2) . "</li>";
            } else {
                $message .= "<li>$itemName x $quantity - Rs. " . number_format($price, 2) . "</li>";
            }
        }
    } else {
        $itemName = $orderDetails['item_name'];
        $imageUrl = '';
        
        // Get image URL if available
        if (isset($orderDetails['image']) && !empty($orderDetails['image'])) {
            // Convert relative path to absolute URL
            if (strpos($orderDetails['image'], 'http') !== 0) {
                $imageUrl = BASE_URL . '/' . ltrim($orderDetails['image'], '/');
            } else {
                $imageUrl = $orderDetails['image'];
            }
            $message .= "<li><img src=\"$imageUrl\" alt=\"$itemName\" style=\"width:50px; height:auto;\"> $itemName x " . $orderDetails['quantity'] . " - Rs. " . 
                       number_format($orderDetails['price'], 2) . "</li>";
        } else {
            $message .= "<li>$itemName x " . $orderDetails['quantity'] . " - Rs. " . 
                       number_format($orderDetails['price'], 2) . "</li>";
        }
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
    
    // Decide whether to use file-based or email-based notifications
    if (USE_FILE_NOTIFICATIONS) {
        return saveNotificationToFile($email, $name, $subject, $message, $orderId, 'preparation');
    } else {
        return sendEmail($email, $name, $subject, $message, $orderId, 'preparation');
    }
}

/**
 * Send ready notification (at pickup time)
 * 
 * @param string $email User's email address
 * @param string $name User's name
 * @param int $orderId Order ID
 * @param array $orderDetails Order details
 * @return bool Whether the notification was saved successfully
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
            $imageUrl = '';
            
            // Get image URL if available
            if (isset($item['image']) && !empty($item['image'])) {
                // Convert relative path to absolute URL
                if (strpos($item['image'], 'http') !== 0) {
                    $imageUrl = BASE_URL . '/' . ltrim($item['image'], '/');
                } else {
                    $imageUrl = $item['image'];
                }
                $message .= "<li><img src=\"$imageUrl\" alt=\"$itemName\" style=\"width:50px; height:auto;\"> $itemName x $quantity - Rs. " . number_format($price, 2) . "</li>";
            } else {
                $message .= "<li>$itemName x $quantity - Rs. " . number_format($price, 2) . "</li>";
            }
        }
    } else {
        $itemName = $orderDetails['item_name'];
        $imageUrl = '';
        
        // Get image URL if available
        if (isset($orderDetails['image']) && !empty($orderDetails['image'])) {
            // Convert relative path to absolute URL
            if (strpos($orderDetails['image'], 'http') !== 0) {
                $imageUrl = BASE_URL . '/' . ltrim($orderDetails['image'], '/');
            } else {
                $imageUrl = $orderDetails['image'];
            }
            $message .= "<li><img src=\"$imageUrl\" alt=\"$itemName\" style=\"width:50px; height:auto;\"> $itemName x " . $orderDetails['quantity'] . " - Rs. " . 
                       number_format($orderDetails['price'], 2) . "</li>";
        } else {
            $message .= "<li>$itemName x " . $orderDetails['quantity'] . " - Rs. " . 
                       number_format($orderDetails['price'], 2) . "</li>";
        }
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
    
    // Decide whether to use file-based or email-based notifications
    if (USE_FILE_NOTIFICATIONS) {
        return saveNotificationToFile($email, $name, $subject, $message, $orderId, 'ready');
    } else {
        return sendEmail($email, $name, $subject, $message, $orderId, 'ready');
    }
}

/**
 * Save notification to a file
 * 
 * @param string $email Recipient email
 * @param string $name Recipient name
 * @param string $subject Email subject
 * @param string $message Email message
 * @param int $orderId Order ID
 * @param string $type Notification type ('preparation' or 'ready')
 * @return bool Whether the notification was saved successfully
 */
function saveNotificationToFile($email, $name, $subject, $message, $orderId, $type) {
    // Create notifications directory
    $notificationsDir = dirname(__DIR__) . '/notifications';
    
    if (!file_exists($notificationsDir)) {
        mkdir($notificationsDir, 0777, true);
    }
    
    // Create a unique filename for this notification
    $filename = $notificationsDir . '/' . $type . '_notification_' . $orderId . '_' . time() . '.html';
    
    // Add email headers to the message for completeness
    $fullMessage = "To: $email ($name)\nFrom: DineAmaze Restaurant <noreply@dineamaze.com>\nSubject: $subject\nDate: " . date('r') . "\n\n$message";
    
    // Save the notification to a file
    $saved = file_put_contents($filename, $fullMessage);
    
    // Log the result
    if ($saved !== false) {
        error_log("$type notification saved to file for order #$orderId");
        return true;
    } else {
        error_log("Failed to save $type notification for order #$orderId");
        return false;
    }
}

/**
 * Send email notification
 * 
 * @param string $email Recipient email
 * @param string $name Recipient name
 * @param string $subject Email subject
 * @param string $message Email message
 * @param int $orderId Order ID
 * @param string $type Notification type ('preparation' or 'ready')
 * @return bool Whether the email was sent successfully
 */
function sendEmail($email, $name, $subject, $message, $orderId, $type) {
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">" . "\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send email using PHP's mail function
    $mailSent = mail($email, $subject, $message, $headers);
    
    // Log the result
    if ($mailSent) {
        error_log("$type email notification sent to $email for order #$orderId");
        return true;
    } else {
        error_log("Failed to send $type email notification to $email for order #$orderId");
        return false;
    }
}
?>
