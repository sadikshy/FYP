<?php
// Start the session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check if user ID is provided
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define base URL for assets
define('BASE_URL', 'http://localhost/dashboard/Kachuwafyp/Development/DineAmaze/');

// Get user details
$user_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: users.php");
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// Get user orders
$stmt = $conn->prepare("SELECT * FROM takeout_order_items WHERE email = ? ORDER BY order_date DESC");
$stmt->bind_param("s", $user['email']);
$stmt->execute();
$orders_result = $stmt->get_result();
$orders = [];

while ($order = $orders_result->fetch_assoc()) {
    $orders[] = $order;
}
$stmt->close();

// Get user ID documents
$id_documents = [];
$stmt = $conn->prepare("SELECT d.* FROM id_documents d 
                       JOIN takeout_order_items t ON d.order_group_id = t.order_group_id 
                       WHERE t.email = ? ORDER BY d.upload_date DESC");
$stmt->bind_param("s", $user['email']);
$stmt->execute();
$docs_result = $stmt->get_result();

while ($doc = $docs_result->fetch_assoc()) {
    $id_documents[] = $doc;
}
$stmt->close();

// Check if reviews table exists
$tableExists = false;
$result = $conn->query("SHOW TABLES LIKE 'reviews'");
if ($result->num_rows > 0) {
    $tableExists = true;
}

// Get user reviews only if the table exists
$reviews = [];
if ($tableExists) {
    $stmt = $conn->prepare("SELECT r.*, m.item_name FROM reviews r 
                           LEFT JOIN menu_item m ON r.menu_item_id = m.item_id
                           WHERE r.user_id = ? ORDER BY r.review_date DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $reviews_result = $stmt->get_result();
    
    while ($review = $reviews_result->fetch_assoc()) {
        $reviews[] = $review;
    }
    $stmt->close();
}

// Handle user verification status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_verification'])) {
    $is_verified = $_POST['is_verified'] ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE user SET is_verified = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $is_verified, $user_id);
    
    if ($stmt->execute()) {
        $success_message = "User verification status updated successfully!";
        
        // Refresh user data
        $stmt = $conn->prepare("SELECT * FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $error_message = "Error updating user verification status: " . $conn->error;
    }
    
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - DineAmaze Admin</title>
    <link rel="stylesheet" href="../css/admin/style.css">
    <link rel="stylesheet" href="../css/admin/sidebar.css">
    <link rel="stylesheet" href="../css/admin/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .user-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .user-details {
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
        
        .verification-form {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .verification-form label {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            cursor: pointer;
        }
        
        .verification-form input[type="checkbox"] {
            margin-right: 10px;
        }
        
        .verification-form button {
            padding: 8px 15px;
            background-color: #6a5acd;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .verification-form button:hover {
            background-color: #5a49c0;
        }
        
        .activity-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .activity-table th, 
        .activity-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .activity-table th {
            background-color: #f9f9f9;
            font-weight: 600;
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
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .user-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-verified {
            background-color: #2ecc71;
            color: #fff;
        }
        
        .status-not-verified {
            background-color: #e74c3c;
            color: #fff;
        }
        
        .user-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 5px solid #f5f5f5;
        }
        
        .user-profile {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .order-status {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .review-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .review-item-name {
            font-weight: 500;
        }
        
        .review-date {
            color: #7f8c8d;
            font-size: 12px;
        }
        
        .review-rating {
            color: #f39c12;
            margin-bottom: 5px;
        }
        
        .review-text {
            font-size: 14px;
            line-height: 1.5;
        }
        
        .tab-container {
            margin-top: 20px;
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
        
        .tab-buttons {
            display: flex;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
        
        .tab-button {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            border-bottom: 3px solid transparent;
            font-weight: 500;
        }
        
        .tab-button.active {
            border-bottom-color: #6a5acd;
            color: #6a5acd;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="dashboard-content">
                <a href="users.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Users</a>
                
                <div class="content-header">
                    <h1>User Details</h1>
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
                
                <div class="user-details">
                    <div class="detail-card">
                        <div class="user-profile">
                            <img src="<?php echo !empty($user['profile_image']) ? '../' . $user['profile_image'] : '../assets/images/default-user.png'; ?>" alt="<?php echo $user['name']; ?>" class="user-image">
                            <h2><?php echo $user['name']; ?></h2>
                            <div class="user-status <?php echo $user['is_verified'] ? 'status-verified' : 'status-not-verified'; ?>">
                                <?php echo $user['is_verified'] ? 'Verified' : 'Not Verified'; ?>
                            </div>
                        </div>
                        
                        <h3>Basic Information</h3>
                        
                        <div class="detail-row">
                            <div class="detail-label">User ID:</div>
                            <div class="detail-value">#<?php echo $user['user_id']; ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Email:</div>
                            <div class="detail-value"><?php echo $user['email']; ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Phone:</div>
                            <div class="detail-value"><?php echo $user['phone_number']; ?></div>
                        </div>
                        
                       
                        
                        <form method="POST" action="" class="verification-form">
                            <label>
                                <input type="checkbox" name="is_verified" value="1" <?php echo $user['is_verified'] ? 'checked' : ''; ?>>
                                Mark User as Verified
                            </label>
                            <p class="note">Verified users can place takeout orders without ID verification.</p>
                            <input type="hidden" name="update_verification" value="1">
                            <button type="submit">Update Verification Status</button>
                        </form>
                    </div>
                    
                    <div class="detail-card">
                        <h3>User Activity</h3>
                        
                        <div class="tab-container">
                            <div class="tab-buttons">
                                <button class="tab-button active" onclick="openTab('orders')">Orders (<?php echo count($orders); ?>)</button>
                                <button class="tab-button" onclick="openTab('reviews')">Reviews (<?php echo count($reviews); ?>)</button>
                            </div>
                            
                            <div id="orders" class="tab-content active">
                                <?php if (count($orders) > 0): ?>
                                <table class="activity-table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Item</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['order_id']; ?></td>
                                            <td><?php echo $order['item_name']; ?></td>
                                            <td><?php echo date("d M Y H:i", strtotime($order['order_date'])); ?></td>
                                            <td>
                                                <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="order-details.php?id=<?php echo $order['order_id']; ?>" class="view-btn">View Order</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <p>No orders found for this user.</p>
                                <?php endif; ?>
                            </div>
                            
                            <div id="reviews" class="tab-content">
                                <?php if (count($reviews) > 0): ?>
                                <?php foreach ($reviews as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="review-item-name"><?php echo $review['item_name']; ?></div>
                                        <div class="review-date"><?php echo date("d M Y", strtotime($review['review_date'])); ?></div>
                                    </div>
                                    <div class="review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="review-text"><?php echo $review['review_text']; ?></div>
                                </div>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <p>No reviews found for this user.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($id_documents)): ?>
                <div class="detail-card" style="margin-top: 20px;">
                    <h3>ID Documents</h3>
                    <div class="document-gallery">
                        <?php foreach ($id_documents as $doc): ?>
                            <div class="document-item">
                      <img src="../<?php echo $doc['document_path']; ?>" alt="ID Document" class="document-image" onclick="openModal('../<?php echo $doc['document_path']; ?>')">
                        <div class="document-info">
                            <span class="document-date">Uploaded: <?php echo date("d M Y H:i", strtotime($doc['upload_date'])); ?></span>
                            <button class="zoom-btn" onclick="openModal('../<?php echo $doc['document_path']; ?>')">
                                <i class="fas fa-search-plus"></i> View Full Size
                            </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="fullImage">
    </div>
    
    <script>
        function openTab(tabId) {
            // Hide all tab contents
            var tabContents = document.getElementsByClassName('tab-content');
            for (var i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Deactivate all tab buttons
            var tabButtons = document.getElementsByClassName('tab-button');
            for (var i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }
            
            // Show the selected tab content
            document.getElementById(tabId).classList.add('active');
            
            // Activate the clicked button
            event.currentTarget.classList.add('active');
        }
        
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
<?php $conn->close(); ?>
