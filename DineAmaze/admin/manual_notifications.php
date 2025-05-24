<?php
// Start the session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Include order notifications helper
require_once '../includes/order_notifications.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle notification sending
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_notification'])) {
    $orderGroupId = $_POST['order_group_id'];
    $notificationType = $_POST['notification_type'];
    
    // Get order details
    $sql = "SELECT o.*, c.full_name, c.email 
            FROM takeout_order_items o
            JOIN takeout_customers c ON o.order_group_id = c.order_group_id
            WHERE o.order_group_id = ?
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $orderGroupId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $email = $order['email'];
        $name = $order['full_name'];
        $pickupTime = $order['pickup_time'];
        $orderId = $order['order_id']; // Get a single order ID for the notification
        
        // Get all items in this order group
        $orderDetailsSql = "SELECT * FROM takeout_order_items WHERE order_group_id = ?";
        $detailsStmt = $conn->prepare($orderDetailsSql);
        $detailsStmt->bind_param("s", $orderGroupId);
        $detailsStmt->execute();
        $detailsResult = $detailsStmt->get_result();
        
        $orderDetails = [];
        if ($detailsResult && $detailsResult->num_rows > 0) {
            while ($item = $detailsResult->fetch_assoc()) {
                $orderDetails[] = [
                    'item_name' => $item['item_name'] ?? 'Menu Item',
                    'quantity' => $item['quantity'] ?? 1,
                    'price' => $item['price'] ?? 0
                ];
            }
        }
        $detailsStmt->close();
        
        $success = false;
        
        if ($notificationType == 'preparation') {
            // Send preparation notification
            $success = sendOrderPreparationEmail($email, $name, $orderGroupId, $orderDetails, $pickupTime);
            
            if ($success) {
                // Update order status for all items in this group
                $updateSql = "UPDATE takeout_order_items SET 
                              status = 'preparation',
                              preparation_notification_sent = 1 
                              WHERE order_group_id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("s", $orderGroupId);
                $updateStmt->execute();
                $updateStmt->close();
            }
        } else if ($notificationType == 'pickup') {
            // Send pickup notification
            $success = sendOrderReadyEmail($email, $name, $orderGroupId, $orderDetails, $pickupTime);
            
            if ($success) {
                // Update order status for all items in this group
                $updateSql = "UPDATE takeout_order_items SET 
                              status = 'ready',
                              pickup_notification_sent = 1 
                              WHERE order_group_id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("s", $orderGroupId);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }
        
        if ($success) {
            $message = '<div class="alert alert-success">Notification sent successfully to ' . htmlspecialchars($email) . '!</div>';
        } else {
            $message = '<div class="alert alert-danger">Error sending notification. Please check the logs.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Order not found.</div>';
    }
    
    $stmt->close();
}

// First, check if the required columns exist and add them if they don't
$checkColumns = $conn->query("SHOW COLUMNS FROM takeout_order_items LIKE 'preparation_notification_sent'");
if ($checkColumns->num_rows == 0) {
    $message .= '<div class="alert alert-warning">The column "preparation_notification_sent" does not exist. Running database update...</div>';
    
    // Add the columns
    $addColumnSql = "ALTER TABLE takeout_order_items 
                     ADD COLUMN preparation_notification_sent TINYINT(1) NOT NULL DEFAULT 0,
                     ADD COLUMN pickup_notification_sent TINYINT(1) NOT NULL DEFAULT 0";
    if ($conn->query($addColumnSql)) {
        $message .= '<div class="alert alert-success">Database updated successfully!</div>';
    } else {
        $message .= '<div class="alert alert-danger">Error updating database: ' . $conn->error . '</div>';
    }
}

