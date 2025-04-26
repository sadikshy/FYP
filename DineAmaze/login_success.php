<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in - only check for user_email and logged_in since those are working
if (!isset($_SESSION['user_email']) || !isset($_SESSION['logged_in'])) {
    header("Location: Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Successful</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .success-container {
            max-width: 500px;
            width: 90%;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            padding: 30px;
            animation: containerFadeIn 0.8s ease-in-out;
        }
        .success-image {
            width: 70%;
            max-width: 300px;
            margin: 0 auto 20px;
            display: block;
            animation: imageFadeIn 1.2s ease-in-out;
        }
        h2 {
            color: #333;
            margin-top: 20px;
        }
        p {
            color: #666;
            margin: 15px 0;
        }
        .redirect-text {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
        }
        .user-name {
            font-weight: 600;
            color: #764ba2;
        }
        
        @keyframes containerFadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes imageFadeIn {
            0% { opacity: 0; }
            30% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
    <meta http-equiv="refresh" content="3;url=Homepage.php">
</head>
<body>
    <div class="success-container">
        <img src="images/Loginimage/LoginSucess.jpg" alt="Login Successful" class="success-image">
        <h2>Welcome Back, <span class="user-name">
            <?php 
            // Use email if name is not available
            echo !empty($_SESSION['user_name']) ? $_SESSION['user_name'] : $_SESSION['user_email']; 
            ?>
        </span>!</h2>
        <p>You have successfully logged in.</p>
        <p class="redirect-text">You will be redirected to the homepage in <span id="countdown">3</span> seconds...</p>
    </div>

    <script>
        // Countdown timer
        let seconds = 3;
        const countdownElement = document.getElementById('countdown');
        
        const countdownTimer = setInterval(function() {
            seconds--;
            countdownElement.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(countdownTimer);
            }
        }, 1000);
    </script>
</body>
</html>