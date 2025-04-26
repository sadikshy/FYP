console.log("Script executed!");


// Show OTP modal when "Forget Password" is clicked
document.getElementById('forgot-password').addEventListener('click', function(event) {
    event.preventDefault();
    
    // Get the email from login form
    const loginEmail = document.getElementById('email').value.trim();
    
    if (loginEmail) {
        // Set the email in the OTP modal
        document.getElementById('otp-email').value = loginEmail;
        
        // Hide the email input container
        document.getElementById('email-input-container').style.display = 'none';
        
        // Show a message that OTP is being sent
        document.getElementById('otp-message').textContent = 'Sending OTP...';
        document.getElementById('otp-message').style.color = '#333';
        
        // Auto-trigger the send OTP function
        setTimeout(function() {
            // Create and configure the request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'login_progress.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (data.success) {
                                document.getElementById('otp-message').textContent = 'OTP sent to your email';
                                document.getElementById('otp-message').style.color = 'green';
                                
                                // Focus on OTP input field
                                document.getElementById('otp-code').focus();
                            } else {
                                // If error, show email input again
                                document.getElementById('email-input-container').style.display = 'block';
                                
                                document.getElementById('otp-message').textContent = data.message;
                                document.getElementById('otp-message').style.color = 'red';
                            }
                        } catch (e) {
                            // If error, show email input again
                            document.getElementById('email-input-container').style.display = 'block';
                            
                            console.error('Error parsing JSON:', e);
                            console.log('Server response:', xhr.responseText);
                            document.getElementById('otp-message').textContent = 'Server error. Please try again later.';
                            document.getElementById('otp-message').style.color = 'red';
                        }
                    } else {
                        // If error, show email input again
                        document.getElementById('email-input-container').style.display = 'block';
                        
                        document.getElementById('otp-message').textContent = 'Server error: ' + xhr.status;
                        document.getElementById('otp-message').style.color = 'red';
                    }
                }
            };
            
            // Set up form data
            const formData = new FormData();
            formData.append('action', 'send_otp');
            formData.append('email', loginEmail);
            
            // Send the request
            xhr.send(formData);
        }, 500);
    } else {
        // If no email is entered in the login form, show the email input in the OTP modal
        document.getElementById('email-input-container').style.display = 'block';
    }
    
    document.getElementById('otp-modal').style.display = 'block';
});

// Event listener for closing the OTP modal
document.getElementById('close-otp-modal').addEventListener('click', function() {
    document.getElementById('otp-modal').style.display = 'none';
    document.getElementById('otp-message').textContent = '';
    document.getElementById('otp-email').value = '';
    document.getElementById('otp-code').value = '';
    
    // Reset the visibility of email input container
    document.getElementById('email-input-container').style.display = 'block';
});

// Keep the rest of your existing code below this point
// Event listener for closing the Reset Password modal
document.getElementById('close-reset-modal').addEventListener('click', function() {
    document.getElementById('reset-password-modal').style.display = 'none';
    document.getElementById('reset-message').textContent = '';
    document.getElementById('new-password').value = '';
    document.getElementById('confirm-password').value = '';
});

// Event listener for sending OTP
document.getElementById('send-otp').addEventListener('click', function() {
    // Your existing code for the send-otp button
    const email = document.getElementById('otp-email').value.trim();
    
    if (!email) {
        document.getElementById('otp-message').textContent = 'Please enter your email';
        document.getElementById('otp-message').style.color = 'red';
        return;
    }
    
    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        document.getElementById('otp-message').textContent = 'Please enter a valid email address';
        document.getElementById('otp-message').style.color = 'red';
        return;
    }
    
    // Show loading indicator
    document.getElementById('otp-message').textContent = 'Sending OTP...';
    document.getElementById('otp-message').style.color = '#333';
    
    // Use XMLHttpRequest instead of fetch for better compatibility
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'login_progress.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        document.getElementById('otp-message').textContent = data.message;
                        document.getElementById('otp-message').style.color = 'green';
                        
                        // Focus on OTP input field
                        document.getElementById('otp-code').focus();
                    } else {
                        document.getElementById('otp-message').textContent = data.message;
                        document.getElementById('otp-message').style.color = 'red';
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    console.log('Server response:', xhr.responseText);
                    document.getElementById('otp-message').textContent = 'Server error. Please try again later.';
                    document.getElementById('otp-message').style.color = 'red';
                }
            } else {
                document.getElementById('otp-message').textContent = 'Server error: ' + xhr.status;
                document.getElementById('otp-message').style.color = 'red';
            }
        }
    };
    
    // Set up form data
    const formData = new FormData();
    formData.append('action', 'send_otp');
    formData.append('email', email);
    
    // Send the request
    xhr.send(formData);
});

