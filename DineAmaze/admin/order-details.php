<?php
// Start the session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$order_id = $_GET['id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get order details
$stmt = $conn->prepare("SELECT o.*, u.name, u.email, u.phone 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.user_id 
                        WHERE o.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: orders.php");
    exit;
}

$order = $result->fetch_assoc();
$stmt->close();

// Get order items
$stmt = $conn->prepare("SELECT oi.*, m.item_name, m.image_name 
                        FROM order_items oi 
                        JOIN menu_item m ON oi.item_id = m.item_id 
                        WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
$order_items = [];

while ($item = $items_result->fetch_assoc()) {
    $order_items[] = $item;
}
$stmt->close();

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        $success_message = "Order status updated successfully!";
        // Refresh order data
        $stmt = $conn->prepare("SELECT o.*, u.name, u.email, u.phone 
                                FROM orders o 
                                JOIN users u ON o.user_id = u.user_id 
                                WHERE o.order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
    } else {
        $error_message = "Error updating order status: " . $conn->error;
    }
    
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - DineAmaze Admin</title>
    <link rel="stylesheet" href="../css/admin/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .order-details {
                grid-template-columns: 1fr;
            }
        }
        
        .detail-card {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .detail-card h3 {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: #2c3e50;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .detail-label {
            font-weight: 500;
            width: 150px;
            color: #7f8c8d;
        }
        
        .detail-value {
            flex: 1;
        }
        
        .status-form {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .status-form select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        
        .status-form button {
            padding: 8px 15px;
            background-color: #6a5acd;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .status-form button:hover {
            background-color: #5a49c0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .items-table th, 
        .items-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .items-table th {
            background-color: #f9f9f9;
            font-weight: 600;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .item-customizations {
            font-size: 13px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
            color: #6a5acd;
            text-decoration: none;
        }
        
        .back-link i {
            margin-right: 5px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="dashboard-content">
                <a href="orders.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Orders</a>
                
                <div class="content-header">
                    <h1>Order #<?php echo $order['order_id']; ?> Details</h1>
                </div>
                
                <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>
                
                <div class="order-details">
                    <div class="detail-card">
                        <h3>Order Information</h3>
                        
                        <div class="detail-row">
                            <div class="detail-label">Order ID:</div>
                            <div class="detail-value">#<?php echo $order['order_id']; ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Date:</div>
                            <div class="detail-value"><?php echo date("d M Y H:i", strtotime($order['order_date'])); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value">
                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></span>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Total Amount:</div>
                            <div class="detail-value">Rs. <?php echo number_format($order['total_amount'], 2); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Payment Method:</div>
                            <div class="detail-value"><?php echo $order['payment_method']; ?></div>
                        </div>
                        
                        <form method="POST" action="" class="status-form">
                            <div class="detail-label">Update Status:</div>
                            <select name="status">
                                <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Processing" <?php echo $order['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="Completed" <?php echo $order['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                            <button type="submit">Update</button>
                        </form>
                    </div>
                    
                    <div class="detail-card">
                        <h3>Customer Information</h3>
                        
                        <div class="detail-row">
                            <div class="detail-label">Name:</div>
                            <div class="detail-value"><?php echo $order['name']; ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Email:</div>
                            <div class="detail-value"><?php echo $order['email']; ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Phone:</div>
                            <div class="detail-value"><?php echo $order['phone']; ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Delivery Address:</div>
                            <div class="detail-value"><?php echo $order['delivery_address']; ?></div>
                        </div>
                        
                        <?php if (!empty($order['special_instructions'])): ?>
                        <div class="detail-row">
                            <div class="detail-label">Special Instructions:</div>
                            <div class="detail-value"><?php echo $order['special_instructions']; ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="detail-card">
                    <h3>Order Items</h3>
                    
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td>
                                    <img src="../images/Menu Photos/<?php echo $item['image_name']; ?>" alt="<?php echo $item['item_name']; ?>" class="item-image">
                                </td>
                                <td>
                                    <?php echo $item['item_name']; ?>
                                    <?php if (!empty($item['customizations'])): ?>
                                    <div class="item-customizations">
                                        <?php echo $item['customizations']; ?>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                <td>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>