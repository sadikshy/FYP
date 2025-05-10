<?php
// Start the session
session_start();

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'cartTotal' => 0,
    'cartCount' => 0
];

// Get the JSON data from the request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Check if data is valid and cart exists
if ($data && isset($_SESSION['cart'])) {
    $index = $data['index'];
    $quantity = $data['quantity'];
    
    // Validate index and quantity
    if (isset($_SESSION['cart'][$index]) && $quantity >= 1 && $quantity <= 10) {
        // Update the quantity
        $_SESSION['cart'][$index]['quantity'] = $quantity;
        
        // Calculate cart total
        $cartTotal = 0;
        foreach ($_SESSION['cart'] as $item) {
            $itemPrice = $item['price'];
            $itemQuantity = $item['quantity'];
            $cartTotal += ($itemPrice * $itemQuantity);
        }
        
        // Update response
        $response = [
            'success' => true,
            'message' => 'Quantity updated successfully',
            'cartTotal' => $cartTotal,
            'cartCount' => count($_SESSION['cart']),
            'itemTotal' => $_SESSION['cart'][$index]['price'] * $quantity
        ];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>