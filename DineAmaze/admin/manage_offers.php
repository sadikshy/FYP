<?php
// Start the session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dineamaze_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debug information
echo "<div style='background-color: #f8f9fa; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd;'>";
echo "<h3>Database Debug Information</h3>";
echo "<p>Connected to database: " . $dbname . "</p>";

// Check if offers table exists
$table_check = $conn->query("SHOW TABLES LIKE 'offers'");
if ($table_check->num_rows == 0) {
    echo "<p style='color: red;'>The offers table does not exist in the database!</p>";
    echo "<p>Please run the <a href='add_is_hidden_column.php'>database update tool</a> to create the table.</p>";
} else {
    echo "<p style='color: green;'>The offers table exists in the database.</p>";
    
    // Check if there are any offers in the table
    $count_check = $conn->query("SELECT COUNT(*) as count FROM offers");
    $count_result = $count_check->fetch_assoc();
    echo "<p>Number of offers in the database: " . $count_result['count'] . "</p>";
}
echo "</div>";


// Handle form submissions
$message = '';
$errorMessage = '';

// Delete offer
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $sql = "DELETE FROM offers WHERE offer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = "Offer deleted successfully!";
    } else {
        $errorMessage = "Error deleting offer: " . $conn->error;
    }
    
    $stmt->close();
}

// Toggle visibility
if (isset($_GET['toggle_visibility']) && is_numeric($_GET['toggle_visibility'])) {
    $id = $_GET['toggle_visibility'];
    
    // First, get the current status
    $statusSql = "SELECT is_hidden FROM offers WHERE offer_id = ?";
    $statusStmt = $conn->prepare($statusSql);
    $statusStmt->bind_param("i", $id);
    $statusStmt->execute();
    $statusResult = $statusStmt->get_result();
    
    if ($statusResult->num_rows > 0) {
        $currentStatus = $statusResult->fetch_assoc();
        
        // Toggle the status
        $newStatus = $currentStatus['is_hidden'] ? 0 : 1;
        
        $updateSql = "UPDATE offers SET is_hidden = ? WHERE offer_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ii", $newStatus, $id);
        
        if ($updateStmt->execute()) {
            $message = "Offer visibility updated successfully!";
        } else {
            $errorMessage = "Error updating offer visibility: " . $conn->error;
        }
        
        $updateStmt->close();
    }
    
    $statusStmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $badge = $_POST['badge'];
    $section = $_POST['section'];
    $how_to_take = $_POST['how_to_take'];
    $is_ongoing = isset($_POST['is_ongoing']) ? 1 : 0;
    $valid_until = $is_ongoing ? NULL : ($_POST['valid_until'] ? $_POST['valid_until'] : NULL);
    
    // Check if it's an update or a new offer
    if (isset($_POST['offer_id']) && is_numeric($_POST['offer_id'])) {
        // Update existing offer
        $offer_id = $_POST['offer_id'];
        
        // Check if a new image was uploaded
        if ($_FILES['image']['size'] > 0) {
            // Handle image upload
            $target_dir = "../images/offers/";
            
            // Create directory if it doesn't exist
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $image_path = "images/offers/" . basename($_FILES["image"]["name"]);
            
            // Check if image file is an actual image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false) {
                // Upload the file
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    // Update with new image
                    $sql = "UPDATE offers SET title = ?, description = ?, badge = ?, image = ?, valid_until = ?, is_ongoing = ?, section = ?, how_to_take = ? WHERE offer_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssisii", $title, $description, $badge, $image_path, $valid_until, $is_ongoing, $section, $how_to_take, $offer_id);
                } else {
                    $errorMessage = "Sorry, there was an error uploading your file.";
                }
            } else {
                $errorMessage = "File is not an image.";
            }
        } else {
            // Update without changing the image
            $sql = "UPDATE offers SET title = ?, description = ?, badge = ?, valid_until = ?, is_ongoing = ?, section = ?, how_to_take = ? WHERE offer_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssisii", $title, $description, $badge, $valid_until, $is_ongoing, $section, $how_to_take, $offer_id);
        }
        
        if (empty($errorMessage)) {
            if ($stmt->execute()) {
                $message = "Offer updated successfully!";
            } else {
                $errorMessage = "Error updating offer: " . $stmt->error;
            }
            
            $stmt->close();
        }
    } else {
        // Add new offer
        // Handle image upload
        $target_dir = "../images/offers/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $image_path = "images/offers/" . basename($_FILES["image"]["name"]);
        
        // Check if image file is an actual image
        if ($_FILES["image"]["tmp_name"]) {
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false) {
                // Upload the file
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    // Insert new offer
                    $sql = "INSERT INTO offers (title, description, badge, image, valid_until, is_ongoing, section, how_to_take, is_hidden) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssissi", $title, $description, $badge, $image_path, $valid_until, $is_ongoing, $section, $how_to_take, $is_hidden);
                    
                    if ($stmt->execute()) {
                        $message = "Offer added successfully!";
                    } else {
                        $errorMessage = "Error adding offer: " . $stmt->error;
                    }
                    
                    $stmt->close();
                } else {
                    $errorMessage = "Sorry, there was an error uploading your file.";
                }
            } else {
                $errorMessage = "File is not an image.";
            }
        } else {
            $errorMessage = "Please select an image for the offer.";
        }
    }
}

