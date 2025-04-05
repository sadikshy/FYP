

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600;1,700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/Login.css"> 
</head>
<body>
    <div class="form-container">
        <h2>Welcome Back</h2>
        <form method="POST" action="login_progress.php">  
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                <a href="#" class="float-right" id="forgot-password">Forget Password?</a>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <div class="text-center mt-3">
            <p>OR</p>
            <p>Don't have an account?</p>
            <a href="signup.php">Create account</a>
        </div>
    </div>

    <div id="otp-modal">
        <div class="modal-content">
            <h2>Verification</h2>
            <button id="close-otp-modal" style="position: absolute; top: 10px; right: 10px; border: none; background: none; cursor: pointer; font-size: 30px;">&times;</button>
            
            <!-- Email input container - can be hidden when auto-sending OTP -->
            <div id="email-input-container">
                <input type="text" id="otp-email" placeholder="Enter email">
                <button id="send-otp">Send OTP</button>
            </div>
            
            <div id="otp-verification-container">
                <input type="text" id="otp-code" placeholder="Enter OTP">
                <button id="verify-otp">Verify</button>
                <p id="otp-message"></p>
            </div>
        </div>
    </div>
    <div id="reset-password-modal" style="display: none;">
        <div class="modal-content">
            <h2>Reset Password</h2>
            <button id="close-reset-modal" style="position: absolute; top: 10px; right: 10px; border: none; background: none; cursor: pointer;">&times;</button>
            <input type="password" id="new-password" placeholder="New Password">
            <input type="password" id="confirm-password" placeholder="Confirm Password">
            <button id="reset-password-btn">Reset Password</button>
            <p id="reset-message"></p>
        </div>
    </div>
    </div>
    <script src="js/Loginscript.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>