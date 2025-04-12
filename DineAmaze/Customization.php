<?php
// Start the session
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}

// Check if item_id is provided
$item_id = isset($_GET['item_id']) ? $_GET['item_id'] : null;
$item_name = "Custom Dish";
$item_image = "pizza-image.jpg";
$item_ingredients = "";
$item_price = 0;

// If item_id is provided, fetch item details from database
if ($item_id) {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "dineamaze_database");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Get item details
    $stmt = $conn->prepare("SELECT * FROM menu_item WHERE item_id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        $item_name = $item['item_name'];
        $item_image = $item['image_name'];
        $item_ingredients = $item['ingredients'];
        $item_price = $item['price'];
    }
    
    $conn->close();
}

// Define toppings with prices
$available_toppings = [
    'cheese' => ['name' => 'Extra Cheese', 'price' => 50],
    'pepperoni' => ['name' => 'Pepperoni', 'price' => 70],
    'mushrooms' => ['name' => 'Mushrooms', 'price' => 40],
    'onions' => ['name' => 'Onions', 'price' => 30],
    'olives' => ['name' => 'Olives', 'price' => 45],
    'bell_peppers' => ['name' => 'Bell Peppers', 'price' => 35],
    'chicken' => ['name' => 'Grilled Chicken', 'price' => 80],
    'paneer' => ['name' => 'Paneer', 'price' => 60],
    'corn' => ['name' => 'Sweet Corn', 'price' => 30],
    'jalapenos' => ['name' => 'Jalapeños', 'price' => 40]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customize <?php echo $item_name; ?> - DineAmaze</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/Customization.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .empty-selection-notification {
            text-align: center;
            padding: 40px;
            background-color: #fff8f3;
            border-radius: 8px;
            margin: 50px auto;
            max-width: 600px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: none; /* Hidden by default */
        }
        
        .empty-selection-notification.show {
            display: block;
        }
        
        .empty-selection-notification i {
            font-size: 48px;
            color: #ff6b00;
            margin-bottom: 20px;
        }
        
        .browse-menu-btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #ff6b00;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        
        .browse-menu-btn:hover {
            background-color: #e55f00;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.3);
        }
        
        .customize-container {
            display: <?php echo (!$item_id) ? 'none' : 'block'; ?>;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    
    
    
   
    <div class="customization-hero">
        <div class="hero-overlay">
            <h1>Customize Your <?php echo $item_name; ?></h1>
            <p>Make it exactly how you like it</p>
        </div>
    </div>
    <?php if (!$item_id): ?>
    <div class="empty-selection-notification show">
        <i class="fas fa-utensils"></i>
        <h3>No Item Selected!</h3>
        <p>Please select a dish from our menu first to customize your order.</p>
        <a href="Menu.php" class="browse-menu-btn">Browse Menu</a>
    </div>
    <?php endif; ?>
    <div class="customize-container">
   
        <div class="customize-header">
            <h2>Customize Your Order</h2>
        </div>
        
        <div class="customize-content">
            <div class="item-preview">
                <img src="assets/images/menu/<?php echo $item_image; ?>" alt="<?php echo $item_name; ?>">
                <div class="item-details">
                    <h3 class="item-name"><?php echo $item_name; ?></h3>
                    <p class="item-ingredients"><?php echo $item_ingredients; ?></p>
                    <p class="item-price">Base Price: Rs. <?php echo number_format($item_price, 2); ?></p>
                </div>
            </div>
            
            <div class="customize-options">
                <div class="option-group">
                    <h3>Quantity</h3>
                    <div class="portion-selector">
                        <button type="button" class="portion-btn minus-btn"><i class="fas fa-minus"></i></button>
                        <input type="number" id="portion" name="portion" value="1" min="1" max="10" readonly>
                        <button type="button" class="portion-btn plus-btn"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                
                <div class="option-group">
                    <h3>Add Extra Toppings</h3>
                    <div class="toppings-grid">
                        <?php foreach ($available_toppings as $key => $topping): ?>
                        <div class="topping-item">
                            <input type="checkbox" id="topping-<?php echo $key; ?>" name="toppings[]" value="<?php echo $key; ?>" data-price="<?php echo $topping['price']; ?>">
                            <label for="topping-<?php echo $key; ?>"><?php echo $topping['name']; ?></label>
                            <span class="topping-price">+Rs. <?php echo $topping['price']; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if (!empty($item_ingredients)): ?>
                <div class="option-group">
                    <h3>Remove Ingredients</h3>
                    <div class="ingredients-list">
                        <?php 
                        $ingredients_array = explode(',', $item_ingredients);
                        foreach ($ingredients_array as $ingredient): 
                            $ingredient = trim($ingredient);
                            if (!empty($ingredient)):
                        ?>
                        <div class="ingredient-item">
                            <input type="checkbox" id="remove-<?php echo strtolower(str_replace(' ', '-', $ingredient)); ?>" name="remove[]" value="<?php echo $ingredient; ?>">
                            <label for="remove-<?php echo strtolower(str_replace(' ', '-', $ingredient)); ?>"><?php echo $ingredient; ?></label>
                        </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="option-group">
                    <h3>Special Instructions</h3>
                    <textarea id="special_instructions" name="special_instructions" class="special-instructions" placeholder="Any special requests or preferences..."></textarea>
                </div>
            </div>
        </div>
        
        <div class="order-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Base Price:</span>
                <span>Rs. <?php echo number_format($item_price, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Quantity:</span>
                <span id="quantity-display">1</span>
            </div>
            <div class="summary-row">
                <span>Extra Toppings:</span>
                <span id="toppings-price">Rs. 0.00</span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span id="total-price">Rs. <?php echo number_format($item_price, 2); ?></span>
            </div>
            
            <div class="action-buttons">
                <a href="Menu.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Menu</a>
                <button type="button" class="add-to-cart-btn" id="addToCartBtn"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
            </div>
        </div>
    </div>
    
    <input type="hidden" id="item_id" name="item_id" value="<?php echo $item_id; ?>">
    <input type="hidden" id="base_price" value="<?php echo $item_price; ?>">
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const minusBtn = document.querySelector('.minus-btn');
            const plusBtn = document.querySelector('.plus-btn');
            const portionInput = document.getElementById('portion');
            const quantityDisplay = document.getElementById('quantity-display');
            const basePrice = parseFloat(document.getElementById('base_price').value);
            const toppingCheckboxes = document.querySelectorAll('input[name="toppings[]"]');
            const toppingsPriceDisplay = document.getElementById('toppings-price');
            const totalPriceDisplay = document.getElementById('total-price');
            
            // Quantity buttons
            minusBtn.addEventListener('click', function() {
                let currentValue = parseInt(portionInput.value);
                if (currentValue > 1) {
                    portionInput.value = currentValue - 1;
                    quantityDisplay.textContent = currentValue - 1;
                    updateTotalPrice();
                }
            });
            
            plusBtn.addEventListener('click', function() {
                let currentValue = parseInt(portionInput.value);
                if (currentValue < 10) {
                    portionInput.value = currentValue + 1;
                    quantityDisplay.textContent = currentValue + 1;
                    updateTotalPrice();
                }
            });
            
            // Topping checkboxes
            toppingCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateTotalPrice);
            });
            
            // Update total price
            function updateTotalPrice() {
                const quantity = parseInt(portionInput.value);
                
                // Calculate toppings price
                let toppingsTotal = 0;
                toppingCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        toppingsTotal += parseFloat(checkbox.dataset.price);
                    }
                });
                
                // Update toppings price display
                toppingsPriceDisplay.textContent = 'Rs. ' + toppingsTotal.toFixed(2);
                
                // Calculate total
                const total = (basePrice + toppingsTotal) * quantity;
                
                // Update total price display
                totalPriceDisplay.textContent = 'Rs. ' + total.toFixed(2);
            }
        });
        
        function addToCart() {
            // Get form values
            const itemId = document.getElementById('item_id').value;
            const portion = document.getElementById('portion').value;
            
            // Get selected toppings
            const selectedToppings = [];
            document.querySelectorAll('input[name="toppings[]"]:checked').forEach(checkbox => {
                selectedToppings.push(checkbox.value);
            });
            
            // Get ingredients to remove
            const removeIngredients = [];
            document.querySelectorAll('input[name="remove[]"]:checked').forEach(checkbox => {
                removeIngredients.push(checkbox.value);
            });
            
            // Get special instructions
            const specialInstructions = document.getElementById('special_instructions').value;
            
            // Here you would typically send this data to the server via AJAX
            // For now, we'll just show an alert
            alert('Item added to cart!');
            
            // Redirect back to menu
            window.location.href = 'Menu.php';
        }
    
    
    // Add this at the end of your file, before the closing </body> tag
    document.addEventListener('DOMContentLoaded', function() {
        // Get elements
        const addToCartBtn = document.getElementById('addToCartBtn');
        const portionInput = document.getElementById('portion');
        const basePrice = parseFloat(document.getElementById('base_price').value);
        const itemId = document.getElementById('item_id').value;
        const itemName = document.querySelector('.item-name').textContent;
        const itemImage = document.querySelector('.item-preview img').getAttribute('src').split('/').pop();
        
        // Add to cart functionality
        addToCartBtn.addEventListener('click', function() {
            // Get quantity
            const quantity = parseInt(portionInput.value);
            
            // Get selected toppings
            const toppings = [];
            const toppingCheckboxes = document.querySelectorAll('input[name="toppings[]"]:checked');
            let toppingsTotal = 0;
            
            toppingCheckboxes.forEach(function(checkbox) {
                const toppingName = checkbox.nextElementSibling.textContent.trim();
                const toppingPrice = parseFloat(checkbox.getAttribute('data-price'));
                toppings.push({
                    name: toppingName,
                    price: toppingPrice
                });
                toppingsTotal += toppingPrice;
            });
            
            // Get removed ingredients
            const removedIngredients = [];
            const ingredientCheckboxes = document.querySelectorAll('input[name="remove[]"]:checked');
            
            ingredientCheckboxes.forEach(function(checkbox) {
                removedIngredients.push(checkbox.nextElementSibling.textContent.trim());
            });
            
            // Get special instructions
            const specialInstructions = document.getElementById('special_instructions').value;
            
            // Calculate total price
            const totalPrice = (basePrice + toppingsTotal);
            
            // Create cart item
            const cartItem = {
                id: itemId,
                name: itemName,
                image: itemImage,
                quantity: quantity,
                price: totalPrice,
                toppings: toppings,
                removed_ingredients: removedIngredients,
                special_instructions: specialInstructions
            };
            
            // Send AJAX request to add item to cart
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(cartItem)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count in header
                    const cartCount = document.querySelector('.cart-count');
                    cartCount.textContent = data.cartCount;
                    
                    // Show success message
                    alert(data.message);
                    
                    // Redirect to cart page or stay on current page
                    if (confirm('View your cart?')) {
                        window.location.href = 'cart.php';
                    }
                } else {
                    alert('Error adding item to cart: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the item to cart.');
            });
        });
    });
</script>
</body>
</html>
