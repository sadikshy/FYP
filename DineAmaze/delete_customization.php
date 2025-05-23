<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to delete customizations']);
    exit();
}

// Check if customization_id is provided
if (!isset($_POST['customization_id'])) {
    echo json_encode(['success' => false, 'message' => 'Customization ID is required']);
    exit();
}

// Get data from POST
$userId = $_SESSION['user_id'];
$customizationId = $_POST['customization_id'];

// Connect to database
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// First, check if the customization exists and belongs to the current user
$checkSql = "SELECT user_id FROM saved_customizations WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $customizationId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Customization not found']);
    $checkStmt->close();
    $conn->close();
    exit();
}

$customization = $checkResult->fetch_assoc();

// Security check: Make sure the customization belongs to the current user
if ($customization['user_id'] != $userId) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this customization']);
    $checkStmt->close();
    $conn->close();
    exit();
}

// Delete the customization
$deleteSql = "DELETE FROM saved_customizations WHERE id = ?";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param("i", $customizationId);

if ($deleteStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Customization deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete customization']);
}

$checkStmt->close();
$deleteStmt->close();
$conn->close();
?>
