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

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: menu-items.php");
    exit;
}

$item_id = $_GET['id'];

// Get menu item details
$stmt = $conn->prepare("SELECT * FROM menu_item WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: menu-items.php");
    exit;
}

$menu_item = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $price = $_POST['price'];
    $ingredients = $_POST['ingredients']; // Changed from description to ingredients
    $category_id = $_POST['category_id'];
    
    // Handle image upload
    $image_name = $menu_item['image_name']; // Keep existing image by default
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Check if the file type is allowed
        if(in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $new_filename = uniqid() . '.' . $filetype;
            $upload_dir = "../assets/images/menu/";
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Upload file
            // After successful image upload
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
                $image_name = $new_filename;
                $_SESSION['image_upload_success'] = "Image uploaded successfully!";
            } else {
                $error_message = "Failed to upload image.";
            }
        } else {
            $error_message = "Invalid file type. Only JPG, JPEG, PNG and GIF are allowed.";
        }
    }
    
    // Update menu item
    $stmt = $conn->prepare("UPDATE menu_item SET item_name = ?, price = ?, ingredients = ?, category_id = ?, image_name = ? WHERE item_id = ?");
    $stmt->bind_param("sdsssi", $item_name, $price, $ingredients, $category_id, $image_name, $item_id);
    
    if ($stmt->execute()) {
        $success_message = "Menu item updated successfully!";
        
        // Refresh menu item data
        $stmt = $conn->prepare("SELECT * FROM menu_item WHERE item_id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $menu_item = $result->fetch_assoc();
    } else {
        $error_message = "Error updating menu item: " . $conn->error;
    }
    
    $stmt->close();
}

// Get categories for dropdown
$categories = [];
$result = $conn->query("SELECT * FROM menu_category ORDER BY category_name");
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
    <title>Edit Menu Item - DineAmaze Admin</title>
    <link rel="stylesheet" href="../css/admin/style.css">
    <link rel="stylesheet" href="../css/admin/sidebar.css">
    <link rel="stylesheet" href="../css/admin/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .menu-form {
            background-color: #fff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #6a5acd;
            outline: none;
            box-shadow: 0 0 0 3px rgba(106, 90, 205, 0.1);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: #6a5acd;
            color: #fff;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #5a49c0;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #f0f0f0;
            color: #333;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-secondary:hover {
            background-color: #e0e0e0;
        }
        
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .image-preview img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .current-image {
            margin-top: 10px;
            font-size: 14px;
            color: #6c757d;
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
                    <h1>Edit Menu Item</h1>
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
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="item_name">Item Name *</label>
                                <input type="text" id="item_name" name="item_name" class="form-control" value="<?php echo $menu_item['item_name']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="price">Price (Rs.) *</label>
                                <input type="number" id="price" name="price" step="0.01" min="0" class="form-control" value="<?php echo $menu_item['price']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="category_id">Category *</label>
                                <select id="category_id" name="category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['category_id']; ?>" <?php echo ($category['category_id'] == $menu_item['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo $category['category_name']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Image</label>
                                <?php if (!empty($item['image_name'])): ?>
                                <div>
                                    <p>Current image:</p>
                                    <img src="../assets/images/menu/<?php echo $item['image_name']; ?>" alt="<?php echo $item['item_name']; ?>" class="current-image">
                                </div>
                                <?php endif; ?>
                                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                                <small>Leave empty to keep current image</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="ingredients">Ingredients</label>
                            <textarea id="ingredients" name="ingredients" class="form-control"><?php echo $menu_item['ingredients']; ?></textarea>
                        </div>
                        
                        <div class="btn-container">
                            <a href="menu-items.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back to Menu Items
                            </a>
                            <button type="submit" class="btn btn-primary">Update Menu Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function previewImage(input) {
            var preview = document.getElementById('imagePreview');
            var previewImg = document.getElementById('preview-img');
            
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                <?php if (!empty($menu_item['image_path'])): ?>
                previewImg.src = '../<?php echo $menu_item['image_path']; ?>';
                preview.style.display = 'block';
                <?php else: ?>
                previewImg.src = '';
                preview.style.display = 'none';
                <?php endif; ?>
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>