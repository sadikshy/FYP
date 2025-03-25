<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dine Time - Exquisite Dining Experience</title>
    <link rel="stylesheet" href="Homepage.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="hero">
        <div class="slider">
            <img src="Sliderimage1.jpg" alt="Delicious Pasta Dish">
            <img src="Sliderimage2.jpg" alt="Gourmet Burger with Fries">
            <img src="Sliderimage3.jpg" alt="Fresh Salad with Grilled Chicken">
        </div>
        <div class="hero-content">
            <h1>Experience Culinary Excellence at DineAmaze</h1>
            <p>Discover a world of flavors with our meticulously crafted dishes.</p>
            <a href="#menu" class="cta-button">View Our Menu</a>
        </div>
    </section>

    <section class="about" id="about">
        <h2>About DineAmaze</h2>
        <p>Welcome to DineAmaze, where we believe in creating memorable dining experiences. Our chefs are passionate about using the freshest ingredients to craft exquisite dishes that delight your senses. We pride ourselves on our commitment to quality and exceptional service.</p>
        <p>Our story began with a simple idea: to bring people together through the love of good food. We've grown into a beloved dining destination, known for our innovative menu and warm, inviting atmosphere. Whether you're here for a casual lunch, a romantic dinner, or a special celebration, we're dedicated to making your visit unforgettable.</p>
    </section>

    <section class="menu" id="menu">
        <h2>Our Delectable Menu</h2>
        <div class="menu-items">
            <div class="item">
                <img src="SignaturePasta.png" alt="Dish 1">
                <h3>Signature Pasta</h3>
                <p>Homemade pasta with rich tomato sauce and fresh basil.</p>
                <span class="price">Rs 970</span>
            </div>
            <div class="item">
                <img src="GourmetBurger.jpeg" alt="Dish 2">
                <h3>Gourmet Burger</h3>
                <p>Juicy beef patty with caramelized onions and special sauce.</p>
                <span class="price">Rs 830</span>
            </div>
            <div class="item">
                <img src="GrilledChickenSalad.jpg" alt="Dish 3">
                <h3>Grilled Chicken Salad</h3>
                <p>Fresh greens with grilled chicken, avocado, and citrus dressing.</p>
                <span class="price">Rs 740</span>
            </div>
            <div class="item">
                <img src="VegetarianPizza.jpg" alt="Dish 4">
                <h3>Vegetarian Pizza</h3>
                <p>Thin crust pizza with seasonal vegetables and mozzarella.</p>
                <span class="price">Rs 1200</span>
            </div>
            <div class="item">
                <img src="ChocolateLavaCake.jpg" alt="Dish 5">
                <h3>Chocolate Lava Cake</h3>
                <p>Warm chocolate cake with molten chocolate center and vanilla ice cream.</p>
                <span class="price">Rs 600</span>
            </div>
            <div class="item">
                <img src="FreshFruitSmoothie.jpeg" alt="Dish 6">
                <h3>Fresh Fruit Smoothie</h3>
                <p>Blend of seasonal fruits and yogurt, perfect for a refreshing treat.</p>
                <span class="price">Rs 500</span>
            </div>
        </div>
    </section>

    <section class="customization" id="customization">
        <h2>Customize Your Dining Experience</h2>
        <p>Make your meal uniquely yours! Choose from a variety of options to customize your order.</p>
        <button class="customize-button">Customize Your Order</button>
    </section>

    <section class="offers" id="offers">
        <h2>Special Offers & Promotions</h2>
        <p>Don't miss out on our exciting deals and promotions!</p>
        <div class="offer-items">
            <div class="offer">
                <h3>Weekday Lunch Special</h3>
                <p>Enjoy a set lunch menu at a special price from Monday to Friday.</p>
                <span class="offer-price">$9.99</span>
            </div>
            <div class="offer">
                <h3>Family Dinner Deal</h3>
                <p>Get a family-sized meal with a variety of dishes for a discounted price.</p>
                <span class="offer-price">$39.99</span>
            </div>
            <div class="offer">
                <h3>Happy Hour Drinks</h3>
                <p>Enjoy discounted drinks during our happy hour from 5 PM to 7 PM.</p>
                <span class="offer-price">50% Off</span>
            </div>
        </div>
    </section>

    <section class="reviews" id="reviews">
        <h2>What Our Customers Say</h2>
        <div class="review-items">
            <div class="review">
                <p>★★★★★ - "The food was absolutely delicious! The service was top-notch and the ambiance was perfect. Highly recommend!"</p>
                <span class="reviewer">- Sarah M.</span>
            </div>
            <div class="review">
                <p>★★★★★ - "DineAmaze is my new favorite spot! The menu is creative and the dishes are always cooked to perfection. A must-try!"</p>
                <span class="reviewer">- John D.</span>
            </div>
            <div class="review">
                <p>★★★★★ - "Great experience! The staff was friendly and attentive, and the food was incredible. Will definitely be coming back."</p>
                <span class="reviewer">- Emily L.</span>
            </div>
        </div>
    </section>

    <!-- At the end of your body content, before closing body tag -->
    
    <?php include 'footer.php'; ?>
    <script src="slider.js"></script>
</body>
</html>