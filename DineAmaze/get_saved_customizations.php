<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to view customizations']);
    exit();
}

// Check if item_id is provided
if (!isset($_GET['item_id'])) {
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
    exit();
}

// Get data from GET
$userId = $_SESSION['user_id'];
$itemId = $_GET['item_id'];

// Connect to database
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Check if customizations table exists
$checkTableSql = "SHOW TABLES LIKE 'saved_customizations'";
$tableExists = $conn->query($checkTableSql)->num_rows > 0;

if (!$tableExists) {
    echo json_encode(['success' => true, 'customizations' => []]);
    $conn->close();
    exit();
}

// Get all saved customizations for this user and item
$sql = "SELECT id, custom_name, customization_data, created_at 
        FROM saved_customizations 
        WHERE user_id = ? AND item_id = ? 
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $itemId);
$stmt->execute();
$result = $stmt->get_result();

$customizations = [];
while ($row = $result->fetch_assoc()) {
    $customizations[] = [
        'id' => $row['id'],
        'custom_name' => $row['custom_name'],
        'customization_data' => $row['customization_data'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode(['success' => true, 'customizations' => $customizations]);

$stmt->close();
$conn->close();
?>
