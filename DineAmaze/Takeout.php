<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TakeOut - DineAmaze</title>
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/Takeout.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="takeout-section">
        <div class="takeout-info">
            <h1>TakeOut are available</h1>
            <p>Mon-Sun: 10AM-10PM</p>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <h3>Filter Menu Items</h3>
            <div class="filter-options">
                <div class="filter-group">
                    <label>Category:</label>
                    <select id="categoryFilter">
                        <option value="all">All Categories</option>
                        <option value="popular">Popular Items</option>
                        <option value="beverages">Beverages</option>
                        <option value="nepali">Nepali Cuisine</option>
                        <option value="fast-food">Fast Food</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Price Range:</label>
                    <select id="priceFilter">
                        <option value="all">All Prices</option>
                        <option value="under100">Under Rs. 100</option>
                        <option value="100-200">Rs. 100 - Rs. 200</option>
                        <option value="above200">Above Rs. 200</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Dietary:</label>
                    <select id="dietaryFilter">
                        <option value="all">All Types</option>
                        <option value="veg">Vegetarian</option>
                        <option value="non-veg">Non-Vegetarian</option>
                    </select>
                </div>
                <button id="applyFilter" class="filter-button">Apply Filter</button>
            </div>
        </div>

        <!-- Menu Selection Section -->
        <div class="menu-selection">
            <h2>Select Items for Takeout</h2>
            <div class="menu-categories">
                <div class="category">
                    <h3>Popular Items</h3>
                    <div class="menu-items">
                        <div class="menu-item">
                            <div class="dish-image">
                                <img src="images/Menu Photos/MOMO.jpg" alt="Momo">
                            </div>
                            <div class="dish-details">
                                <input type="checkbox" id="item1" name="menuItems[]" value="Momo">
                                <label for="item1">Momo - Rs. 150</label>
                                <select name="quantity[Momo]">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                        </div>
                        <div class="menu-item">
                            <div class="dish-image">
                                <img src="images/Menu Photos/Veg Khana Set.jpg" alt="Veg Khana Set">
                            </div>
                            <div class="dish-details">
                                <input type="checkbox" id="item2" name="menuItems[]" value="Dal Bhat">
                                <label for="item2">Veg Khana Set - Rs. 250</label>
                                <select name="quantity[Dal Bhat]">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                        </div>
                        <div class="menu-item">
                            <div class="dish-image">
                                <img src="images/Menu Photos/Chicken Burger.jpg" alt="Chicken Burger">
                            </div>
                            <div class="dish-details">
                                <input type="checkbox" id="item3" name="menuItems[]" value="Chicken Burger">
                                <label for="item3">Chicken Burger - Rs. 200</label>
                                <select name="quantity[Chicken Burger]">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="category">
                    <h3>Beverages</h3>
                    <div class="menu-items">
                        <div class="menu-item">
                            <div class="dish-image">
                                <img src="images/Menu Photos/Fruit Juice.png" alt="Orange Juice">
                            </div>
                            <div class="dish-details">
                                <input type="checkbox" id="drink1" name="menuItems[]" value="Orange Juice">
                                <label for="drink1">Fresh Juice - Rs. 120</label>
                                <select name="quantity[Fresh Juice]">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                        </div>
                        <div class="menu-item">
                            <div class="dish-image">
                                <img src="images/Menu Photos/Vanilla Milkshake.jpg" alt="Vanilla Milkshake">
                            </div>
                            <div class="dish-details">
                                <input type="checkbox" id="drink2" name="menuItems[]" value="Vanilla Milkshake">
                                <label for="drink2">Vanilla Milkshake - Rs. 150</label>
                                <select name="quantity[Milkshake]">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- After the menu selection section, add a cart summary -->
        </div>
        </div>
        
        <!-- Add an order summary section -->
        <div class="order-summary" id="orderSummary">
        <h3>Order Summary</h3>
        <div id="selectedItems">
            <p class="empty-cart-message">No items selected yet</p>
        </div>
        <div class="total-section">
            <p>Total: <span id="orderTotal">Rs. 0</span></p>
        </div>
        </div>

        <form class="takeout-form" id="takeoutForm" method="POST" action="takeout_process.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="contactNumber">Contact Number</label>
                    <input type="tel" id="contactNumber" name="contactNumber" placeholder="Enter your contact number" required>
                </div>
                <div class="form-group">
                    <label for="time">Time</label>
                    <input type="time" id="time" name="time" required>
                </div>
            </div>
            <button type="submit">Verify</button>
        </form>
    </section>

    <footer>
        <div class="footer-content">
            <div class="nav-footer">
                <h3>Navigation</h3>
                <div class="nav-links">
                    <a href="Homepage.php">Home</a> | 
                    <a href="AboutUs.php">About Us</a> | 
                    <a href="Menu.php">Menu</a> | 
                    <a href="Customization.php">Customization</a> | 
                    <a href="Takeout.php">TakeOut</a> | 
                    <a href="ContactUs.php">Contact Us</a> | 
                    <a href="account_settings.php">My Account</a>
                </div>
            </div>
            <div class="contact-footer" id="contact">
                <h3>Contact Us</h3>
                <p>Email: DineAmaze@gmail.com</p>
                <p>Phone: 9861050118, 016675486</p>
                <p>Address: Srijananagar, Bhaktapur</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 DineAmaze. All rights reserved.</p>
        </div>
    </footer>

    <style>
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