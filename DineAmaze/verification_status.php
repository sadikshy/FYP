<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}

// Check if verification has been submitted
if (!isset($_SESSION['verification_completed']) || $_SESSION['verification_completed'] !== true) {
    // Redirect to takeout page if verification not completed
    header("Location: Takeout.php");
    exit();
}

// Get verification status from database
$status = "pending"; // Default status
$status_message = "";

// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get verification status
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT status, notes FROM verification WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $verification = $result->fetch_assoc();
    $status = $verification['status'];
    $status_message = $verification['notes'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Status - DineAmaze</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/Verification.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="verification-section">
        <h2>Verification Status</h2>
        
        <div class="status-container">
            <?php if ($status == 'pending'): ?>
                <div class="status-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Verification Pending</h3>
                <p>Your verification is currently being reviewed by our team. This usually takes 1-2 business days.</p>
            <?php elseif ($status == 'under_review'): ?>
                <div class="status-icon review">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Under Review</h3>
                <p>Our team is currently reviewing your verification documents.</p>
            <?php elseif ($status == 'confirmed'): ?>
                <div class="status-icon confirmed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Verification Confirmed</h3>
                <p>Your verification has been approved! You can now place takeout orders.</p>
            <?php elseif ($status == 'rejected'): ?>
                <div class="status-icon rejected">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h3>Verification Rejected</h3>
                <p>Unfortunately, your verification was not approved. Please contact our support team for more information.</p>
            <?php endif; ?>
            
            <?php if (!empty($status_message)): ?>
                <div class="admin-message">
                    <h4>Message from Admin:</h4>
                    <p><?php echo htmlspecialchars($status_message); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="status-actions">
                <?php if ($status == 'rejected'): ?>
                    <a href="Verification.php?resubmit=1" class="action-button">Resubmit Verification</a>
                <?php endif; ?>
                <a href="Menu.php" class="action-button secondary">Browse Menu</a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>