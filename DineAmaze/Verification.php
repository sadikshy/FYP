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
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/Verification.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

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

    <?php include 'includes/footer.php'; ?>

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