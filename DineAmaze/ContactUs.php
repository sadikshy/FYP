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
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <br> <br>
    <div class="contact-container">
        <div class="contact-info">
            <h2>Restaurant Contact Details</h2>
            <div class="contact-details-table">
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
            </div>
        </div>

        <div class="restaurant-image">
            <img src="images/resturant.jpeg" alt="Restaurant Image">
        </div>
    </div>
    
    <!-- After your contact-container div -->
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
