<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Successful</title>
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
            max-width: 600px;
            width: 90%;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            padding-bottom: 30px;
        }
        .success-image {
            width: 100%;
            height: auto;
            border-radius: 12px 12px 0 0;
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
        .btn-primary {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 15px;
        }
        .btn-primary:hover {
            background: linear-gradient(to right, #764ba2, #667eea);
        }
    </style>
    <meta http-equiv="refresh" content="5;url=Login.php">
</head>
<body>
    <div class="success-container">
        <img src="images/Loginimage/Register_sucess.jpg" alt="Registration Successful" class="success-image">
        <h2>Signup Successful!</h2>
        <p>Your account has been created successfully.</p>
        <a href="Login.php" class="btn btn-primary">Login now</a>
        <p class="redirect-text">You will be redirected to the login in <span id="countdown">5</span> seconds...</p>
    </div>

    <script>
        // Countdown timer
        let seconds = 5;
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