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
    // Get form data
    $name = isset($_POST['fullName']) ? $_POST['fullName'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';
    $phone_number = isset($_POST['phone_number']) ? $_POST['phone_number'] : '';
    $terms = isset($_POST['terms']) ? true : false;
    
    // Initialize error array
    $errors = [];
    
    // Validate name (at least 3 characters)
    if (empty($name) || strlen($name) < 3) {
        $errors['name'] = "Name must be at least 3 characters long";
    }
    
    // Validate email format
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    }
    
    // Validate password (at least 8 characters with letters and numbers)
    if (empty($password) || !preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        $errors['password'] = "Password must be at least 8 characters long and include numbers and letters";
    }
    
    // Validate passwords match
    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = "Passwords do not match";
    }
    
    // Validate phone number (10 digits)
    if (empty($phone_number) || !preg_match('/^\d{10}$/', $phone_number)) {
        $errors['phone'] = "Please enter a valid 10-digit phone number";
    }
    
    // Validate terms acceptance
    if (!$terms) {
        $errors['terms'] = "You must accept the terms and conditions";
    }
    
    // If there are validation errors, redirect back with errors
    if (!empty($errors)) {
        $error_query = http_build_query([
            'errors' => $errors,
            'name' => $name,
            'email' => $email,
            'phone' => $phone_number
        ]);
        header("Location: signup.php?" . $error_query);
        exit();
    }
    
    // Sanitize input (important for security)
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $phone_number = mysqli_real_escape_string($conn, $phone_number);
    
    // Check if email already exists
    $checkEmailSql = "SELECT * FROM user WHERE email = '$email'";
    $result = $conn->query($checkEmailSql);
    
    if ($result->num_rows > 0) {
        // Redirect back to signup page with error parameter and the email
        header("Location: signup.php?error=email_exists&email=" . urlencode($email));
        exit();
    }
    
    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    
    
    $sql = "INSERT INTO user ( name, email, password, phone_number) 
            VALUES ( '$name', '$email', '$hashedPassword', '$phone_number')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success-message'>Signup successful! <a href='login.php'>Login now</a></div>";
        
        // Set session variable for successful signup
        $_SESSION['signup_success'] = true;
        
        // Redirect to success page
        header("Location: signup_success.php");
        exit();
    } else {
        echo "<div class='error-message'>Error: " . $conn->error . "</div>";
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
<?php

$_SESSION['signup_success'] = true;

// Redirect to success page
header("Location: signup_success.php");
exit();
?>