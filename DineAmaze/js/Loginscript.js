console.log("Script executed!");
document.getElementById('forgot-password').addEventListener('click', function(event) {
    event.preventDefault();
    document.getElementById('otp-modal').style.display = 'block';
});

// Event listener for closing the OTP modal
document.getElementById('close-otp-modal').addEventListener('click', function() {
    document.getElementById('otp-modal').style.display = 'none';
});

// Event listener for closing the Reset Password modal
document.getElementById('close-reset-modal').addEventListener('click', function() {
    document.getElementById('reset-password-modal').style.display = 'none';
});

// Event listener for sending OTP
document.getElementById('send-otp').addEventListener('click', function() {
    const email = document.getElementById('otp-email').value.trim();
    const users = JSON.parse(localStorage.getItem('users')) || [];

    console.log('Checking Email:', email);
    console.log('Stored Users:', users);

   

    const otp = (Math.floor(100000 + Math.random() * 900000)).toString();
   

    console.log('Generated OTP:', otp);
    document.getElementById('otp-message').textContent = 'OTP is sent to your E-mail';
});

// Event listener for verifying OTP
document.getElementById('verify-otp').addEventListener('click', function() {
    const userOtp = document.getElementById('otp-code').value.trim();
   

    console.log('User OTP:', userOtp);
    console.log('Stored OTP:', storedOtp);

    if (!storedEmail || !storedOtp) {
        alert('Session expired. Please request OTP again.');
        return;
    }

    if (userOtp === storedOtp) {
        document.getElementById('otp-modal').style.display = 'none';
        document.getElementById('reset-password-modal').style.display = 'block';
    } else {
        alert('Invalid OTP. Please try again.');
    }
});
// Event listener for login form submission




