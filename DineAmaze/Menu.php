<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - DineAmaze</title>
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/Menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-solid-straight/css/uicons-solid-straight.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-chubby/css/uicons-solid-chubby.css'>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="hero-section">
        <h1 class="hero-title">Our Menu</h1>
        <div>
            <span class="diamond"></span>
            <span class="hero-subtitle">Cooking Since</span>
            <span class="diamond"></span>
        </div>
    </div>

    <div class="menu-container">
        <div class="menu-sidebar">
            <div class="menu-filter">
                <h3>Categories</h3>
                <div class="filter-item active" data-filter="all">
                    <i class="fas fa-utensils"></i>
                    <span>All Items</span>
                </div>
                <div class="filter-item" data-filter="nepali">
                    <i class="fi fi-sc-plate"></i>
                    <span>Traditional Nepali Meals & Platters</span>
                </div>
                <div class="filter-item" data-filter="street">
                    <i class="fas fa-hotdog"></i>
                    <span>Street Food & Quick Bites</span>
                </div>
                <div class="filter-item" data-filter="snacks">
                    <i class="fas fa-hamburger"></i>
                    <span>Pizza, Burger & Snacks</span>
                </div>
                <div class="filter-item" data-filter="noodles">
                    <i class="fi fi-ss-bowl-chopsticks-noodles"></i>
                    <span>Cozy Bowls & Noodles Delights</span>
                </div>
                <div class="filter-item" data-filter="desserts">
                    <i class="fas fa-ice-cream"></i>
                    <span>Desserts</span>
                </div>
                <div class="filter-item" data-filter="beverages">
                    <i class="fas fa-mug-hot"></i>
                    <span>Beverages</span>
                </div>
            </div>
        </div>
        
        <div class="menu-content">
            <div class="menu-category" data-category="nepali">
                <h3>1. Traditional Nepali Meals and Platters</h3>
                <div class="dish-grid">
                    <div class="dish-item">
                        <img src="images/Menu Photos/Veg Khana set.jpg" alt="Veg Khana Set">
                        <h4>Dal Bhat Tarkari - Rs. 250</h4>
                        <p>Ingredients: Rice, Lentils (Dal), Mixed Vegetables (Carrot, Potato, Cabbage), Ghee, Spices (Turmeric, Cumin, Coriander), Papad, Salad, Pickle</p>
                    </div>
                    <div class="dish-item">
                        <img src="images/Menu Photos/MOMO.jpg" alt="Momo">
                        <h4>Momo - Rs. 150</h4>
                        <p>Ingredients: Flour, Chicken (or Vegetable), Cabbage, Garlic, Ginger, Soy Sauce, Spices</p>
                    </div>
                    <div class="dish-item">
                        <img src="images/Menu Photos/Sel Roti.jpg" alt="Sel Roti">
                        <h4>Sel Roti - Rs. 100</h4>
                        <p>Ingredients: Rice Flour, Sugar, Yogurt, Ghee, Cardamom</p>
                    </div>
                    <div class="dish-item">
                        <img src="images/Menu Photos/Samaye Baji.jpg" alt="Newari Khaja Set">
                        <h4>Newari Khaja Set - Rs. 350</h4>
                        <p>Ingredients: Beaten Rice (Chiura), Buff Sukuti, Egg, Aalu Tama, Bhatmas Sadeko, Chhwela, Spinach, Pickles, Dried fish fry, Bara</p>
                    </div>
                    <div class="dish-item">
                        <img src="images/Menu Photos/Non-Veg Khana Set.jpg" alt="Non-Veg Khana Set">
                        <h4>Nepali Thali - Rs. 300</h4>
                        <p>Ingredients: Rice, Dal, Vegetable Curry, Chicken (or Mutton), Pickle, Salad, Raita, Papad</p>
                    </div>
                </div>
            </div>

            <div class="menu-category" data-category="street">
                <h3>2. Street Food and Quick Bites</h3>
                <div class="dish-grid">
                    <div class="dish-item">
                        <img src="images/Menu Photos/Pakoda.jpg" alt="Pakoda">
                        <h4>Pakodi - Rs. 80</h4>
                        <p>Ingredients: Chickpea Flour, Potatoes, Onion, Spinach, Cumin, Coriander, Turmeric</p>
                    </div>
                    <div class="dish-item">
                        <img src="images/Menu Photos/Papadi Chaat.jpg" alt="Papadi Chaat">
                        <h4>Chaat - Rs. 100</h4>
                        <p>Ingredients: Fried Bread (Puri), Potatoes, Yogurt, Tamarind, Spices (Cumin, Chaat Masala, Salt)</p>
                    </div>
                    <div class="dish-item">
                        <img src="images/Menu Photos/Pani Puri.jpg" alt="Pani Puri">
                        <h4>Pani Puri - Rs. 85</h4>
                        <p>Ingredients: Puffed Wheat (Puri), Tamarind Water, Potato, Chickpeas, Spices</p>
                    </div>
                    <div class="dish-item">
                        <img src="images/Menu Photos/Samosa.jpg" alt="Samosa">
                        <h4>Samosa - Rs. 60</h4>
                        <p>Ingredients: Flour, Potatoes, Peas, Cumin, Turmeric, Coriander</p>
                    </div>
                    <div class="dish-item">
                        <img src="images/Menu Photos/Pizza Roll.jpg" alt="Pizza Roll">
                        <h4>Pizza Roll - Rs. 150</h4>
                        <p>Ingredients: Pizza Dough, Cheese, Tomato Sauce, Chicken (or Veggies), Herbs</p>
                    </div>
                </div>
            </div>

            <div class="menu-category" data-category="snacks">
                <h3>3. Pizza, Burgers, and Snacks</h3>
                <div class="dish-grid">
                    <!-- Add your pizza, burgers and snacks items here with the same structure -->
                </div>
            </div>

            <div class="menu-category" data-category="noodles">
                <h3>4. Cozy Bowls and Noodles Delights</h3>
                <div class="dish-grid">
                    <!-- Add your noodles and bowls items here with the same structure -->
                </div>
            </div>

            <div class="menu-category" data-category="desserts">
                <h3>5. Desserts</h3>
                <div class="dish-grid">
                    <!-- Add your dessert items here with the same structure -->
                </div>
            </div>

            <div class="menu-category" data-category="beverages">
                <h3>6. Beverages</h3>
                <div class="dish-grid">
                    <!-- Add your beverage items here with the same structure -->
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript for filtering -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterItems = document.querySelectorAll('.filter-item');
            const menuCategories = document.querySelectorAll('.menu-category');
            
            filterItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all filter items
                    filterItems.forEach(filter => filter.classList.remove('active'));
                    
                    // Add active class to clicked filter
                    this.classList.add('active');
                    
                    const filterValue = this.getAttribute('data-filter');
                    
                    // Show/hide menu categories based on filter
                    if (filterValue === 'all') {
                        menuCategories.forEach(category => {
                            category.style.display = 'block';
                        });
                    } else {
                        menuCategories.forEach(category => {
                            if (category.getAttribute('data-category') === filterValue) {
                                category.style.display = 'block';
                            } else {
                                category.style.display = 'none';
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>