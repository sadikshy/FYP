

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/Signup.css"> 
</head>
<body>
    <div class="signup-container">
        <div class="signup-image"></div>
        <div class="form-container">
            <h2>Create an Account</h2>
            <form method="POST" action="signup_progress.php" id="signupForm" novalidate>
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Enter full name">
                    <div class="error-message" id="fullNameError">Name must be at least 3 characters long</div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                    <div class="error-message" id="emailError">Please enter a valid email address</div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    <div class="error-message" id="passwordError">Password must be at least 8 characters long and include numbers and letters</div>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter phone number">
                    <div class="error-message" id="phoneError">Please enter a valid phone number</div>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to DineAmaze's <a href="terms.php" target="_blank">Terms & Conditions</a>
                    </label>
                    <div class="error-message" id="termsError">You must accept the terms and conditions</div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Create account</button>
            </form>
            <div class="text-center mt-3">
                <p>OR</p>
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
   
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Full Name validation
            const fullName = document.getElementById('fullName').value.trim();
            if (fullName.length < 3) {
                document.getElementById('fullNameError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('fullNameError').style.display = 'none';
            }
            
            // Email validation
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('emailError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('emailError').style.display = 'none';
            }
            
            // Password validation
            const password = document.getElementById('password').value;
            const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
            if (!passwordRegex.test(password)) {
                document.getElementById('passwordError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('passwordError').style.display = 'none';
            }
            
            // Phone validation
            const phone = document.getElementById('phone_number').value.trim();
            const phoneRegex = /^\d{10}$/;
            if (!phoneRegex.test(phone)) {
                document.getElementById('phoneError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('phoneError').style.display = 'none';
            }
            
            // Terms validation
            const terms = document.getElementById('terms').checked;
            if (!terms) {
                document.getElementById('termsError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('termsError').style.display = 'none';
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>