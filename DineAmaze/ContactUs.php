<?php
// Start the session
session_start();

// Include database connection
include 'includes/db_connection.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['contact_error'] = "You must be logged in to send a message.";
    } else {
        // Get form data
        $user_id = $_SESSION['user_id'];
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        
        // Validate data
        $errors = [];
        
        if (empty($message)) {
            $errors[] = "Message is required";
        }
        
        // If no errors, save to database
        if (empty($errors)) {
            $sql = "INSERT INTO contact_message (user_id, message) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $user_id, $message);
            
            if ($stmt->execute()) {
                $_SESSION['contact_success'] = "Your message has been sent successfully. We'll get back to you soon!";
            } else {
                $_SESSION['contact_error'] = "Error sending message. Please try again later.";
            }
            
            $stmt->close();
            
            // Redirect to avoid form resubmission
            header("Location: ContactUs.php");
            exit;
        } else {
            $_SESSION['contact_error'] = implode("<br>", $errors);
        }
    }
}
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
                <?php if(isset($_SESSION['contact_success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['contact_success']; 
                    unset($_SESSION['contact_success']);
                    ?>
                </div>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['contact_error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    echo $_SESSION['contact_error']; 
                    unset($_SESSION['contact_error']);
                    ?>
                </div>
                <?php endif; ?>
                
                <form action="ContactUs.php" method="post">
                    <!-- Updated Let's Chat heading to match the second image -->
                    <h2 class="lets-chat-heading">Let's <span class="chat-text">Chat</span></h2>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="form-group user-info">
                            <p>Sending message as: <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong></p>
                        </div>
                        <div class="form-group">
                            <textarea name="message" placeholder="Your Message" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>
                        <button type="submit" class="send-message-btn">SEND MESSAGE</button>
                    <?php else: ?>
                        <div class="login-required">
                            <p>You need to <a href="Login.php">log in</a> to send a message.</p>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>