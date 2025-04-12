<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - DineAmaze</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/ContactUs.css">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="contact-hero">
        <div class="hero-content">
            <h1>CONTACT US</h1>
            <div class="tagline-container">
                <span class="tagline">Get In Touch</span>
            </div>
        </div>
    </div>
    
    <!-- Contact Form Section -->
    <div class="contact-form-section">
        <div class="contact-form-container">
            <div class="contact-form-text">
                <!-- Updated heading with two different colors -->
                <h3 class="contact-heading">Contact <span class="details-text">Details</span></h3>
                
                <!-- Restaurant Contact Details moved here -->
                <div class="contact-details-compact">
                    <div class="contact-row">
                        <span class="label">Phone:</span>
                        <span class="value">9861050118, 016675486</span>
                    </div>
                    <div class="contact-row">
                        <span class="label">Email:</span>
                        <span class="value">DineAmaze@gmail.com</span>
                    </div>
                    <div class="contact-row">
                        <span class="label">Address:</span>
                        <span class="value">Srijananagar, Bhaktapur</span>
                    </div>
                    <div class="contact-row">
                        <span class="label">Opening Hours:</span>
                        <span class="value">10Am-10Pm</span>
                    </div>
                    <div class="contact-row">
                        <span class="label">Take-Out Available:</span>
                        <span class="value">11Am-10Pm</span>
                    </div>
                </div>
            </div>
            
            <div class="contact-form">
                <form action="#" method="post">
                    <!-- Updated Let's Chat heading to match the second image -->
                    <h2 class="lets-chat-heading">Let's <span class="chat-text">Chat</span></h2>
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Your Message" required></textarea>
                    </div>
                    <button type="submit" class="send-message-btn">SEND MESSAGE</button>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>