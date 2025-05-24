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
    $category_id = $_GET['delete'];
    
    // Check if category has menu items
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM menu_item WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $error_message = "Cannot delete category. It has " . $row['count'] . " menu items associated with it.";
    } else {
        // Delete the category
        $stmt = $conn->prepare("DELETE FROM menu_category WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        
        if ($stmt->execute()) {
            $success_message = "Category deleted successfully!";
        } else {
            $error_message = "Error deleting category: " . $conn->error;
        }
    }
    
    $stmt->close();
}

// Handle add/edit category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST['category_name'];
    
    if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
        // Update existing category
        $category_id = $_POST['category_id'];
        $stmt = $conn->prepare("UPDATE menu_category SET category_name = ? WHERE category_id = ?");
        $stmt->bind_param("si", $category_name, $category_id);
        
        if ($stmt->execute()) {
            $success_message = "Category updated successfully!";
        } else {
            $error_message = "Error updating category: " . $conn->error;
        }
    } else {
        // Add new category
        $stmt = $conn->prepare("INSERT INTO menu_category (category_name) VALUES (?)");
        $stmt->bind_param("s", $category_name);
        
        if ($stmt->execute()) {
            $success_message = "Category added successfully!";
        } else {
            $error_message = "Error adding category: " . $conn->error;
        }
    }
    
    $stmt->close();
}

// Get all categories
$sql = "SELECT c.*, COUNT(m.item_id) as item_count 
        FROM menu_category c 
        LEFT JOIN menu_item m ON c.category_id = m.category_id 
        GROUP BY c.category_id 
        ORDER BY c.category_name";
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
    <title>Manage Categories - DineAmaze Admin</title>
    <link rel="stylesheet" href="../css/admin/style.css">
    <link rel="stylesheet" href="../css/admin/sidebar.css">
    <link rel="stylesheet" href="../css/admin/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/admin/style.css">
   
    <style>
        .category-form {
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
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="dashboard-content">
                <div class="content-header">
                    <h1>Manage Categories</h1>
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
                
                <div class="category-form">
                    <h3 id="form-title">Add New Category</h3>
                    <form method="POST" action="" id="category-form">
                        <input type="hidden" id="category_id" name="category_id" value="">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="category_name">Category Name *</label>
                                <input type="text" id="category_name" name="category_name" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="btn-container">
                            <button type="button" id="cancel-btn" class="btn btn-cancel" onclick="resetForm()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Category</button>
                        </div>
                    </form>
                </div>
                
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Menu Items</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($categories) > 0): ?>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['category_id']; ?></td>
                                    <td><?php echo $category['category_name']; ?></td>
                                    <td><?php echo $category['item_count']; ?></td>
                                    <td class="actions">
                                        <button type="button" class="edit-btn" onclick="editCategory(<?php echo $category['category_id']; ?>, '<?php echo addslashes($category['category_name']); ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($category['item_count'] == 0): ?>
                                        <a href="categories.php?delete=<?php echo $category['category_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this category?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php else: ?>
                                        <button type="button" class="delete-btn disabled" title="Cannot delete category with menu items" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="no-data">No categories found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Function to edit category
        function editCategory(id, name) {
            // Set form values
            document.getElementById('category_id').value = id;
            document.getElementById('category_name').value = name;
            
            // Change form title and button text
            document.getElementById('form-title').innerText = 'Edit Category';
            document.querySelector('#category-form button[type="submit"]').innerText = 'Update Category';
            
            // Show cancel button
            document.getElementById('cancel-btn').classList.add('show');
            
            // Scroll to form
            document.querySelector('.category-form').scrollIntoView({ behavior: 'smooth' });
        }
        
        // Function to reset form
        function resetForm() {
            // Reset form values
            document.getElementById('category-form').reset();
            document.getElementById('category_id').value = '';
            
            // Change form title and button text back
            document.getElementById('form-title').innerText = 'Add New Category';
            document.querySelector('#category-form button[type="submit"]').innerText = 'Add Category';
            
            // Hide cancel button
            document.getElementById('cancel-btn').classList.remove('show');
        }
    </script>
</body>
</html>
