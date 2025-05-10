<?php
// login_progress.php

// Start the session
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dineamaze_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Include the mail helper
require_once 'includes/mail_helper.php';

// Handle OTP generation and sending
if (isset($_POST['action']) && $_POST['action'] === 'send_otp') {
    $email = $_POST['email'];
    
    // Check if email exists in database
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Generate OTP
        $otp = rand(100000, 999999);
        
        // Store OTP in session
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['otp_time'] = time();
        
        // Send OTP via email
        if (sendOTPEmail($email, $otp)) {
            echo json_encode(['success' => true, 'message' => 'OTP sent to your email']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send OTP email. Please try again.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found in our records']);
    }
    exit;
}

// Handle OTP verification
if (isset($_POST['action']) && $_POST['action'] === 'verify_otp') {
    $userOtp = $_POST['otp'];
    
    if (isset($_SESSION['reset_otp']) && isset($_SESSION['otp_time'])) {
        // Check if OTP is expired (15 minutes)
        if (time() - $_SESSION['otp_time'] > 900) {
            echo json_encode(['success' => false, 'message' => 'OTP expired. Please request a new one.']);
            exit;
        }
        
        if ($userOtp == $_SESSION['reset_otp']) {
            echo json_encode(['success' => true, 'message' => 'OTP verified successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Session expired. Please request OTP again.']);
    }
    exit;
}

// Handle password reset
if (isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (!isset($_SESSION['reset_email'])) {
        echo json_encode(['success' => false, 'message' => 'Session expired. Please start over.']);
        exit;
    }
    
    if ($newPassword !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit;
    }
    
    $email = $_SESSION['reset_email'];
    
    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update password in database
    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);
    
    if ($stmt->execute()) {
        // Clear session variables
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_otp']);
        unset($_SESSION['otp_time']);
        
        echo json_encode(['success' => true, 'message' => 'Password reset successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reset password: ' . $conn->error]);
    }
    exit;
}

// Regular login process
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify password - check if passwords are stored as plain text or hashed
        if (password_verify($password, $row['password']) || $password === $row['password']) {
            // Login successful
            // Set session variables based on actual database column names
            $_SESSION['user_id'] = $row['user_id']; // Changed to match database column 'user_id'
            $_SESSION['user_name'] = $row['name']; // Changed to match database column 'name'
            $_SESSION['user_email'] = $row['email']; // This is correct
            $_SESSION['logged_in'] = true;
            
            // Add profile image to session if it exists
            if(!empty($row['profile_image']) && file_exists($row['profile_image'])) {
                $_SESSION['profile_image'] = $row['profile_image'];
            }
            
            // Redirect to success page
            header("Location: login_success.php");
            exit(); // Important to prevent further code execution
        } else {
            // Incorrect password
            $_SESSION['login_error'] = "Incorrect password.";
            header("Location: Login.php");
            exit();
        }
    } else {
        // No account found
        $_SESSION['login_error'] = "No account found with that email.";
        header("Location: Login.php");
        exit();
    }
}
?>