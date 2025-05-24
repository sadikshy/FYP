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
    $itemPrice = $item['price'];
    $itemQuantity = $item['quantity'];
    $cartTotal += ($itemPrice * $itemQuantity);
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
                        <?php if (!empty($item['image'])): ?>
                            <?php
                            $imagePath = $item['image'];
                            
                            // Check if the image path already contains the full path structure
                            if (strpos($imagePath, 'assets/images/menu/') === false) {
                                // The image path in the database is stored as "Category/filename.jpg"
                                // So we need to prepend the base path
                                $imagePath = 'assets/images/menu/' . $imagePath;
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" onerror="this.onerror=null; this.src='assets/images/menu/default-food.jpg';">
                        <?php else: ?>
                            <img src="assets/images/menu/default-food.jpg" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="item-details">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        
                        <?php if (!empty($item['toppings'])): ?>
                            <p class="toppings">
                                <strong>Toppings:</strong> 
                                <?php 
                                $toppingNames = array_map(function($topping) {
                                    return $topping['name'];
                                }, $item['toppings']);
                                echo htmlspecialchars(implode(', ', $toppingNames)); 
                                ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="item-price">
                            <span>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                        
                        <div class="quantity-controls">
                            <button class="quantity-btn minus" data-index="<?php echo $index; ?>">-</button>
                            <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" max="10" data-index="<?php echo $index; ?>" data-price="<?php echo $item['price']; ?>">
                            <button class="quantity-btn plus" data-index="<?php echo $index; ?>">+</button>
                        </div>
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
                    <span class="cart-total-amount">Rs. <?php echo number_format($cartTotal, 2); ?></span>
                </div>
                
                <div class="cart-actions">
                    <a href="cart.php?clear=1" class="clear-btn">Clear Cart</a>
                    <a href="Takeout.php" class="checkout-btn">Proceed to Takeout</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Add this JavaScript at the end of the file, before the closing body tag -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all quantity buttons
        const minusButtons = document.querySelectorAll('.quantity-btn.minus');
        const plusButtons = document.querySelectorAll('.quantity-btn.plus');
        const quantityInputs = document.querySelectorAll('.quantity-input');
        
        // Add event listeners to minus buttons
        minusButtons.forEach(button => {
            button.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                const input = document.querySelector(`.quantity-input[data-index="${index}"]`);
                let value = parseInt(input.value);
                
                if (value > 1) {
                    value--;
                    input.value = value;
                    updateQuantity(index, value);
                }
            });
        });
        
        // Add event listeners to plus buttons
        plusButtons.forEach(button => {
            button.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                const input = document.querySelector(`.quantity-input[data-index="${index}"]`);
                let value = parseInt(input.value);
                
                if (value < 10) {
                    value++;
                    input.value = value;
                    updateQuantity(index, value);
                }
            });
        });
        
        // Add event listeners to quantity inputs
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                const index = this.getAttribute('data-index');
                let value = parseInt(this.value);
                
                // Ensure value is between 1 and 10
                if (value < 1) value = 1;
                if (value > 10) value = 10;
                
                this.value = value;
                updateQuantity(index, value);
            });
        });
        
        // Function to update quantity via AJAX
        function updateQuantity(index, quantity) {
            fetch('update_cart_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    index: index,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the price display
                    const priceElement = document.querySelector(`.quantity-input[data-index="${index}"]`).closest('.cart-item').querySelector('.item-price span');
                    const unitPrice = parseFloat(document.querySelector(`.quantity-input[data-index="${index}"]`).getAttribute('data-price'));
                    priceElement.textContent = 'Rs. ' + (unitPrice * quantity).toFixed(2);
                    
                    // Update the cart total
                    document.querySelector('.cart-total-amount').textContent = 'Rs. ' + data.cartTotal.toFixed(2);
                    
                    // Update cart count in header if it exists
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.cartCount;
                    }
                }
            })
            .catch(error => {
                console.error('Error updating quantity:', error);
            });
        }
    });
    </script>
</body>
</html>