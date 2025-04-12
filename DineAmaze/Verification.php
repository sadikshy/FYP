<?php
// Start the session
session_start();

// Check if takeout order details exist in session
if (!isset($_SESSION['takeout_order'])) {
    // Redirect to takeout page if no order details found
    header("Location: Takeout.php");
    exit();
}

// Check if user has already completed verification
if (isset($_SESSION['verification_completed']) && $_SESSION['verification_completed'] === true) {
    // Redirect to a waiting page
    header("Location: verification_status.php");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="verification-section">
        <h2>Verification to Confirm TakeOut</h2>
        
        <div class="verification-message">
            <p><i class="fas fa-info-circle"></i> Your order will be reviewed by our staff. You will receive a confirmation email once approved.</p>
        </div>
        
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
                <label for="citizenship">Front Picture of Citizenship/Passport/National Id</label>
                <input type="file" name="id_document" id="id_document" required accept="image/*">
                <div class="citizenship-preview"></div>
            </div>
            <div class="form-group">
                <label for="additional_document">Back Picture of Citizenship/Passport/National Id</label>
                <input type="file" name="additional_document" id="additional_document" accept="image/*,.pdf">
                <p class="file-hint">You have to  upload a Back side of ID or any supporting document</p>
                <div class="additional-doc-preview"></div>
            </div>
            <div class="form-group">
                <label for="notes">Special Notes (Optional)</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Any additional information you'd like to provide..."></textarea>
            </div>
            <div class="verification-status">
                <p>Your verification status: <span class="status-pending">Pending Submission</span></p>
            </div>
            <button type="submit">Submit for Verification</button>
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
        
        // Preview additional document
        document.getElementById('additional_document').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const preview = document.querySelector('.additional-doc-preview');
                
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Additional Document Preview" style="max-width: 100%; max-height: 200px;">`;
                    }
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    preview.innerHTML = `<div class="pdf-preview"><i class="fas fa-file-pdf"></i> ${file.name}</div>`;
                }
            }
        });
    </script>
</body>
</html>