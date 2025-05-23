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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $price = $_POST['price'];
    $offer_price = !empty($_POST['offer_price']) ? $_POST['offer_price'] : NULL;
    $ingredients = $_POST['ingredients'];
    $category_id = $_POST['category_id'];
    $is_customizable = isset($_POST['is_customizable']) ? $_POST['is_customizable'] : 0;
    
    // Handle image upload
    $image_name = ''; // Changed from image_path to image_name
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Get category name for folder structure
        $category_stmt = $conn->prepare("SELECT category_name FROM menu_category WHERE category_id = ?");
        $category_stmt->bind_param("i", $category_id);
        $category_stmt->execute();
        $category_result = $category_stmt->get_result();
        $category_data = $category_result->fetch_assoc();
        $category_folder = $category_data['category_name'];
        
        // Create category-specific directory
        $target_dir = "../assets/images/menu/{$category_folder}/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Upload the file
        if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_name = $category_folder . '/' . $new_filename;
            $_SESSION['image_upload_success'] = "Image uploaded successfully!";
        } else {
            $error_message = "Failed to upload image.";
        }
    }
    
    // Insert menu item
    $stmt = $conn->prepare("INSERT INTO menu_item (item_name, price, offer_price, ingredients, category_id, image_name, is_customizable) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sddsssi", $item_name, $price, $offer_price, $ingredients, $category_id, $image_name, $is_customizable);
    
    if ($stmt->execute()) {
        $success_message = "Menu item added successfully!";
    } else {
        $error_message = "Error adding menu item: " . $conn->error;
    }
    
    $stmt->close();
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
    <title>Add Menu Item - DineAmaze Admin</title>
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
            display: none;
        }
        
        .image-preview img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 5px;
            display: none;
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
                    <h1>Add Menu Item</h1>
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
                    <!-- In the form section -->
                    <form method="POST" action="" enctype="multipart/form-data" id="addMenuForm" novalidate>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="item_name">Item Name *</label>
                                <input type="text" id="item_name" name="item_name" class="form-control" required>
                                <div class="error-message" id="itemNameError">Item name is required and must be at least 3 characters</div>
                            </div>
                            <div class="form-group">
                                <label for="price">Price (Rs.) *</label>
                                <input type="number" id="price" name="price" class="form-control" step="0.01" required>
                                <div class="error-message" id="priceError">Please enter a valid price (greater than 0)</div>
                            </div>
                            <div class="form-group">
                                <label for="offer_price">Offer Price (Rs.)</label>
                                <input type="number" id="offer_price" name="offer_price" class="form-control" step="0.01" placeholder="Leave empty if no offer">
                                <div class="error-message" id="offerPriceError">Offer price must be less than regular price</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select id="category_id" name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['category_id']; ?>">
                                    <?php echo $category['category_name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="error-message" id="categoryError">Please select a category</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                            <div class="image-preview" id="imagePreview">
                                <img src="#" alt="Image Preview" id="preview-img">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="ingredients">Ingredients</label>
                            <textarea id="ingredients" name="ingredients" class="form-control"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Customizable</label>
                            <div style="display: flex; gap: 20px; margin-top: 8px;">
                                <div>
                                    <input type="radio" id="customizable_yes" name="is_customizable" value="1">
                                    <label for="customizable_yes" style="display: inline; font-weight: normal;">Yes</label>
                                </div>
                                <div>
                                    <input type="radio" id="customizable_no" name="is_customizable" value="0" checked>
                                    <label for="customizable_no" style="display: inline; font-weight: normal;">No</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="btn-container">
                            <a href="menu-items.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back to Menu Items
                            </a>
                            <button type="submit" class="btn btn-primary">Add Menu Item</button>
                        </div>
                    </form>
                    <!-- Add this script before closing body tag -->
                    <script>
                        document.getElementById('addMenuForm').addEventListener('submit', function(e) {
                            let isValid = true;
                            
                            // Item Name validation
                            const itemName = document.getElementById('item_name').value.trim();
                            if (itemName.length < 3) {
                                document.getElementById('itemNameError').style.display = 'block';
                                isValid = false;
                            } else {
                                document.getElementById('itemNameError').style.display = 'none';
                            }
                            
                            // Price validation
                            const price = parseFloat(document.getElementById('price').value);
                            if (isNaN(price) || price <= 0) {
                                document.getElementById('priceError').style.display = 'block';
                                isValid = false;
                            } else {
                                document.getElementById('priceError').style.display = 'none';
                            }
                            
                            // Category validation
                            const category = document.getElementById('category_id').value;
                            if (!category) {
                                document.getElementById('categoryError').style.display = 'block';
                                isValid = false;
                            } else {
                                document.getElementById('categoryError').style.display = 'none';
                            }
                            
                            // Image validation
                            const image = document.getElementById('image').files[0];
                            if (!image && !document.getElementById('preview-img').src) {
                                document.getElementById('imageError').style.display = 'block';
                                isValid = false;
                            } else {
                                document.getElementById('imageError').style.display = 'none';
                            }
                            
                            if (!isValid) {
                                e.preventDefault();
                            }
                        });
                    </script>
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
                previewImg.src = '';
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>