// Event listener for verifying OTP
document.getElementById('verify-otp').addEventListener('click', function() {
    const userOtp = document.getElementById('otp-code').value.trim();
    const userEmail = document.getElementById('otp-email').value.trim(); // Get the email
    
    if (!userOtp) {
        alert('Please enter the OTP sent to your email');
        return;
    }
    
    // Use XMLHttpRequest for better compatibility
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'login_progress.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); // Add this line
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        // OTP verified, show reset password modal
                        document.getElementById('otp-modal').style.display = 'none';
                        document.getElementById('reset-password-modal').style.display = 'block';
                        
                        // Store email in session storage for later use
                        sessionStorage.setItem('reset_email', userEmail);
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    console.log('Server response:', xhr.responseText);
                    alert('Server error. Please try again later.');
                }
            } else {
                alert('Server error: ' + xhr.status);
            }
        }
    };
    
    // Set up form data with proper encoding - include email
    const formData = 'action=verify_otp&otp=' + encodeURIComponent(userOtp) + 
                    '&email=' + encodeURIComponent(userEmail);
    
    // Send the request
    xhr.send(formData);
});

// Event listener for reset password button
document.getElementById('reset-password-btn').addEventListener('click', function() {
    const newPassword = document.getElementById('new-password').value.trim();
    const confirmPassword = document.getElementById('confirm-password').value.trim();
    const resetMessage = document.getElementById('reset-message');
    const userEmail = sessionStorage.getItem('reset_email'); // Get email from session storage
    
    console.log("Email for reset:", userEmail); // Debug line
    console.log("Password:", newPassword); // Debug line
    
    // Clear previous error messages
    resetMessage.textContent = '';
    
    // Validate passwords
    if (!newPassword || !confirmPassword) {
        resetMessage.textContent = 'Please enter both passwords';
        resetMessage.style.color = 'red';
        return;
    }
    
    if (newPassword !== confirmPassword) {
        resetMessage.textContent = 'Passwords do not match';
        resetMessage.style.color = 'red';
        return;
    }
    
    // Special characters are causing issues - modify the regex to allow special characters
    const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;
    if (!passwordRegex.test(newPassword)) {
        resetMessage.textContent = 'Password must be at least 8 characters and contain both letters and numbers';
        resetMessage.style.color = 'red';
        return;
    }
    
    // Show loading message
    resetMessage.textContent = 'Resetting password...';
    resetMessage.style.color = '#333';
    
    // Use XMLHttpRequest for better compatibility
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'login_progress.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); // Add this line
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        resetMessage.textContent = data.message;
                        resetMessage.style.color = 'green';
                        
                        // Clear session storage
                        sessionStorage.removeItem('reset_email');
                        
                        // Close the reset password modal and redirect to login page after 3 seconds
                        setTimeout(function() {
                            document.getElementById('reset-password-modal').style.display = 'none';
                            window.location.href = 'Login.php';
                        }, 3000);
                    } else {
                        resetMessage.textContent = data.message;
                        resetMessage.style.color = 'red';
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    console.log('Server response:', xhr.responseText);
                    resetMessage.textContent = 'Server error. Please try again later.';
                    resetMessage.style.color = 'red';
                }
            } else {
                resetMessage.textContent = 'Server error: ' + xhr.status;
                resetMessage.style.color = 'red';
            }
        }
    };
    
    // Set up form data with proper encoding and include email
    const formData = 'action=reset_password' + 
                    '&new_password=' + encodeURIComponent(newPassword) + 
                    '&confirm_password=' + encodeURIComponent(confirmPassword) +
                    '&email=' + encodeURIComponent(userEmail);
    
    // Send the request
    xhr.send(formData);
});

