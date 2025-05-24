<?php
/**
 * Order notification functions for DineAmaze
 * Handles notifications for order preparation and ready-for-pickup
 */

// Include mail helper for sending email notifications
require_once 'mail_helper.php';

/**
 * Send notification when order goes into preparation
 * 
 * @param string $email User's email address
 * @param string $name User's name
 * @param int $orderId Order ID
 * @param array $orderDetails Order details
 * @param string $pickupTime Scheduled pickup time
 * @return bool Whether the notification was sent successfully
 */
function sendOrderPreparationEmail($email, $name, $orderId, $orderDetails, $pickupTime) {
    // Use the new email template function from mail_helper.php
    return sendOrderPreparationNotification($email, $name, $orderId, $orderDetails, $pickupTime);
}
/**
 * Send notification when order is ready for pickup (10 minutes before pickup time)
 * 
 * @param string $email User's email address
 * @param string $name User's name
 * @param int $orderId Order ID
 * @param array $orderDetails Order details
 * @param string $pickupTime Scheduled pickup time
 * @return bool Whether the notification was sent successfully
 */
function sendOrderReadyEmail($email, $name, $orderId, $orderDetails, $pickupTime) {
    // Use the new email template function from mail_helper.php
    return sendOrderReadyNotification($email, $name, $orderId, $orderDetails, $pickupTime);
}

/**
 * Check if an order needs notifications and send them if necessary
 * This function should be called by a cron job or scheduled task
 * 
 * @return void
 */
function checkAndSendOrderNotifications() {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "dineamaze_database");
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        return;
    }
    
    // Current time
    $currentTime = date('Y-m-d H:i:s');
    
    // Get orders that need preparation notification (orders that are confirmed but not yet in preparation)
    $prepSql = "SELECT o.*, c.full_name, c.email 
               FROM takeout_order_items o 
               JOIN takeout_customers c ON o.order_group_id = c.order_group_id 
               WHERE o.status = 'confirmed' 
               AND o.preparation_notification_sent = 0 
               AND o.pickup_time > '$currentTime'";
    
    $prepResult = $conn->query($prepSql);
    
    if ($prepResult && $prepResult->num_rows > 0) {
        while ($order = $prepResult->fetch_assoc()) {
            $orderId = $order['order_id'];
            $email = $order['email'];
            $name = $order['full_name'];
            $pickupTime = $order['pickup_time'];
            
            // Get order details
            $orderDetails = getOrderDetails($orderId, $conn);
            
            // Send preparation notification
            if (sendOrderPreparationEmail($email, $name, $orderId, $orderDetails, $pickupTime)) {
                // Update database to mark notification as sent
                $updateSql = "UPDATE takeout_order_items SET 
                              preparation_notification_sent = 1, 
                              status = 'preparation' 
                              WHERE order_id = '$orderId'";
                $conn->query($updateSql);
                
                error_log("Preparation notification sent for order #$orderId");
            }
        }
    }
    
    // Get orders that need pickup notification (10 minutes before pickup time)
    $tenMinutesFromNow = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    $pickupSql = "SELECT o.*, c.full_name, c.email 
                 FROM takeout_order_items o 
                 JOIN takeout_customers c ON o.order_group_id = c.order_group_id 
                 WHERE o.status = 'preparation' 
                 AND o.pickup_notification_sent = 0 
                 AND o.pickup_time <= '$tenMinutesFromNow' 
                 AND o.pickup_time > '$currentTime'";
    
    $pickupResult = $conn->query($pickupSql);
    
    if ($pickupResult && $pickupResult->num_rows > 0) {
        while ($order = $pickupResult->fetch_assoc()) {
            $orderId = $order['order_id'];
            $email = $order['email'];
            $name = $order['full_name'];
            $pickupTime = $order['pickup_time'];
            
            // Get order details
            $orderDetails = getOrderDetails($orderId, $conn);
            
            // Send pickup notification
            if (sendOrderReadyEmail($email, $name, $orderId, $orderDetails, $pickupTime)) {
                // Update database to mark notification as sent
                $updateSql = "UPDATE takeout_order_items SET 
                              pickup_notification_sent = 1, 
                              status = 'ready' 
                              WHERE order_id = '$orderId'";
                $conn->query($updateSql);
                
                error_log("Pickup notification sent for order #$orderId");
            }
        }
    }
    
    $conn->close();
}

/**
 * Get detailed order information for notifications
 * 
 * @param int $orderId Order ID
 * @param mysqli $conn Database connection
 * @return array Order details
 */
function getOrderDetails($orderId, $conn) {
    // Get order items
    $sql = "SELECT * FROM takeout_order_items WHERE order_id = '$orderId'";
    $result = $conn->query($sql);
    
    $orderDetails = [];
    
    if ($result && $result->num_rows > 0) {
        while ($item = $result->fetch_assoc()) {
            // Use the item details directly from the takeout_order_items table
            $orderDetails[] = [
                'item_name' => $item['item_name'] ?? 'Menu Item',
                'quantity' => $item['quantity'] ?? 1,
                'price' => $item['price'] ?? 0
            ];
        }
    }
    
    // If no items found, create a default item
    if (empty($orderDetails)) {
        $orderDetails[] = [
            'item_name' => 'Order #' . $orderId,
            'quantity' => 1,
            'price' => 0
        ];
    }
    
    return $orderDetails;
}

/**
 * Update order status and send appropriate notifications manually from admin panel
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
    $sql = "SELECT o.*, c.full_name, c.email, c.phone 
           FROM takeout_order_items o
           JOIN takeout_customers c ON o.order_group_id = c.order_group_id
           WHERE o.order_id = '$orderId'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $email = $order['email'];
        $name = $order['full_name'];
        $pickupTime = $order['pickup_time'];
        
        // Get order items
        $orderDetails = getOrderDetails($orderId, $conn);
        
        // Update order status
        $newStatus = ($status == 'preparation') ? 'preparation' : 'ready';
        $updateSql = "UPDATE takeout_order_items SET status = '$newStatus' WHERE order_id = '$orderId'";
        
        if ($conn->query($updateSql)) {
            // Send appropriate notification
            $success = false;
            
            if ($status == 'preparation') {
                // Mark as sent
                $conn->query("UPDATE takeout_order_items SET preparation_notification_sent = 1 WHERE order_id = '$orderId'");
                // Send notification
                $success = sendOrderPreparationEmail($email, $name, $orderId, $orderDetails, $pickupTime);
            } else if ($status == 'ready') {
                // Mark as sent
                $conn->query("UPDATE takeout_order_items SET pickup_notification_sent = 1 WHERE order_id = '$orderId'");
                // Send notification
                $success = sendOrderReadyEmail($email, $name, $orderId, $orderDetails, $pickupTime);
            }
            
            $conn->close();
            return $success;
        }
    }
    
    $conn->close();
    return false;
}
?>
