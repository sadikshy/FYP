<?php
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Get cart count
$count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $count = count($_SESSION['cart']);
}

// Return count as JSON
echo json_encode(['count' => $count]);
?>