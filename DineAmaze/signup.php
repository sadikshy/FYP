

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/Signup.css"> 
    <style>
        .error-message {
            display: none;
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .form-control.is-valid {
            border-color: #28a745;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .email-status {
            position: absolute;
            right: 10px;
            top: 10px;
            display: none;
        }
        .email-status.checking {
            display: inline-block;
            color: #6c757d;
        }
        .email-status.available {
            display: inline-block;
            color: #28a745;
        }
        .email-status.taken {
            display: inline-block;
            color: #dc3545;
        }
        .form-group {
            position: relative;
        }
        .alert {
            display: none;
            margin-bottom: 20px;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 40px;
            cursor: pointer;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-image"></div>
        <div class="form-container">
            <h2>Create an Account</h2>
            <!-- Error messages will be displayed inline with the fields -->
            <form method="POST" action="signup_progress.php" id="signupForm" novalidate>
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" class="form-control <?php echo (isset($_GET['errors']['name'])) ? 'is-invalid' : ''; ?>" id="fullName" name="fullName" placeholder="Enter full name" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>">
                    <?php if(isset($_GET['errors']['name'])): ?>
                        <div class="error-message" style="display:block; color:#dc3545;">
                            <?php echo htmlspecialchars($_GET['errors']['name']); ?>
                        </div>
                    <?php else: ?>
                        <div class="error-message" id="fullNameError">Name must be at least 3 characters long</div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control <?php echo (isset($_GET['error']) && $_GET['error'] == 'email_exists') ? 'is-invalid' : ''; ?>" id="email" name="email" placeholder="Enter email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                    <?php if(isset($_GET['error']) && $_GET['error'] == 'email_exists'): ?>
                        <div class="error-message" style="display:block; color:#dc3545;">
                            This email is already registered. Please use a different email.
                        </div>
                    <?php else: ?>
                        <div class="error-message" id="emailError">Please enter a valid email address</div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control <?php echo (isset($_GET['errors']['password'])) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Password">
                    <?php if(isset($_GET['errors']['password'])): ?>
                        <div class="error-message" style="display:block; color:#dc3545;">
                            <?php echo htmlspecialchars($_GET['errors']['password']); ?>
                        </div>
                    <?php else: ?>
                        <div class="error-message" id="passwordError">Password must be at least 8 characters long and include numbers and letters</div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" class="form-control <?php echo (isset($_GET['errors']['confirm_password'])) ? 'is-invalid' : ''; ?>" id="confirmPassword" name="confirmPassword" placeholder="Confirm password">
                    <?php if(isset($_GET['errors']['confirm_password'])): ?>
                        <div class="error-message" style="display:block; color:#dc3545;">
                            <?php echo htmlspecialchars($_GET['errors']['confirm_password']); ?>
                        </div>
                    <?php else: ?>
                        <div class="error-message" id="confirmPasswordError">Passwords do not match</div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" class="form-control <?php echo (isset($_GET['errors']['phone'])) ? 'is-invalid' : ''; ?>" id="phone_number" name="phone_number" placeholder="Enter phone number" value="<?php echo isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : ''; ?>">
                    <?php if(isset($_GET['errors']['phone'])): ?>
                        <div class="error-message" style="display:block; color:#dc3545;">
                            <?php echo htmlspecialchars($_GET['errors']['phone']); ?>
                        </div>
                    <?php else: ?>
                        <div class="error-message" id="phoneError">Please enter a valid phone number</div>
                    <?php endif; ?>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input <?php echo (isset($_GET['errors']['terms'])) ? 'is-invalid' : ''; ?>" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to DineAmaze's <a href="terms.php" target="_blank">Terms & Conditions</a>
                    </label>
                    <?php if(isset($_GET['errors']['terms'])): ?>
                        <div class="error-message" style="display:block; color:#dc3545;">
                            <?php echo htmlspecialchars($_GET['errors']['terms']); ?>
                        </div>
                    <?php else: ?>
                        <div class="error-message" id="termsError">You must accept the terms and conditions</div>
                    <?php endif; ?>
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
            
            // Confirm Password validation
            const confirmPassword = document.getElementById('confirmPassword').value;
            if (password !== confirmPassword) {
                document.getElementById('confirmPasswordError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('confirmPasswordError').style.display = 'none';
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