// Get offer for editing
$editOffer = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = $_GET['edit'];
    
    $sql = "SELECT * FROM offers WHERE offer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $editOffer = $result->fetch_assoc();
    }
    
    $stmt->close();
}

// Get all offers with detailed error handling
try {
    // First check if the offers table exists
    $tableCheckSql = "SHOW TABLES LIKE 'offers'";
    $tableCheckResult = $conn->query($tableCheckSql);
    
    if (!$tableCheckResult) {
        throw new Exception("Error checking for offers table: " . $conn->error);
    }
    
    if ($tableCheckResult->num_rows == 0) {
        throw new Exception("The offers table does not exist in the database. Please run the setup script.");
    }
    
    // Now check the structure of the offers table
    $columnCheckSql = "SHOW COLUMNS FROM offers";
    $columnCheckResult = $conn->query($columnCheckSql);
    
    if (!$columnCheckResult) {
        throw new Exception("Error checking columns in offers table: " . $conn->error);
    }
    
    // Now get all offers without debug information
    $sql = "SELECT * FROM offers ORDER BY offer_id DESC";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Error fetching offers: " . $conn->error);
    }
    
    $result = $conn->query($sql); // Re-execute the query for use in the main display
} catch (Exception $e) {
    echo "<div class='admin-message error'>" . $e->getMessage() . "</div>";
    echo "<div class='admin-message'>Please run the <a href='setup_offers_table.php'>setup script</a> to fix database issues.</div>";
    $result = null; // Set result to null so the rest of the page knows there was an error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Offers - DineAmaze Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        
        .admin-wrapper {
            display: flex;
            flex-direction: column;
        }
        
        .content-wrapper {
            padding: 20px;
        }
        
        .main-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        
        h1, h2 {
            color: #333;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .admin-actions {
            display: flex;
            gap: 10px;
        }
        
        .admin-btn, .action-btn, .visibility-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
        }
        
        .admin-btn {
            background-color: #4CAF50;
            color: white;
        }
        
        .admin-btn.secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .edit-btn {
            background-color: #ffc107;
            color: #212529;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        
        .hide-btn {
            background-color: #6c757d;
            color: white;
        }
        
        .show-btn {
            background-color: #28a745;
            color: white;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            min-width: 70px;
        }
        
        .status-badge.visible {
            background-color: #e0f7e9;
            color: #0c7c3e;
            border: none;
        }
        
        .status-badge.hidden {
            background-color: #f8e9ea;
            color: #b02a37;
            border: none;
        }
        
        .action-buttons {
            display: flex;
            gap: 6px;
        }
        
        .edit-btn, .delete-btn, .hide-btn, .show-btn {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .edit-btn {
            background-color: #e9ecef;
            color: #495057;
            border: 1px solid #ced4da;
        }
        
        .edit-btn:hover {
            background-color: #dde2e6;
        }
        
        .delete-btn {
            background-color: #f8e9ea;
            color: #b02a37;
            border: 1px solid #f1aeb5;
        }
        
        .delete-btn:hover {
            background-color: #f5c2c7;
        }
        
        .hide-btn {
            background-color: #e9ecef;
            color: #495057;
            border: 1px solid #ced4da;
        }
        
        .hide-btn:hover {
            background-color: #dde2e6;
        }
        
        .show-btn {
            background-color: #e3f6e9;
            color: #0c7c3e;
            border: 1px solid #a8e6c1;
        }
        
        .show-btn:hover {
            background-color: #c3e6cb;
        }
        
        .admin-message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .admin-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .admin-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .offer-form {
            background-color: #fff;
            padding: 25px;
            border-radius: 6px;
            margin-bottom: 30px;
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4e73df;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d3e2;
            border-radius: 4px;
            box-sizing: border-box;
            color: #6e707e;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }
        
        .form-control:focus {
            border-color: #4e73df;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .form-header h2 {
            margin: 0;
            color: #5a5c69;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .badge-preview {
            display: inline-block;
            padding: 4px 8px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .offers-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .offers-table th, .offers-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eaeaea;
        }
        
        .offers-table th {
            background-color: #f5f5f5;
            font-weight: 600;
            color: #333;
            text-transform: uppercase;
            font-size: 0.85rem;
            border-top: 1px solid #ddd;
        }
        
        .offers-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .offers-table tr:last-child td {
            border-bottom: none;
        }
        
        .offers-table td:first-child, .offers-table th:first-child {
            padding-left: 20px;
        }
        
        .offers-table td:last-child, .offers-table th:last-child {
            padding-right: 20px;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .image-preview {
            max-width: 100px;
            max-height: 80px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .section-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            background-color: #e9ecef;
            color: #495057;
        }
    </style>
    <link rel="stylesheet" href="../css/admin/style.css">
    <link rel="stylesheet" href="../css/admin/sidebar.css">
    <link rel="stylesheet" href="../css/admin/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="content-wrapper">
            <?php include 'includes/header.php'; ?>
            
            <div class="main-content">
                <div class="admin-header">
                    <h1 class="admin-title">Manage Offers</h1>
                    <div class="admin-actions">
                        <a href="../index.php" class="admin-btn secondary">Back to Home</a>
                        <a href="#add-offer" class="admin-btn" id="toggle-form-btn">Add New Offer</a>
                    </div>
                </div>
                
                <?php if (!empty($message)): ?>
                    <div class="admin-message success">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errorMessage)): ?>
                    <div class="admin-message error">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Add/Edit Offer Form -->
                <div class="offer-form" id="offer-form" style="<?php echo $editOffer || isset($_GET['add']) ? 'display: block;' : 'display: none;'; ?>">
                    <h2><?php echo $editOffer ? 'Edit Offer' : 'Add New Offer'; ?></h2>
                    <form action="manage_offers.php" method="POST" enctype="multipart/form-data">
                        <?php if ($editOffer): ?>
                            <input type="hidden" name="offer_id" value="<?php echo $editOffer['offer_id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" class="form-control" value="<?php echo $editOffer ? $editOffer['title'] : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4" required><?php echo $editOffer ? $editOffer['description'] : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="badge">Badge Text</label>
                            <input type="text" id="badge" name="badge" class="form-control" value="<?php echo $editOffer ? $editOffer['badge'] : ''; ?>" required>
                            <div class="badge-preview" id="badge-preview"><?php echo $editOffer ? $editOffer['badge'] : 'BADGE'; ?></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" id="image" name="image" class="form-control" <?php echo $editOffer ? '' : 'required'; ?>>
                            <?php if ($editOffer && isset($editOffer['image'])): ?>
                                <p>Current image: <?php echo $editOffer['image']; ?></p>
                                <img src="../<?php echo $editOffer['image']; ?>" alt="<?php echo $editOffer['title']; ?>" class="image-preview">
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" id="is_ongoing" name="is_ongoing" <?php echo $editOffer && $editOffer['is_ongoing'] ? 'checked' : ''; ?>>
                            <label for="is_ongoing">This is an ongoing offer (no end date)</label>
                        </div>
                        
                        <div class="form-group" id="valid_until_group" style="<?php echo $editOffer && $editOffer['is_ongoing'] ? 'display: none;' : ''; ?>">
                            <label for="valid_until">Valid Until</label>
                            <input type="date" id="valid_until" name="valid_until" class="form-control" value="<?php echo $editOffer && $editOffer['valid_until'] ? $editOffer['valid_until'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="section">Section</label>
                            <select id="section" name="section" class="form-control" required>
                                <option value="current-offers" <?php echo $editOffer && $editOffer['section'] == 'current-offers' ? 'selected' : ''; ?>>Current Offers</option>
                                <option value="special-deals" <?php echo $editOffer && $editOffer['section'] == 'special-deals' ? 'selected' : ''; ?>>Special Deals</option>
                                <option value="celebration-offers" <?php echo $editOffer && $editOffer['section'] == 'celebration-offers' ? 'selected' : ''; ?>>Celebration Offers</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="how_to_take">How to Take the Offer</label>
                            <textarea id="how_to_take" name="how_to_take" class="form-control" required><?php echo $editOffer ? $editOffer['how_to_take'] : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="admin-btn"><?php echo $editOffer ? 'Update Offer' : 'Add Offer'; ?></button>
                            <a href="manage_offers.php" class="admin-btn secondary">Cancel</a>
                        </div>
                    </form>
                </div>
                
                <!-- Offers Table -->
                <h2>All Offers</h2>
                <!-- Offers List Table -->
                <?php if ($result && $result->num_rows > 0): ?>
                    <table class="offers-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>TITLE</th>
                                <th>BADGE</th>
                                <th>DESCRIPTION</th>
                                <th>VALID UNTIL</th>
                                <th>IMAGE</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Reset result pointer to ensure we get all rows
                            $result->data_seek(0);
                            while ($row = $result->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td><?php echo isset($row['offer_id']) ? htmlspecialchars($row['offer_id']) : ''; ?></td>
                                    <td><?php echo isset($row['title']) ? htmlspecialchars($row['title']) : ''; ?></td>
                                    <td><?php echo isset($row['badge']) ? htmlspecialchars($row['badge']) : ''; ?></td>
                                    <td>
                                        <?php if (isset($row['description'])): ?>
                                            <?php 
                                            $description = $row['description'];
                                            if (strlen($description) > 50) {
                                                $description = substr($description, 0, 50) . '...';
                                            }
                                            echo htmlspecialchars($description); 
                                            ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($row['valid_until']) && $row['valid_until'] == 'NULL'): ?>
                                            Ongoing
                                        <?php elseif (isset($row['valid_until']) && $row['valid_until'] == '0000-00-00'): ?>
                                            Not specified
                                        <?php elseif (isset($row['valid_until'])): ?>
                                            <?php echo htmlspecialchars($row['valid_until']); ?>
                                        <?php else: ?>
                                            Not specified
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($row['image']) && !empty($row['image'])): ?>
                                            <img src="../<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo isset($row['title']) ? htmlspecialchars($row['title']) : 'Offer'; ?>" class="image-preview">
                                        <?php else: ?>
                                            No image
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($row['is_hidden'])): ?>
                                            <?php if ($row['is_hidden']): ?>
                                                <span class="status-badge hidden">Hidden</span>
                                            <?php else: ?>
                                                <span class="status-badge visible">Visible</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="status-badge visible">Visible</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="action-buttons">
                                        <?php if (isset($row['offer_id'])): ?>
                                            <a href="?edit=<?php echo $row['offer_id']; ?>" class="edit-btn">Edit</a>
                                            <a href="?delete=<?php echo $row['offer_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this offer?');">Delete</a>
                                            
                                            <?php if (isset($row['is_hidden']) && $row['is_hidden']): ?>
                                                <a href="?toggle_visibility=<?php echo $row['offer_id']; ?>" class="show-btn">Show</a>
                                            <?php else: ?>
                                                <a href="?toggle_visibility=<?php echo $row['offer_id']; ?>" class="hide-btn">Hide</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No offers found. Please add offers using the form above.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                
                    <?php if (!$result || $result->num_rows == 0): ?>
                        <p>No offers found. Please add some offers.</p>
                    <?php endif; ?>
                
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle form visibility
            const toggleFormBtn = document.getElementById('toggle-form-btn');
            const offerForm = document.getElementById('offer-form');
            
            toggleFormBtn.addEventListener('click', function(e) {
                e.preventDefault();
                offerForm.style.display = offerForm.style.display === 'none' ? 'block' : 'none';
            });
            
            // Toggle valid_until field based on is_ongoing checkbox
            const isOngoingCheckbox = document.getElementById('is_ongoing');
            const validUntilGroup = document.getElementById('valid_until_group');
            
            isOngoingCheckbox.addEventListener('change', function() {
                validUntilGroup.style.display = this.checked ? 'none' : 'block';
            });
            
            // Live preview of badge text
            const badgeInput = document.getElementById('badge');
            const badgePreview = document.getElementById('badge-preview');
            
            badgeInput.addEventListener('input', function() {
                badgePreview.textContent = this.value || 'BADGE';
            });
        });
    </script>
</body>
</html>

