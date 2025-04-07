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

// Handle delete action
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $item_id = $_GET['delete'];
    
    // Delete the menu item
    $stmt = $conn->prepare("DELETE FROM menu_item WHERE item_id = ?");
    $stmt->bind_param("i", $item_id);
    
    if ($stmt->execute()) {
        $success_message = "Menu item deleted successfully!";
    } else {
        $error_message = "Error deleting menu item: " . $conn->error;
    }
    
    $stmt->close();
}

// Check for image upload success message from other pages
if (isset($_SESSION['image_upload_success'])) {
    $success_message = $_SESSION['image_upload_success'];
    unset($_SESSION['image_upload_success']); // Clear the message after displaying
}

// Get all menu items with category names
$sql = "SELECT m.*, c.category_name 
        FROM menu_item m 
        LEFT JOIN menu_category c ON m.category_id = c.category_id 
        ORDER BY m.item_name";
$result = $conn->query($sql);
$menu_items = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $menu_items[] = $row;
    }
}

// Get all categories for the dropdown
$sql = "SELECT * FROM menu_category ORDER BY category_name";
$result = $conn->query($sql);
$categories = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu Items - DineAmaze Admin</title>
    <link rel="stylesheet" href="../css/admin/style.css">
    <link rel="stylesheet" href="../css/admin/sidebar.css">
    <link rel="stylesheet" href="../css/admin/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .menu-form {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            flex: 1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .btn-container {
            display: flex;
            justify-content: flex-end;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: #6a5acd;
            color: #fff;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #5a49c0;
        }
        
        .btn-cancel {
            background-color: #f0f0f0;
            color: #333;
            border: none;
            margin-right: 10px;
            display: none;
        }
        
        .btn-cancel.show {
            display: inline-block;
        }
        
        .btn-cancel:hover {
            background-color: #e0e0e0;
        }
        
        .menu-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .price {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .category-badge {
            display: inline-block;
            padding: 5px 10px;
            background-color: #e9ecef;
            color: #495057;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="dashboard-content">
                <div class="content-header">
                    <h1>Manage Menu Items</h1>
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
                
                <div class="menu-form">
                    <h3>Add New Menu Item</h3>
                    <p>To add a new menu item, please use the <a href="add-menu-item.php" style="color: #6a5acd; text-decoration: none; font-weight: 600;">Add Menu Item</a> page.</p>
                </div>
                
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Ingredients</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($menu_items) > 0): ?>
                                <?php foreach ($menu_items as $item): ?>
                                <tr>
                                    <td>
                                        <img src="../<?php echo $item['image_name'] ? 'assets/images/menu/'.$item['image_name'] : 'assets/images/default-food.jpg'; ?>" alt="<?php echo $item['item_name']; ?>" class="menu-image">
                                    </td>
                                    <td><?php echo $item['item_name']; ?></td>
                                    <td class="price">Rs. <?php echo number_format($item['price'], 2); ?></td>
                                    <td><span class="category-badge"><?php echo $item['category_name']; ?></span></td>
                                    <td><?php echo strlen($item['ingredients']) > 50 ? substr($item['ingredients'], 0, 50) . '...' : $item['ingredients']; ?></td>
                                    <td class="actions">
                                        <a href="edit-menu-item.php?id=<?php echo $item['item_id']; ?>" class="edit-btn">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="menu-items.php?delete=<?php echo $item['item_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this menu item?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="no-data">No menu items found</td>
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