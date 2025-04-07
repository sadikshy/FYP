<?php
// Start the session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE takeout_order_items SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        $success_message = "Order status updated successfully!";
    } else {
        $error_message = "Error updating order status: " . $conn->error;
    }
    
    $stmt->close();
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build query with filters - Group by order_group_id to show unique orders
$sql = "SELECT order_group_id, MIN(order_id) as first_order_id, email, 
        order_date, SUM(price * quantity) as total_amount, 
        CASE 
            WHEN COUNT(DISTINCT status) > 1 THEN 'mixed' 
            ELSE MAX(status) 
        END as status 
        FROM takeout_order_items 
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($status_filter)) {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($date_from)) {
    $sql .= " AND order_date >= ?";
    $params[] = $date_from . " 00:00:00";
    $types .= "s";
}

if (!empty($date_to)) {
    $sql .= " AND order_date <= ?";
    $params[] = $date_to . " 23:59:59";
    $types .= "s";
}

$sql .= " GROUP BY order_group_id, email, order_date ORDER BY order_date DESC";

// Prepare and execute the query
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$orders = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - DineAmaze Admin</title>
    <link rel="stylesheet" href="../css/admin/style.css">
    <link rel="stylesheet" href="../css/admin/sidebar.css">
    <link rel="stylesheet" href="../css/admin/header.css">
    <link rel="stylesheet" href="../css/admin/orders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="dashboard-content">
                <div class="content-header">
                    <h1>Manage Orders</h1>
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
                
                <div class="filter-form">
                    <h3>Filter Orders</h3>
                    <form method="GET" action="">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="filter-control">
                                    <option value="">All Statuses</option>
                                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="verified" <?php echo $status_filter == 'verified' ? 'selected' : ''; ?>>Verified</option>
                                    <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="date_from">Date From</label>
                                <input type="date" id="date_from" name="date_from" class="filter-control" value="<?php echo $date_from; ?>">
                            </div>
                            
                            <div class="filter-group">
                                <label for="date_to">Date To</label>
                                <input type="date" id="date_to" name="date_to" class="filter-control" value="<?php echo $date_to; ?>">
                            </div>
                            
                            <div class="filter-buttons">
                                <button type="submit" class="filter-btn">Filter</button>
                                <a href="orders.php" class="reset-btn">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($orders) > 0): ?>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['order_group_id']; ?></td>
                                    <td><?php echo $order['email']; ?></td>
                                    <td><?php echo date("d M Y H:i", strtotime($order['order_date'])); ?></td>
                                    <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <?php 
                                        $statusClass = 'status-' . strtolower($order['status']);
                                        echo '<span class="status-badge ' . $statusClass . '">' . ucfirst($order['status']) . '</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <a href="order-details.php?group_id=<?php echo $order['order_group_id']; ?>" class="view-btn">View Details</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="no-data">No orders found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>