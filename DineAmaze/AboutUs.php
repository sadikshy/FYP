<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - DineAmaze</title>
    
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/AboutUs.css">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section with Who We Are -->
    <div class="hero-section">
        <div class="hero-overlay">
            <h2>About Us</h2>
            <p class="tagline">Cooking Since 2023</p>
        </div>
    </div>

    <!-- About Us Section -->
    <div class="about-section">
        <div class="section-title">
            <h2>About<span>Us</span></h2>
        </div>
        <p class="about-description">
        Welcome to DineAmaze, your go-to place for delicious and authentic food! We offer a variety of meals, including Traditional Nepali Platters, Street Food, Pizza, Burgers, Cozy Bowls, Desserts, and Beverages.<br>
        At DineAmaze, we focus on quality, taste, and hygiene in every dish. Our food customization feature lets you personalize your meal just the way you like it. To ensure safety, we verify all customized orders with a citizenship photo or national ID card before preparation.<br>
        Whether you're in the mood for a quick bite, a hearty meal, or a sweet treat, our team is here to serve you with great flavors and excellent service. Every visit to DineAmaze is a memorable dining experience!
    </div>

    <!-- Our Mission Section -->
    <div class="mission-section">
        <div class="mission-container">
            <div class="mission-images">
                <div class="image-grid">
                    <img src="images/AboutUs/chef-cooking.jpg" alt="Chef cooking" class="grid-image img1">
                    <img src="images/AboutUs/Food.jpg" alt="Our team" class="grid-image img2">
                    <img src="images/AboutUs/food-prep.jpg" alt="Food preparation" class="grid-image img3">
                </div>
            </div>
            <div class="mission-content">
                <h2> <span>Our</span>  Mission</h2>
                <p>At DineAmaze, our mission is to make every meal delicious and memorable. We believe food is more than just eating—it’s an experience to enjoy. That’s why we use fresh, high-quality ingredients to serve everything from Traditional Nepali Meals to Pizza, Burgers, and Street Food.</p>
                <p>We provide a clean and cozy space and let you customize your meal while keeping it safe with ID verification. Whether you're dining in or picking up, we ensure fast service and notify you 10 minutes before your order is ready. At DineAmaze, we promise great taste, hygiene, and a warm welcome every time.</p>
                <p>We believe in using only the freshest ingredients and authentic recipes to create dishes that transport you to Nepal with every bite.</p>
            </div>
        </div>
    </div>

    <!-- Why To Choose Us Section -->
    <div class="why-choose-section">
        <div class="section-title">
            <h2><span>Why</span> To Choose Us</h2>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <i class="fi fi-rr-restaurant"></i>
                <h3>Authentic & Customizable Meals</h3>
                <p>Our dishes are prepared using traditional recipes and cooking methods to ensure authentic flavors, while allowing you to customize each meal to your preferences.</p>
            </div>
            
            <div class="feature-card">
                <i class="fi fi-rr-leaf"></i>
                <h3>Fresh & Quality Ingredients</h3>
                <p>We choose fresh local ingredients to prepare dishes that are not only delicious but also nutritious.</p>
            </div>
            
            <div class="feature-card">
                <i class="fi fi-rr-shield-check"></i>
                <h3>Secure Customization Process</h3>
                <p>We verify all customized orders with ID verification to ensure safety and accuracy in your personalized meal preparation.</p>
            </div>
            
            <div class="feature-card">
                <i class="fi fi-rr-home"></i>
                <h3>Hygienic & Cozy Ambience</h3>
                <p>Our restaurant offers a clean, cozy, and welcoming environment that makes you feel right at home while enjoying your meal.</p>
            </div>
            
            <div class="feature-card">
                <i class="fi fi-rr-alarm-clock"></i>
                <h3>Timely Notifications & Easy Pickup</h3>
                <p>We ensure fast service and notify you 10 minutes before your order is ready, making pickup convenient and efficient.</p>
            </div>
            
            
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
