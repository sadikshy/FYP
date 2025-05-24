<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}

// Get user's verification status
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];
$sql = "SELECT is_verified FROM user WHERE user_id = '$userId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['is_verified'] = $user['is_verified'] ?? 0;
}
$conn->close();

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <div class="customization-buttons">
                    <button id="save-customization-btn" class="customization-btn save-customization-btn" data-item-id="<?php echo $item_id; ?>" data-item-name="<?php echo $item_name; ?>">
                        <i class="fas fa-save"></i> Save Custom
                    </button>
                    <button id="load-customization-btn" class="customization-btn load-customization-btn" data-item-id="<?php echo $item_id; ?>" data-item-name="<?php echo $item_name; ?>">
                        <i class="fas fa-history"></i> My Customs
                    </button>
                </div>
                <button type="button" class="add-to-cart-btn" id="addToCartBtn"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
            </div>
        </div>
    </div>
    
    <input type="hidden" id="item_id" name="item_id" value="<?php echo $item_id; ?>">
    <input type="hidden" id="base_price" value="<?php echo $item_price; ?>">
    
    <?php include 'includes/footer.php'; ?>
   
    
    <!-- Initialize notification container for food customization -->
    <div id="notification-container"></div>
    
    <!-- Add this script at the bottom of your Customization.php file -->
    <script>
    // Add to cart functionality
    document.getElementById('addToCartBtn').addEventListener('click', function() {
        const itemId = document.getElementById('item_id').value;
        const itemName = document.querySelector('.item-name').textContent;
        const basePrice = parseFloat(document.getElementById('base_price').value);
        
        // Get the image path
        let itemImage = document.querySelector('.item-preview img').getAttribute('src');
        
        // Fix image path if needed
        if (itemImage) {
            // If the image path contains 'assets/images/menu/', keep it as is
            if (!itemImage.includes('assets/images/menu/')) {
                // Extract just the filename if it's a full path
                if (itemImage.includes('/')) {
                    const parts = itemImage.split('/');
                    itemImage = parts[parts.length - 1];
                }
                
                // Add the correct path prefix
                itemImage = 'assets/images/menu/' + itemImage;
            }
        }
        
        // Get customization details
        const customizationData = collectCustomizationDetails();
        
        // Calculate final price with customizations
        let finalPrice = basePrice * customizationData.quantity;
        let toppingsText = '';
        
        if (customizationData && customizationData.details.toppings.length > 0) {
            // Add topping prices
            customizationData.details.toppings.forEach(topping => {
                finalPrice += parseFloat(topping.price || 0);
                toppingsText += topping.name + ', ';
            });
            
            // Remove trailing comma and space
            toppingsText = toppingsText.slice(0, -2);
        }
        
        // Add special instructions if any
        if (customizationData && customizationData.details.special_instructions) {
            if (toppingsText) {
                toppingsText += ' | ';
            }
            toppingsText += 'Instructions: ' + customizationData.details.special_instructions;
        }
        
        // Add to cart via AJAX
        $.ajax({
            url: 'add_to_cart.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id: itemId,
                name: itemName,
                price: finalPrice,
                quantity: customizationData.quantity,
                image: itemImage,
                toppings: customizationData.details.toppings,
                special_instructions: customizationData.details.special_instructions || '',
                removed_ingredients: customizationData.details.removed_ingredients || []
            }),
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        // Update cart count in header directly from the response
                        const cartCountElement = document.querySelector('.cart-count');
                        if (cartCountElement && result.cartCount) {
                            cartCountElement.textContent = result.cartCount;
                        } else {
                            // Fallback to AJAX update if count not in response
                            updateCartCount();
                        }
                        showNotification('Item added to cart!', 'success');
                    } else {
                        showNotification('Error adding item to cart', 'error');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    showNotification('Error adding item to cart', 'error');
                }
            },
            error: function() {
                showNotification('Error adding item to cart', 'error');
            }
        });
    });
    
    // Function to update cart count in header
    function updateCartCount() {
        $.ajax({
            url: 'get_cart_count.php',
            type: 'GET',
            cache: false,
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = result.count;
                    }
                } catch (e) {
                    console.error('Error updating cart count:', e);
                }
            }
        });
    }
    
    // Function to collect all customization details
    function collectCustomizationDetails() {
        const itemId = document.getElementById('item_id').value;
        const quantity = parseInt(document.getElementById('portion').value) || 1;
        const specialInstructions = document.getElementById('special_instructions').value.trim();
        
        // Get selected toppings
        const selectedToppings = [];
        const toppingCheckboxes = document.querySelectorAll('input[name="toppings[]"]:checked');
        toppingCheckboxes.forEach(checkbox => {
            selectedToppings.push({
                id: checkbox.value,
                name: checkbox.parentElement.querySelector('label').textContent,
                price: parseFloat(checkbox.dataset.price)
            });
        });
        
        // Get removed ingredients
        const removedIngredients = [];
        const removeCheckboxes = document.querySelectorAll('input[name="remove[]"]:checked');
        removeCheckboxes.forEach(checkbox => {
            removedIngredients.push(checkbox.value);
        });
        
        // Create customization object
        return {
            item_id: itemId,
            quantity: quantity,
            details: {
                toppings: selectedToppings,
                removed_ingredients: removedIngredients,
                special_instructions: specialInstructions
            }
        };
    }
    
    // Function to save customization
    document.getElementById('save-customization-btn').addEventListener('click', function() {
        const itemId = this.getAttribute('data-item-id');
        const itemName = this.getAttribute('data-item-name');
        
        // Get customization details
        const customizationData = collectCustomizationDetails();
        
        // Prompt user for a name for this customization
        const customName = prompt('Give a name to your customization:', itemName + ' Custom');
        
        if (customName) {
            // Save customization to database
            $.ajax({
                url: 'save_customization.php',
                type: 'POST',
                data: {
                    item_id: itemId,
                    custom_name: customName,
                    customization_data: JSON.stringify(customizationData)
                },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            showNotification('Customization saved successfully!', 'success');
                        } else {
                            showNotification(result.message || 'Error saving customization', 'error');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        showNotification('Error saving customization', 'error');
                    }
                },
                error: function() {
                    showNotification('Error saving customization', 'error');
                }
            });
        }
    });
    
    // Function to load saved customizations
    document.getElementById('load-customization-btn').addEventListener('click', function() {
        const itemId = this.getAttribute('data-item-id');
        
        // Load saved customizations for this item
        $.ajax({
            url: 'get_saved_customizations.php',
            type: 'GET',
            data: {
                item_id: itemId
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success && result.customizations.length > 0) {
                        // Show modal with saved customizations
                        showCustomizationsModal(result.customizations);
                    } else {
                        showNotification('No saved customizations found', 'info');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    showNotification('Error loading customizations', 'error');
                }
            },
            error: function() {
                showNotification('Error loading customizations', 'error');
            }
        });
    });
    
    // Function to show customizations modal
    function showCustomizationsModal(customizations) {
        // Create modal container
        const modalContainer = document.createElement('div');
        modalContainer.className = 'customization-modal';
        
        // Create modal content
        let modalContent = `
            <div class="customization-modal-content">
                <span class="close-modal">&times;</span>
                <h3>Your Saved Customizations</h3>
                <div class="saved-customizations-list">
        `;
        
        // Add each customization
        customizations.forEach(custom => {
            modalContent += `
                <div class="saved-customization-item" data-id="${custom.id}">
                    <div class="custom-info">
                        <h4>${custom.custom_name}</h4>
                        <p>${formatCustomizationSummary(custom.customization_data)}</p>
                    </div>
                    <div class="custom-actions">
                        <button class="load-custom-btn" data-id="${custom.id}">Load</button>
                        <button class="delete-custom-btn" data-id="${custom.id}">Delete</button>
                    </div>
                </div>
            `;
        });
        
        modalContent += `
                </div>
            </div>
        `;
        
        // Add modal to page
        modalContainer.innerHTML = modalContent;
        document.body.appendChild(modalContainer);
        
        // Show modal
        setTimeout(() => {
            modalContainer.classList.add('show');
        }, 10);
        
        // Close modal when clicking on X
        modalContainer.querySelector('.close-modal').addEventListener('click', function() {
            closeCustomizationsModal(modalContainer);
        });
        
        // Close modal when clicking outside
        modalContainer.addEventListener('click', function(e) {
            if (e.target === modalContainer) {
                closeCustomizationsModal(modalContainer);
            }
        });
        
        // Load customization when clicking Load button
        modalContainer.querySelectorAll('.load-custom-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const customId = this.getAttribute('data-id');
                loadCustomization(customId);
                closeCustomizationsModal(modalContainer);
            });
        });
        
        // Delete customization when clicking Delete button
        modalContainer.querySelectorAll('.delete-custom-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const customId = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this customization?')) {
                    deleteCustomization(customId, this.closest('.saved-customization-item'));
                }
            });
        });
    }
    
    // Function to close customizations modal
    function closeCustomizationsModal(modalContainer) {
        modalContainer.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(modalContainer);
        }, 300);
    }
    
    // Function to format customization summary
    function formatCustomizationSummary(customData) {
        if (typeof customData === 'string') {
            customData = JSON.parse(customData);
        }
        
        let summary = '';
        
        // Add toppings
        if (customData.details.toppings && customData.details.toppings.length > 0) {
            summary += 'Toppings: ' + customData.details.toppings.map(t => t.name).join(', ');
        }
        
        // Add removed ingredients
        if (customData.details.removed_ingredients && customData.details.removed_ingredients.length > 0) {
            if (summary) summary += ' | ';
            summary += 'Removed: ' + customData.details.removed_ingredients.join(', ');
        }
        
        // Add quantity
        if (customData.quantity && customData.quantity > 1) {
            if (summary) summary += ' | ';
            summary += 'Quantity: ' + customData.quantity;
        }
        
        return summary || 'Standard customization';
    }
    
    // Function to load a customization
    function loadCustomization(customId) {
        $.ajax({
            url: 'get_customization.php',
            type: 'GET',
            data: {
                customization_id: customId
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        applyCustomization(result.customization);
                        showNotification('Customization loaded!', 'success');
                    } else {
                        showNotification('Error loading customization', 'error');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    showNotification('Error loading customization', 'error');
                }
            },
            error: function() {
                showNotification('Error loading customization', 'error');
            }
        });
    }
    
    // Function to apply a customization to the form
    function applyCustomization(customization) {
        const customData = typeof customization.customization_data === 'string' 
            ? JSON.parse(customization.customization_data) 
            : customization.customization_data;
        
        // Reset current selections
        document.querySelectorAll('input[name="toppings[]"]:checked').forEach(cb => cb.checked = false);
        document.querySelectorAll('input[name="remove[]"]:checked').forEach(cb => cb.checked = false);
        document.getElementById('special_instructions').value = '';
        
        // Set quantity
        const portionInput = document.getElementById('portion');
        portionInput.value = customData.quantity || 1;
        document.getElementById('quantity-display').textContent = portionInput.value;
        
        // Set toppings
        if (customData.details.toppings && customData.details.toppings.length > 0) {
            customData.details.toppings.forEach(topping => {
                const toppingCheckbox = document.querySelector(`input[name="toppings[]"][value="${topping.id}"]`);
                if (toppingCheckbox) {
                    toppingCheckbox.checked = true;
                }
            });
        }
        
        // Set removed ingredients
        if (customData.details.removed_ingredients && customData.details.removed_ingredients.length > 0) {
            customData.details.removed_ingredients.forEach(ingredient => {
                const ingredientSelector = `input[name="remove[]"][value="${ingredient.replace(/"/g, '\"')}"]`;
                const ingredientCheckbox = document.querySelector(ingredientSelector);
                if (ingredientCheckbox) {
                    ingredientCheckbox.checked = true;
                }
            });
        }
        
        // Set special instructions
        if (customData.details.special_instructions) {
            document.getElementById('special_instructions').value = customData.details.special_instructions;
        }
        
        // Update price calculation
        updateTotalPrice();
    }
    
    // Function to delete a customization
    function deleteCustomization(customId, element) {
        $.ajax({
            url: 'delete_customization.php',
            type: 'POST',
            data: {
                customization_id: customId
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        // Remove element from DOM
                        element.remove();
                        showNotification('Customization deleted!', 'success');
                        
                        // If no more customizations, close modal
                        const list = document.querySelector('.saved-customizations-list');
                        if (list && list.children.length === 0) {
                            const modal = document.querySelector('.customization-modal');
                            if (modal) {
                                closeCustomizationsModal(modal);
                            }
                            showNotification('No more saved customizations', 'info');
                        }
                    } else {
                        showNotification('Error deleting customization', 'error');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    showNotification('Error deleting customization', 'error');
                }
            },
            error: function() {
                showNotification('Error deleting customization', 'error');
            }
        });
    }
    
    // Function to show notification
    function showNotification(message, type) {
        const notificationContainer = document.getElementById('notification-container');
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        notificationContainer.appendChild(notification);
        
        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notificationContainer.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    // Function to update total price
    function updateTotalPrice() {
        const basePrice = parseFloat(document.getElementById('base_price').value);
        const quantity = parseInt(document.getElementById('portion').value) || 1;
        
        // Calculate toppings price
        let toppingsPrice = 0;
        const selectedToppings = document.querySelectorAll('input[name="toppings[]"]:checked');
        selectedToppings.forEach(topping => {
            toppingsPrice += parseFloat(topping.dataset.price || 0);
        });
        
        // Update toppings price display
        document.getElementById('toppings-price').textContent = 'Rs. ' + toppingsPrice.toFixed(2);
        
        // Calculate total price
        const totalPrice = (basePrice + toppingsPrice) * quantity;
        
        // Update total price display
        document.getElementById('total-price').textContent = 'Rs. ' + totalPrice.toFixed(2);
    }
    
    // Add event listeners for quantity buttons
    document.querySelector('.minus-btn').addEventListener('click', function() {
        const portionInput = document.getElementById('portion');
        if (parseInt(portionInput.value) > 1) {
            portionInput.value = parseInt(portionInput.value) - 1;
            document.getElementById('quantity-display').textContent = portionInput.value;
            updateTotalPrice();
        }
    });
    
    document.querySelector('.plus-btn').addEventListener('click', function() {
        const portionInput = document.getElementById('portion');
        if (parseInt(portionInput.value) < 10) {
            portionInput.value = parseInt(portionInput.value) + 1;
            document.getElementById('quantity-display').textContent = portionInput.value;
            updateTotalPrice();
        }
    });
    
    // Add event listeners for topping checkboxes
    document.querySelectorAll('input[name="toppings[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateTotalPrice);
    });
    
    // Initialize price calculation
    updateTotalPrice();
    </script>
    
    <style>
    /* Styles for customization modal */
    .customization-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .customization-modal.show {
        opacity: 1;
        visibility: visible;
    }
    
    .customization-modal-content {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        width: 90%;
        max-width: 600px;
        max-height: 80vh;
        overflow-y: auto;
        position: relative;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }
    
    .customization-modal.show .customization-modal-content {
        transform: translateY(0);
    }
    
    .close-modal {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
        color: #777;
        transition: color 0.3s ease;
    }
    
    .close-modal:hover {
        color: #ff6b00;
    }
    
    .saved-customizations-list {
        margin-top: 20px;
    }
    
    .saved-customization-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #eee;
        transition: background-color 0.3s ease;
    }
    
    .saved-customization-item:hover {
        background-color: #f9f9f9;
    }
    
    .saved-customization-item:last-child {
        border-bottom: none;
    }
    
    .custom-info {
        flex: 1;
    }
    
    .custom-info h4 {
        margin: 0 0 5px 0;
        color: #333;
    }
    
    .custom-info p {
        margin: 0;
        color: #777;
        font-size: 14px;
    }
    
    .custom-actions {
        display: flex;
        gap: 10px;
    }
    
    .load-custom-btn, .delete-custom-btn {
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .load-custom-btn {
        background-color: #ff6b00;
        color: white;
    }
    
    .load-custom-btn:hover {
        background-color: #e55f00;
    }
    
    .delete-custom-btn {
        background-color: #f2f2f2;
        color: #777;
    }
    
    .delete-custom-btn:hover {
        background-color: #ff3b30;
        color: white;
    }
    
    /* Notification styles */
    #notification-container {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1000;
    }
    
    .notification {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        margin-bottom: 10px;
        padding: 15px;
        width: 300px;
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .notification.show {
        transform: translateX(0);
        opacity: 1;
    }
    
    .notification-content {
        display: flex;
        align-items: center;
    }
    
    .notification-content i {
        margin-right: 10px;
        font-size: 20px;
    }
    
    .notification.success i {
        color: #4cd964;
    }
    
    .notification.error i {
        color: #ff3b30;
    }
    
    .notification.info i {
        color: #007aff;
    }
    
    /* Customization buttons styles */
    .customization-buttons {
        display: flex;
        gap: 10px;
        margin: 10px 0;
    }
    
    .customization-btn {
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .customization-btn i {
        margin-right: 8px;
    }
    
    .save-customization-btn {
        background-color: #4cd964;
        color: white;
    }
    
    .save-customization-btn:hover {
        background-color: #3cc153;
    }
    
    .load-customization-btn {
        background-color: #007aff;
        color: white;
    }
    
    .load-customization-btn:hover {
        background-color: #0066cc;
    }
    </style>
</body>
</html>
