<?php
// login_progress.php

// Start the session (if you're using sessions)
session_start();

// Database connection details
$servername = "localhost";  // e.g., localhost
$username = "root";
$password = "";
$dbname = "dineamaze_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Login successful
            $_SESSION['user_id'] = $row['user_id']; // Store user ID in session
            $_SESSION['user_name'] = $row['name']; // Store user name for display
            
            echo '<div class="success-message">Login successful! Redirecting...</div>';
            echo '<style>
                    .success-message {
                        font-size: 24px;
                        color: rgb(80, 174, 83);
                        text-align: center;
                        margin-top: 20px;
                        padding: 10px;
                        border: 2px solid rgba(0, 0, 0, 0);
                        border-radius: 5px;
                        background-color: #e8f5e9;
                        display: inline-block;
                        animation: fadeIn 1s ease-in-out forwards;
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                    }
                    
                    @keyframes fadeIn {
                        0% {
                            opacity: 0;
                        }
                        100% {
                            opacity: 1;
                        }
                    }
                  </style>';
            
            // Check if there's a redirect destination
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']); // Clear the redirect
                echo '<meta http-equiv="refresh" content="2;url=' . $redirect . '">';
            } else {
                // Default redirect to homepage
                echo '<meta http-equiv="refresh" content="2;url=Homepage.php">';
            }
            
        } else {
            echo '<div class="error-message">Incorrect password.</div>';
        }
    } else {
        echo '<div class="error-message">User not found.</div>';
    }
    
    $stmt->close();
}

$conn->close();
?>
