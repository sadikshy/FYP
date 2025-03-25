<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineAmaze - Client Reviews</title>
    <link rel="stylesheet" href="ClientReview.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="banner">Banner Section</div>
    <section class="client-reviews">
        <h3>Client Review</h3>
        <div class="reviews">
            <div class="review-card">★★★★★ <br> Review 1</div>
            <div class="review-card">★★★★★ <br> Review 2</div>
            <div class="review-card">★★★★★ <br> Review 3</div>
            <div class="review-card">★★★★★ <br> Review 4</div>
            <div class="review-card">★★★★★ <br> Review 5</div>
            <div class="review-card">★★★★★ <br> Review 6</div>
            <div class="review-card">★★★★★ <br> Review 7</div>
            <div class="review-card">★★★★★ <br> Review 8</div>
        </div>
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
</body>
</html>
