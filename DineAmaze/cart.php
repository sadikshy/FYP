<?php
// Start the session
session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle remove item action
if (isset($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: cart.php");
    exit;
}

// Handle clear cart action
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;
}

// Calculate cart total
$cartTotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - DineAmaze</title>
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-solid-straight/css/uicons-solid-straight.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-chubby/css/uicons-solid-chubby.css'>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="cart-container">
        <h1>Your Cart</h1>
        
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty</p>
                <a href="Menu.php" class="btn">Browse Menu</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <div class="cart-item">
                    <div class="item-image">
                        <img src="images/Menu Photos/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                    </div>
                    <div class="item-details">
                        <h3><?php echo $item['name']; ?></h3>
                        <p class="item-price">Rs. <?php echo number_format($item['price'], 2); ?></p>
                        <?php if (!empty($item['toppings'])): ?>
                        <p class="item-toppings">
                            <strong>Extra Toppings:</strong> 
                            <?php echo implode(', ', array_map(function($topping) {
                                return $topping['name'];
                            }, $item['toppings'])); ?>
                        </p>
                        <?php endif; ?>
                        <?php if (!empty($item['special_instructions'])): ?>
                        <p class="item-instructions">
                            <strong>Special Instructions:</strong> 
                            <?php echo $item['special_instructions']; ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <div class="item-quantity">
                        <span>Quantity: <?php echo $item['quantity']; ?></span>
                    </div>
                    <div class="item-total">
                        <span>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                    <div class="item-actions">
                        <a href="cart.php?remove=<?php echo $index; ?>" class="remove-btn">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Total:</span>
                    <span>Rs. <?php echo number_format($cartTotal, 2); ?></span>
                </div>
                
                <div class="cart-actions">
                    <a href="cart.php?clear=1" class="clear-btn">Clear Cart</a>
                    <a href="Takeout.php" class="checkout-btn">Proceed to Takeout</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>