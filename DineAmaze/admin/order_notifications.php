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
    $orderId = $_POST['order_id'];
    $notificationType = $_POST['notification_type'];
    
    if (updateOrderStatusAndNotify($orderId, $notificationType)) {
        $message = '<div class="alert alert-success">Notification sent successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Error sending notification. Please try again.</div>';
    }
}

// Get all orders
$sql = "SELECT o.*, GROUP_CONCAT(DISTINCT o.status) as statuses, 
        MAX(o.order_date) as latest_order_date, 
        MAX(o.pickup_time) as pickup_time,
        c.full_name
        FROM takeout_order_items o
        LEFT JOIN takeout_customers c ON o.order_group_id = c.order_group_id
        GROUP BY o.order_group_id
        ORDER BY latest_order_date DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Notifications - DineAmaze Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: #f8f9fa;
            padding: 10px 15px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar a.active {
            background-color: #007bff;
        }
        .content {
            padding: 20px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .status-verified {
            background-color: #28a745;
            color: white;
        }
        .status-preparation {
            background-color: #17a2b8;
            color: white;
        }
        .status-ready {
            background-color: #007bff;
            color: white;
        }
        .status-cancelled {
            background-color: #dc3545;
            color: white;
        }
        .status-completed {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-white text-center mb-4">DineAmaze Admin</h4>
                <a href="index.php"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
                <a href="orders.php"><i class="fas fa-shopping-cart mr-2"></i> Orders</a>
                <a href="order_notifications.php" class="active"><i class="fas fa-bell mr-2"></i> Notifications</a>
                <a href="messages.php"><i class="fas fa-envelope mr-2"></i> Messages</a>
                <a href="users.php"><i class="fas fa-users mr-2"></i> Users</a>
                <a href="settings.php"><i class="fas fa-cog mr-2"></i> Settings</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 content">
                <h2 class="mb-4">Order Notifications</h2>
                
                <?php echo $message; ?>
                
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Manage Order Notifications</h5>
                    </div>
                    <div class="card-body">
                        <p>Use this page to manually send preparation and ready notifications to customers. The system will also automatically send these notifications based on the pickup time.</p>
                        <ul>
                            <li><strong>Preparation Notification:</strong> Sent 10 minutes before pickup time</li>
                            <li><strong>Ready Notification:</strong> Sent at the pickup time</li>
                        </ul>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Order Date</th>
                                <th>Pickup Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while ($order = $result->fetch_assoc()) {
                                    $orderId = $order['order_id'];
                                    $customerName = $order['full_name'] ?? 'N/A';
                                    $email = $order['email'];
                                    $orderDate = date('M d, Y - H:i A', strtotime($order['latest_order_date']));
                                    $pickupTime = !empty($order['pickup_time']) ? date('H:i A', strtotime($order['pickup_time'])) : 'Not specified';
                                    $status = $order['status'];
                                    
                                    $statusClass = '';
                                    switch ($status) {
                                        case 'pending':
                                            $statusClass = 'status-pending';
                                            break;
                                        case 'verified':
                                            $statusClass = 'status-verified';
                                            break;
                                        case 'preparation':
                                            $statusClass = 'status-preparation';
                                            break;
                                        case 'ready':
                                            $statusClass = 'status-ready';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'status-cancelled';
                                            break;
                                        case 'completed':
                                            $statusClass = 'status-completed';
                                            break;
                                        default:
                                            $statusClass = '';
                                    }
                                    
                                    echo '<tr>
                                        <td>' . $orderId . '</td>
                                        <td>' . htmlspecialchars($customerName) . '</td>
                                        <td>' . htmlspecialchars($email) . '</td>
                                        <td>' . $orderDate . '</td>
                                        <td>' . $pickupTime . '</td>
                                        <td><span class="status-badge ' . $statusClass . '">' . ucfirst($status) . '</span></td>
                                        <td>';
                                    
                                    // Only show notification buttons for pending, verified, or preparation orders
                                    if ($status == 'pending' || $status == 'verified') {
                                        echo '<form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="order_id" value="' . $orderId . '">
                                            <input type="hidden" name="notification_type" value="preparation">
                                            <button type="submit" name="send_notification" class="btn btn-info btn-sm">
                                                <i class="fas fa-utensils"></i> Send Preparation
                                            </button>
                                        </form> ';
                                    }
                                    
                                    if ($status == 'preparation' || ($status == 'verified' && !empty($order['pickup_time']))) {
                                        echo '<form method="POST" action="" class="d-inline ml-2">
                                            <input type="hidden" name="order_id" value="' . $orderId . '">
                                            <input type="hidden" name="notification_type" value="ready">
                                            <button type="submit" name="send_notification" class="btn btn-success btn-sm">
                                                <i class="fas fa-check-circle"></i> Send Ready
                                            </button>
                                        </form>';
                                    }
                                    
                                    echo '</td>
                                    </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="7" class="text-center">No orders found</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