// Get all orders - Group by order_group_id to show unique orders
$sql = "SELECT t.order_group_id, MIN(t.order_id) as first_order_id, 
        t.status, t.pickup_time, t.email, t.order_date,
        MAX(IFNULL(t.preparation_notification_sent, 0)) as preparation_notification_sent,
        MAX(IFNULL(t.pickup_notification_sent, 0)) as pickup_notification_sent,
        c.full_name, SUM(t.price * t.quantity) as total_amount
        FROM takeout_order_items t
        LEFT JOIN takeout_customers c ON t.order_group_id = c.order_group_id
        GROUP BY t.order_group_id, t.status, t.pickup_time, t.email, t.order_date, c.full_name
        ORDER BY t.pickup_time DESC";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $message .= '<div class="alert alert-danger">Error preparing query: ' . $conn->error . '</div>';
} else {
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Notifications - DineAmaze Admin</title>
    <link rel="stylesheet" href="../css/admin/style.css">
    <link rel="stylesheet" href="../css/admin/sidebar.css">
    <link rel="stylesheet" href="../css/admin/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .badge-preparation {
            background-color: #fd7e14;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-ready {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-confirmed {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-cancelled {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-completed {
            background-color: #6c757d;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        .card-body {
            padding: 20px;
        }
        .bg-primary {
            background-color: #764ba2 !important;
        }
        .text-white {
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Include Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <!-- Include Header -->
            <?php include 'includes/header.php'; ?>
            
            <div class="dashboard-content">
                <h2 class="mb-4">Manual Order Notifications</h2>
                
                <?php echo $message; ?>
                
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>About Manual Notifications</h5>
                    </div>
                    <div class="card-body">
                        <p>This page allows you to manually send email notifications to customers about their orders.</p>
                        <p><strong>Notification Types:</strong></p>
                        <ul>
                            <li><strong>Preparation Started</strong> - Notifies the customer that their order is being prepared</li>
                            <li><strong>Ready for Pickup</strong> - Notifies the customer that their order will be ready for pickup soon</li>
                        </ul>
                        <p><strong>Note:</strong> Notifications are also sent automatically based on order status and pickup time.</p>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Pickup Time</th>
                                <th>Amount</th>
                                <th>Notifications Sent</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($orders) && count($orders) > 0): ?>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['order_group_id']; ?></td>
                                    <td><?php echo !empty($order['full_name']) ? htmlspecialchars($order['full_name']) : 'N/A'; ?></td>
                                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                                    <td>
                                        <?php 
                                        $statusClass = 'status-' . strtolower($order['status']);
                                        echo '<span class="status-badge ' . $statusClass . '">' . ucfirst($order['status']) . '</span>';
                                        ?>
                                    </td>
                                    <td><?php echo date("d M Y H:i", strtotime($order['pickup_time'])); ?></td>
                                    <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <?php
                                        $notificationsSent = [];
                                        if ($order['preparation_notification_sent']) {
                                            $notificationsSent[] = 'Preparation';
                                        }
                                        if ($order['pickup_notification_sent']) {
                                            $notificationsSent[] = 'Pickup';
                                        }
                                        echo !empty($notificationsSent) ? implode(', ', $notificationsSent) : 'None';
                                        ?>
                                    </td>
                                    <td>
                                        <form method="post" action="">
                                            <input type="hidden" name="order_group_id" value="<?php echo $order['order_group_id']; ?>">
                                            <select name="notification_type" class="notification-select">
                                                <option value="preparation">Preparation Started</option>
                                                <option value="pickup">Ready for Pickup</option>
                                            </select>
                                            <button type="submit" name="send_notification" class="send-btn">
                                                <i class="fas fa-paper-plane"></i> Send
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="no-data">
                                        <div style="padding: 20px;">
                                            <i class="fas fa-info-circle" style="font-size: 24px; color: #6c757d; margin-bottom: 10px;"></i>
                                            <p><strong>No orders found in the system</strong></p>
                                            <p>When customers place takeout orders, they will appear here for you to manage notifications.</p>
                                            <p>You can send two types of notifications to customers:</p>
                                            <ul style="text-align: left; display: inline-block;">
                                                <li>Preparation Started - When the kitchen begins preparing the order</li>
                                                <li>Ready for Pickup - When the order will be ready in about 10 minutes</li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the sidebar and toggle button
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.querySelector('.sidebar-toggle');
            
            // Add click event to toggle button
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                });
            }
        });
    </script>
</body>
</html>

