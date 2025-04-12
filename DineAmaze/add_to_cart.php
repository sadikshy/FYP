<?php
// Start the session
session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get the JSON data from the request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Check if data is valid
if ($data) {
    // Check if this item already exists in the cart
    $itemExists = false;
    $existingItemIndex = -1;
    
    // Compare based on item ID and toppings
    foreach ($_SESSION['cart'] as $index => $cartItem) {
        if ($cartItem['id'] == $data['id']) {
            // Check if toppings are the same
            $sameItem = true;
            
            // Compare toppings
            if (count($cartItem['toppings']) != count($data['toppings'])) {
                $sameItem = false;
            } else {
                // Create arrays of topping names for comparison
                $existingToppings = array_map(function($topping) {
                    return $topping['name'];
                }, $cartItem['toppings']);
                
                $newToppings = array_map(function($topping) {
                    return $topping['name'];
                }, $data['toppings']);
                
                sort($existingToppings);
                sort($newToppings);
                
                // Check if toppings match
                if ($existingToppings != $newToppings) {
                    $sameItem = false;
                }
            }
            
            // Compare special instructions
            if ($cartItem['special_instructions'] != $data['special_instructions']) {
                $sameItem = false;
            }
            
            // Compare removed ingredients
            if ($cartItem['removed_ingredients'] != $data['removed_ingredients']) {
                $sameItem = false;
            }
            
            if ($sameItem) {
                $itemExists = true;
                $existingItemIndex = $index;
                break;
            }
        }
    }
    
    if ($itemExists) {
        // Increase quantity instead of adding a new item
        $_SESSION['cart'][$existingItemIndex]['quantity'] += $data['quantity'];
    } else {
        // Add the item to the cart
        $_SESSION['cart'][] = $data;
    }
    
    // Return success response with cart count
    echo json_encode([
        'success' => true,
        'cartCount' => count($_SESSION['cart']),
        'message' => $itemExists ? 'Item quantity updated in cart' : 'Item added to cart successfully'
    ]);
} else {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Invalid data received'
    ]);
}
?>