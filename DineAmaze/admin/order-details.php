<?php
// Start the session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Add debugging code
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug the incoming parameters
echo "<pre>";
echo "GET parameters: ";
print_r($_GET);
echo "</pre>";

// Check if order ID is provided
if (!isset($_GET['id']) && !isset($_GET['group_id'])) {
    header("Location: orders.php");
    exit;
}

// Define base URL for assets
define('BASE_URL', 'http://localhost/dashboard/Kachuwafyp/Development/DineAmaze/');

// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get order details
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT t.*, u.name, u.email, u.phone_number as phone 
                            FROM takeout_order_items t 
                            LEFT JOIN user u ON t.email = u.email 
                            WHERE t.order_id = ? LIMIT 1");
    $stmt->bind_param("i", $order_id);
} else {
    $group_id = $_GET['group_id'];
    $stmt = $conn->prepare("SELECT t.*, u.name, u.email, u.phone_number as phone 
                            FROM takeout_order_items t 
                            LEFT JOIN user u ON t.email = u.email 
                            WHERE t.order_group_id = ? ORDER BY t.order_id LIMIT 1");
    $stmt->bind_param("s", $group_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: orders.php");
    exit;
}

$order = $result->fetch_assoc();
$stmt->close();

// Get order items
if (isset($_GET['id'])) {
    $group_id = $order['order_group_id'];
} else {
    $group_id = $_GET['group_id'];
}

$stmt = $conn->prepare("SELECT t.*, m.item_name as menu_item_name, m.image_name 
                        FROM takeout_order_items t 
                        LEFT JOIN menu_item m ON t.item_name = m.item_name 
                        WHERE t.order_group_id = ?");
$stmt->bind_param("s", $group_id);
$stmt->execute();
$items_result = $stmt->get_result();
$order_items = [];

while ($item = $items_result->fetch_assoc()) {
    $order_items[] = $item;
}
$stmt->close();

// Check if there are any ID documents for this order
$id_documents = [];
$stmt = $conn->prepare("SELECT * FROM id_documents WHERE order_group_id = ? OR order_id = ? OR order_id IN (SELECT order_id FROM takeout_order_items WHERE order_group_id = ?)");
$stmt->bind_param("sis", $group_id, $order['order_id'], $group_id);
$stmt->execute();
$docs_result = $stmt->get_result();

// Debug the documents query
echo "<pre style='display:none;'>";
echo "Documents query for group_id: " . $group_id . " and order_id: " . $order['order_id'] . "\n";
echo "Found " . $docs_result->num_rows . " documents\n";
echo "</pre>";

while ($doc = $docs_result->fetch_assoc()) {
    $id_documents[] = $doc;
}
$stmt->close();

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    
    // Validate status to match enum values in database
    $valid_statuses = ['pending', 'verified', 'completed'];
    if (!in_array($status, $valid_statuses)) {
        $status = 'pending';
    }
    
    // Update only the status field
    $stmt = $conn->prepare("UPDATE takeout_order_items SET status = ? WHERE order_group_id = ?");
    $stmt->bind_param("ss", $status, $group_id);
    
    if ($stmt->execute()) {
        $success_message = "Order status updated successfully!";
        
        // If status is changed to verified, update user verification status
        if ($status == 'verified' && !empty($order['email'])) {
            // Check if user exists
            $check_user = $conn->prepare("SELECT * FROM user WHERE email = ?");
            $check_user->bind_param("s", $order['email']);
            $check_user->execute();
            $user_result = $check_user->get_result();
            
            if ($user_result->num_rows > 0) {
                // Update user verification status
                $update_user = $conn->prepare("UPDATE user SET is_verified = 1 WHERE email = ?");
                $update_user->bind_param("s", $order['email']);
                $update_user->execute();
                $update_user->close();
                
                $success_message .= " User has been verified for future orders.";
            }
            $check_user->close();
        }
        
        // Refresh order data
        $stmt = $conn->prepare("SELECT t.*, u.name, u.email, u.phone_number as phone 
                                FROM takeout_order_items t 
                                LEFT JOIN user u ON t.email = u.email 
                                WHERE t.order_group_id = ? ORDER BY t.order_id LIMIT 1");
        $stmt->bind_param("s", $group_id);
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
    <link rel="stylesheet" href="../css/admin/sidebar.css">
    <link rel="stylesheet" href="../css/admin/header.css">
    <link rel="stylesheet" href="../css/admin/orders.css">
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
        
        /* Document gallery styling */
        .document-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }
        
        .document-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .document-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .document-image {
            width: 100%;
            height: 300px;
            object-fit: contain;
            background-color: #f8f8f8;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }
        
        .document-info {
            padding: 12px;
            background-color: #f9f9f9;
            font-size: 13px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .zoom-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .zoom-btn:hover {
            background-color: #2980b9;
        }
        
        /* Fullscreen image modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
            overflow: auto;
        }
        
        .modal-content {
            margin: auto;
            display: block;
            max-width: 95%;
            max-height: 95%;
            margin-top: 2%;
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }
        
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }
        
        .close:hover {
            color: #bbb;
        }
        
        /* Status badge styling */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: #f39c12;
            color: #fff;
        }
        
        .status-verified {
            background-color: #3498db;
            color: #fff;
        }
        
        .status-completed {
            background-color: #2ecc71;
            color: #fff;
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
                        
                        <?php
                        $total_amount = 0;
                        foreach ($order_items as $item) {
                            $total_amount += $item['price'] * $item['quantity'];
                        }?>
                        
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
                            <div class="detail-value">Rs. <?php echo number_format($total_amount, 2); ?></div>
                        </div>
                        
                       
                        
                        <form method="POST" action="" class="status-form">
                            <div class="detail-label">Update Status:</div>
                            <select name="status">
                                <option value="pending" <?php echo strtolower($order['status']) == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="verified" <?php echo strtolower($order['status']) == 'verified' ? 'selected' : ''; ?>>Verified</option>
                                <option value="completed" <?php echo strtolower($order['status']) == 'completed' ? 'selected' : ''; ?>>Completed</option>
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
                                    <?php if (!empty($item['image_name'])): ?>
                                        <img src="<?php echo BASE_URL; ?>assets/images/menu/<?php echo $item['image_name']; ?>" alt="<?php echo $item['item_name']; ?>" class="item-image" onerror="this.src='<?php echo BASE_URL; ?>assets/images/default-food.jpg'; this.onerror=null;">
                                    <?php else: ?>
                                        <img src="<?php echo BASE_URL; ?>assets/images/default-food.jpg" alt="No Image Available" class="item-image">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo !empty($item['item_name']) ? $item['item_name'] : 'Unknown Item'; ?>
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
                
                <?php if (!empty($id_documents)): ?>
                <div class="detail-card">
                    <h3>ID Documents</h3>
                    <div class="document-gallery">
                        <?php foreach ($id_documents as $doc): ?>
                        <div class="document-item">
                            <?php 
                            // Get the document path from database
                            $docPath = $doc['document_path'];
                            
                            // Make sure the path is correct
                            if (strpos($docPath, 'uploads/') === 0) {
                                // Path already has uploads/ prefix
                                $imgPath = "../" . $docPath;
                            } else {
                                // Add uploads/ prefix if missing
                                $imgPath = "../" . basename($docPath);
                            }
                            ?>
                            <img src="<?php echo $imgPath; ?>" alt="ID Document" class="document-image" onclick="openModal('<?php echo $imgPath; ?>')">
                            <div class="document-info">
                                <span class="document-date">Uploaded: <?php echo date("d M Y H:i", strtotime($doc['upload_date'])); ?></span>
                                <button class="zoom-btn" onclick="openModal('<?php echo $imgPath; ?>')">
                                    <i class="fas fa-search-plus"></i> View Full Size
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Image Modal -->
                <div id="imageModal" class="modal">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <img class="modal-content" id="fullImage">
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Image modal functionality
        function openModal(imageSrc) {
            var modal = document.getElementById("imageModal");
            var modalImg = document.getElementById("fullImage");
            modal.style.display = "block";
            
            // Create a new image object to ensure we load the full resolution
            var img = new Image();
            img.onload = function() {
                modalImg.src = imageSrc;
                
                // Apply high-quality rendering
                modalImg.style.imageRendering = "-webkit-optimize-contrast";
                modalImg.style.imageRendering = "crisp-edges";
            };
            img.src = imageSrc + "?quality=high&t=" + new Date().getTime(); // Add cache-busting parameter
        }
        
        function closeModal() {
            document.getElementById("imageModal").style.display = "none";
        }
        
        // Close modal when clicking outside the image
        window.onclick = function(event) {
            var modal = document.getElementById("imageModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        
        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeModal();
            }
        });
    </script>
</body>
</html>
