<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}

// Include database connection
include 'includes/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $user_id = $_SESSION['user_id'];
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $review_text = isset($_POST['review']) ? trim($_POST['review']) : '';
    
    // Validate data
    if ($rating < 1 || $rating > 5) {
        $_SESSION['review_error'] = "Please select a valid rating (1-5 stars).";
        header("Location: ClientReview.php");
        exit();
    }
    
    if (empty($review_text)) {
        $_SESSION['review_error'] = "Please enter your review text.";
        header("Location: ClientReview.php");
        exit();
    }
    
    // Current date for review submission
    $review_date = date('Y-m-d');
    
    // Check if user has already submitted a review
    $check_sql = "SELECT review_id FROM review WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User has a review, update it
        $row = $result->fetch_assoc();
        $review_id = $row['review_id'];
        
        $update_sql = "UPDATE review SET rating = ?, review_text = ?, review_date = ? WHERE review_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("issi", $rating, $review_text, $review_date, $review_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['review_success'] = "Your review has been updated successfully!";
        } else {
            $_SESSION['review_error'] = "Error updating your review. Please try again.";
        }
        $update_stmt->close();
    } else {
        
        $insert_sql = "INSERT INTO review (user_id, rating, review_text, review_date) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iiss", $user_id, $rating, $review_text, $review_date);
        
        if ($insert_stmt->execute()) {
            $_SESSION['review_success'] = "Your review has been submitted successfully!";
        } else {
            $_SESSION['review_error'] = "Error submitting your review. Please try again.";
        }
        $insert_stmt->close();
    }
    
    $check_stmt->close();
    $conn->close();
    
    // Redirect back to the reviews page
    header("Location: ClientReview.php");
    exit();
} else {
    // If not a POST request, redirect to the reviews page
    header("Location: ClientReview.php");
    exit();
}
?>