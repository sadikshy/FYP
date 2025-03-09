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

    // Sanitize input (important for security)
    $email = mysqli_real_escape_string($conn, $email);

    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Login successful
            $_SESSION['user_id'] = $row['user_id']; // Store user ID in session
            echo '<div class="success-message">Login successful! Redirecting to homepage...</div>';
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
                        left: 50%;
                        transform: translateX(-50%) scale(0);
                    }
                    @keyframes fadeIn {
                        from { 
                            top: 10%;
                            transform: translateX(-50%) scale(0.2);
                        }
                        to {
                            top: 50%;
                            transform: translateX(-50%) scale(1);
                        }
                    }
                  </style>';
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "Homepage.html";
                    }, 2000); // Redirect after 2 seconds
                  </script>';
        } else {
            echo '<div class="error-message">Incorrect password.</div>';
        }
    } else {
        echo '<div class="error-message">User not found.</div>';
    }
}

// Close the connection (optional, as the script will end here anyway)
$conn->close(); 
?>
