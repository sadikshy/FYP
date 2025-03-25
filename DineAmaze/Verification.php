<?php
// Start the session
session_start();

// Check if takeout order details exist in session
if (!isset($_SESSION['takeout_order'])) {
    // Redirect to takeout page if no order details found
    header("Location: Takeout.php");
    exit();
}

// Get order details from session
$order = $_SESSION['takeout_order'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification - DineAmaze</title>
    <link rel="stylesheet" href="Homepage.css">
    <link rel="stylesheet" href="Verification.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="verification-section">
        <h2>Verification to Confirm TakeOut</h2>
        <form class="verification-form" method="POST" action="verification_process.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($order['fullName']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($order['email']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="contactNumber">Contact No.</label>
                <input type="tel" id="contactNumber" name="contactNumber" value="<?php echo htmlspecialchars($order['contactNumber']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="citizenship">Picture of Citizenship/Passport/National Id</label>
                <input type="file" name="id_document" id="id_document" required>
                <div class="citizenship-preview"></div>
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

    <script>
        // Preview uploaded ID document
        document.getElementById('id_document').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('.citizenship-preview');
                    preview.innerHTML = `<img src="${e.target.result}" alt="ID Document Preview" style="max-width: 100%; max-height: 200px;">`;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>