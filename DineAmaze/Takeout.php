<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}

// Get user's email and verification status for the form
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];
$sql = "SELECT email, is_verified, name, phone_number FROM user WHERE user_id = '$userId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_phone'] = $user['phone_number'];
    $_SESSION['is_verified'] = $user['is_verified'] ?? 0;
}
$conn->close();

// Check if cart is empty
$cart_is_empty = !isset($_SESSION['cart']) || empty($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TakeOut - DineAmaze</title>
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/Takeout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="takeout-hero">
        <div class="hero-content">
            <h1>TAKEOUT</h1>
            <div class="tagline-container">
                <span class="tagline">Order & Enjoy</span> <br> <br>
                <span class="tagline1">TakeOut are available</span> <br> <br>
                <span class="tagline1">Mon-Sun: 11AM-10PM</span>
            
        
            </div>
        </div>
    </div>
    <section class="takeout-section">
        <?php if($cart_is_empty): ?>
        <!-- Empty Cart Notification -->
        <div class="empty-cart-notification">
            <i class="fas fa-shopping-cart"></i>
            <h3>Your cart is empty!</h3>
            <p>Please add some dishes to your cart before proceeding with takeout.</p>
            <a href="Menu.php" class="browse-menu-btn">Browse Menu</a>
        </div>
        <?php else: ?>
           
        <!-- Menu Selection Section -->
        <div class="menu-selection">
            <div class="browse-menu-message">
                <i class="fas fa-utensils"></i>
                <h3>Select Items for Takeout</h3>
                <p>Please browse our menu and select items you'd like to order for takeout.</p>
                <a href="Menu.php" class="browse-menu-btn">Browse Menu</a>
            </div>
        </div>

        
        </div>
        </div>
        
        <!-- Add an order summary section -->
        <!-- Add this section to display cart items -->
        <div class="order-summary">
            <h2>Order Summary</h2>
            <table class="order-items-table">
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['cart'] as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td class="item-name"><?php echo $item['name']; ?></td>
                        <td class="item-quantity">x<?php echo $item['quantity']; ?></td>
                        <td class="item-price">Rs. <?php echo number_format($item['price'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2">Total:</td>
                        <td>Rs. <?php echo number_format($total, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form class="takeout-form" id="takeoutForm" method="POST" action="takeout_process.php" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" placeholder="Enter your full name" required>
                    <div class="error-message" id="fullNameError">Name must be at least 3 characters long</div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" placeholder="Enter your email" required readonly>
                    <div class="error-message" id="emailError">Please enter a valid email address</div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="contactNumber">Contact Number</label>
                    <input type="tel" id="contactNumber" name="contactNumber" value="<?php echo htmlspecialchars($_SESSION['user_phone'] ?? ''); ?>" placeholder="Enter your contact number" required>
                    <div class="error-message" id="contactNumberError">Please enter a valid 10-digit phone number</div>
                </div>
                <div class="form-group">
                    <label for="time">Time</label>
                    <input type="time" id="time" name="time" required>
                    <div class="error-message" id="timeError">Please select a valid time between 11 AM and 10 PM</div>
                </div>
            </div>
            <?php if (isset($_SESSION['is_verified']) && $_SESSION['is_verified'] == 1): ?>
            <!-- User is already verified, show proceed button -->
            <div class="verified-user-notice">
                <i class="fas fa-check-circle"></i>
                <p>Your account has been verified. You can proceed with your order.</p>
            </div>
            <button type="submit">Proceed with Order</button>
            <input type="hidden" name="skip_verification" value="1">
            <?php else: ?>
            <!-- User is not verified, show verify button -->
            <button type="submit">Verify</button>
            <?php endif; ?>
        </form>
        <!-- Add this script before the closing body tag -->
        <script>
            document.getElementById('takeoutForm').addEventListener('submit', function(e) {
                let isValid = true;
                
                // Full Name validation
                const fullName = document.getElementById('fullName').value.trim();
                if (fullName.length < 3) {
                    document.getElementById('fullNameError').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('fullNameError').style.display = 'none';
                }
                
                // Email validation - now readonly but still validate format
                const email = document.getElementById('email').value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    document.getElementById('emailError').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('emailError').style.display = 'none';
                }
                
                // Contact Number validation
                const contactNumber = document.getElementById('contactNumber').value.trim();
                const phoneRegex = /^\d{10}$/;
                if (!phoneRegex.test(contactNumber)) {
                    document.getElementById('contactNumberError').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('contactNumberError').style.display = 'none';
                }
                
                // Time validation
                const time = document.getElementById('time').value;
                const selectedTime = new Date(`2000-01-01T${time}`);
                const openTime = new Date(`2000-01-01T11:00`);
                const closeTime = new Date(`2000-01-01T22:00`);
                
                if (!time || selectedTime < openTime || selectedTime > closeTime) {
                    document.getElementById('timeError').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('timeError').style.display = 'none';
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        </script>
        <?php endif; ?>
    </section>

    <?php include 'includes/footer.php';?>

    <style>
        /* Empty cart notification styling */
        .empty-cart-notification {
            background-color: linear-gradient(to right, #fff8f3, #fff1e6);;
            border-left: 4px solid #ff6b00;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .empty-cart-notification i {
            font-size: 48px;
            color: #ff6b00;
            margin-bottom: 15px;
        }
        
        .empty-cart-notification h3 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .empty-cart-notification p {
            color: #856404;
            margin-bottom: 20px;
        }
        
        .browse-menu-btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .browse-menu-btn:hover {
            background-color: #45a049;
        }

        /* Existing styles */
        .filter-section {
            background-color: #f0f8f0;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .filter-section h3 {
            color: #4CAF50;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .filter-options {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-group select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
        }
        
        .filter-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .filter-button:hover {
            background-color: #45a049;
        }
        
        .menu-selection {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .dish-image {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #4CAF50;
        }
        
        .dish-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .dish-details {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }
        
        .dish-details select {
            margin-left: auto;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>

    <script>
        // Filter functionality
        document.getElementById('applyFilter').addEventListener('click', function() {
            const category = document.getElementById('categoryFilter').value;
            const price = document.getElementById('priceFilter').value;
            const dietary = document.getElementById('dietaryFilter').value;
            
            // Get all menu items
            const menuItems = document.querySelectorAll('.menu-item');
            
            menuItems.forEach(item => {
                let showItem = true;
                const itemName = item.querySelector('label').textContent;
                const itemPrice = parseInt(itemName.match(/Rs\.\s*(\d+)/)[1]);
                
                // Filter by category
                if (category !== 'all') {
                    // Check if item belongs to selected category
                    const parentCategory = item.closest('.category').querySelector('h3').textContent.toLowerCase();
                    const itemNameLower = itemName.toLowerCase();
                    
                    // Special case for Dal Bhat - consider it both Nepali and Popular
                    if (category === 'nepali' && itemNameLower.includes('dal bhat')) {
                        // Keep it visible for Nepali filter
                        showItem = true;
                    } 
                    else if (
                        (category === 'popular' && !parentCategory.includes('popular') && !itemNameLower.includes('dal bhat')) ||
                        (category === 'beverages' && !parentCategory.includes('beverages')) ||
                        (category === 'nepali' && !parentCategory.includes('nepali') && !itemNameLower.includes('dal bhat')) ||
                        (category === 'fast-food' && !parentCategory.includes('fast'))
                    ) {
                        showItem = false;
                    }
                }
                
                // Filter by price
                if (price !== 'all' && showItem) {
                    if (
                        (price === 'under100' && itemPrice >= 100) ||
                        (price === '100-200' && (itemPrice < 100 || itemPrice > 200)) ||
                        (price === 'above200' && itemPrice <= 200)
                    ) {
                        showItem = false;
                    }
                }
                
                // Filter by dietary preference
                if (dietary !== 'all' && showItem) {
                    // This is a simplified check - you might need more specific logic
                    const isVeg = !itemName.toLowerCase().includes('chicken') && 
                                 !itemName.toLowerCase().includes('mutton') &&
                                 !itemName.toLowerCase().includes('beef');
                    
                    if ((dietary === 'veg' && !isVeg) || (dietary === 'non-veg' && isVeg)) {
                        showItem = false;
                    }
                }
                
                // Show or hide the item based on filters
                item.style.display = showItem ? 'flex' : 'none';
            });
            
            // Check if any category is empty after filtering
            document.querySelectorAll('.category').forEach(category => {
                // Count visible items more reliably
                let hasVisibleItems = false;
                const items = category.querySelectorAll('.menu-item');
                
                for (let i = 0; i < items.length; i++) {
                    if (items[i].style.display !== 'none') {
                        hasVisibleItems = true;
                        break;
                    }
                }
                
                category.style.display = hasVisibleItems ? 'block' : 'none';
            });
        });
        
        // Reset filters
        const resetButton = document.createElement('button');
        resetButton.textContent = 'Reset Filters';
        resetButton.className = 'filter-button reset-button';
        resetButton.style.backgroundColor = '#f44336';
        resetButton.style.marginLeft = '10px';
        
        document.querySelector('.filter-options').appendChild(resetButton);
        
        resetButton.addEventListener('click', function() {
            // Reset all filter dropdowns
            document.getElementById('categoryFilter').value = 'all';
            document.getElementById('priceFilter').value = 'all';
            document.getElementById('dietaryFilter').value = 'all';
            
            // Show all menu items and categories
            document.querySelectorAll('.menu-item').forEach(item => {
                item.style.display = 'flex';
            });
            
            document.querySelectorAll('.category').forEach(category => {
                category.style.display = 'block';
            });
        });
   

    // Add cart functionality
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    const quantitySelects = document.querySelectorAll('select[name^="quantity"]');
    const selectedItemsDiv = document.getElementById('selectedItems');
    const orderTotalSpan = document.getElementById('orderTotal');
    const emptyCartMessage = document.querySelector('.empty-cart-message');
    
    // Item prices mapping
    const itemPrices = {
        'Momo': 150,
        'Dal Bhat': 250,
        'Chicken Burger': 200,
        'Fresh Juice': 120,
        'Milkshake': 150
    };
    
    function updateOrderSummary() {
        let total = 0;
        let hasItems = false;
        let summaryHTML = '';
        
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                hasItems = true;
                const itemName = checkbox.value;
                const quantitySelect = document.querySelector(`select[name="quantity[${itemName}]"]`);
                const quantity = parseInt(quantitySelect.value);
                const price = itemPrices[itemName];
                const itemTotal = price * quantity;
                
                total += itemTotal;
                
                summaryHTML += `
                    <div class="summary-item">
                        <span class="item-name">${itemName}</span>
                        <span class="item-quantity">x${quantity}</span>
                        <span class="item-price">Rs. ${itemTotal}</span>
                    </div>
                `;
            }
        });
        
        if (hasItems) {
            emptyCartMessage.style.display = 'none';
            selectedItemsDiv.innerHTML = summaryHTML;
        } else {
            emptyCartMessage.style.display = 'block';
            selectedItemsDiv.innerHTML = '<p class="empty-cart-message">No items selected yet</p>';
        }
        
        orderTotalSpan.textContent = `Rs. ${total}`;
        
        // Add selected items to a hidden input for form submission
        const hiddenInput = document.getElementById('selectedItemsInput') || document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'selectedItemsData';
        hiddenInput.id = 'selectedItemsInput';
        hiddenInput.value = JSON.stringify(getSelectedItemsData());
        document.getElementById('takeoutForm').appendChild(hiddenInput);
    }
    
    function getSelectedItemsData() {
        const items = [];
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const itemName = checkbox.value;
                const quantitySelect = document.querySelector(`select[name="quantity[${itemName}]"]`);
                const quantity = parseInt(quantitySelect.value);
                const price = itemPrices[itemName];
                
                items.push({
                    name: itemName,
                    quantity: quantity,
                    price: price,
                    total: price * quantity
                });
            }
        });
        return items;
    }
    
    // Add event listeners
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateOrderSummary);
    });
    
    quantitySelects.forEach(select => {
        select.addEventListener('change', updateOrderSummary);
    });
    
    // Form validation
    document.getElementById('takeoutForm').addEventListener('submit', function(e) {
        const hasItems = Array.from(checkboxes).some(checkbox => checkbox.checked);
        
        if (!hasItems) {
            e.preventDefault();
            alert('Please select at least one item before proceeding.');
        }
    });
    </script>

<style>
        /* Existing styles */
        
        /* Order summary styles */
        .order-summary {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .order-summary h3 {
            color: #4CAF50;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .item-name {
            flex: 2;
        }
        
        .item-quantity {
            flex: 1;
            text-align: center;
        }
        
        .item-price {
            flex: 1;
            text-align: right;
            font-weight: bold;
        }
        
        .total-section {
            margin-top: 15px;
            text-align: right;
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .empty-cart-message {
            text-align: center;
            color: #888;
            font-style: italic;
        }
    </style>
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Store form data in session
    $_SESSION['takeout_order'] = [
        'fullName' => $_POST['fullName'],
        'email' => $_POST['email'],
        'contactNumber' => $_POST['contactNumber'],
        'pickupTime' => $_POST['pickupTime'],
        'specialInstructions' => $_POST['specialInstructions'],
        // Store cart items in the takeout order
        'cartItems' => $_SESSION['cart']
    ];
    
    // Redirect to verification page
    header("Location: Verification.php");
    exit();
}
?>