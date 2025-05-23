<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to save customizations']);
    exit();
}

// Check if required data is provided
if (!isset($_POST['item_id']) || !isset($_POST['custom_name']) || !isset($_POST['customization_data'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

// Get data from POST
$userId = $_SESSION['user_id'];
$itemId = $_POST['item_id'];
$customName = $_POST['custom_name'];
$customizationData = $_POST['customization_data'];

// Connect to database
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Check if customizations table exists, create if not
$checkTableSql = "SHOW TABLES LIKE 'saved_customizations'";
$tableExists = $conn->query($checkTableSql)->num_rows > 0;

if (!$tableExists) {
    $createTableSql = "CREATE TABLE saved_customizations (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        item_id INT(11) NOT NULL,
        custom_name VARCHAR(255) NOT NULL,
        customization_data TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES menu_item(item_id) ON DELETE CASCADE
    )";
    
    if (!$conn->query($createTableSql)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create customizations table']);
        $conn->close();
        exit();
    }
}

// Check if a customization with the same name already exists for this user and item
$checkSql = "SELECT id FROM saved_customizations WHERE user_id = ? AND item_id = ? AND custom_name = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("iis", $userId, $itemId, $customName);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    // Update existing customization
    $customId = $checkResult->fetch_assoc()['id'];
    $updateSql = "UPDATE saved_customizations SET customization_data = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $customizationData, $customId);
    
    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Customization updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update customization']);
    }
    
    $updateStmt->close();
} else {
    // Insert new customization
    $insertSql = "INSERT INTO saved_customizations (user_id, item_id, custom_name, customization_data) VALUES (?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("iiss", $userId, $itemId, $customName, $customizationData);
    
    if ($insertStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Customization saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save customization']);
    }
    
    $insertStmt->close();
}

$checkStmt->close();
$conn->close();
?>
