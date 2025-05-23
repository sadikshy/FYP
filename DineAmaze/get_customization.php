<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to view customizations']);
    exit();
}

// Check if customization_id is provided
if (!isset($_GET['customization_id'])) {
    echo json_encode(['success' => false, 'message' => 'Customization ID is required']);
    exit();
}

// Get data from GET
$userId = $_SESSION['user_id'];
$customizationId = $_GET['customization_id'];

// Connect to database
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Get the specific customization
$sql = "SELECT id, user_id, item_id, custom_name, customization_data 
        FROM saved_customizations 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customizationId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Customization not found']);
    $stmt->close();
    $conn->close();
    exit();
}

$customization = $result->fetch_assoc();

// Security check: Make sure the customization belongs to the current user
if ($customization['user_id'] != $userId) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to view this customization']);
    $stmt->close();
    $conn->close();
    exit();
}

echo json_encode([
    'success' => true, 
    'customization' => [
        'id' => $customization['id'],
        'item_id' => $customization['item_id'],
        'custom_name' => $customization['custom_name'],
        'customization_data' => $customization['customization_data']
    ]
]);

$stmt->close();
$conn->close();
?>
