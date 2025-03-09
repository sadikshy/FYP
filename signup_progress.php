<?php
// signup.php

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
    $name = $_POST['fullName'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $phone_number = $_POST['phone_number']; // Assuming you add a phone_number field to your form

    // Sanitize input (important for security)
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $phone_number = mysqli_real_escape_string($conn, $phone_number);

    // Generate a unique user ID (you might want a more robust method)
    $userId = uniqid(); 

    $sql = "INSERT INTO user (user_id, name, email, password, phone_number) 
            VALUES ('$userId', '$name', '$email', '$password', '$phone_number')";

    if ($conn->query($sql) === TRUE) {
        echo "<div class='success-message'>Signup successful! <a href='login.php'>Login now</a></div>";
    } else {
        echo "<div class='error-message'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }
}

// Close the connection (optional, as the script will end here anyway)
$conn->close();
?>

<style>
.success-message {
    color: rgb(23, 34, 26);
    background-color: #d4edda;
    border-color: #c3e6cb;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid transparent;
    border-radius: 4px;
    font-family: "Poppins", sans-serif;
    font-size: 30px;
    margin: 20px;
    padding: 20px;
    text-align: center;
    animation: popup 0.5s ease-in-out;
}

@keyframes popup {
    0% {
        transform: scale(0.5);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.error-message {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid transparent;
    border-radius: 4px;
    font-family: Arial, sans-serif;
}
</style>