<?php
// Include PHPMailer classes manually
require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendOTPEmail($recipientEmail, $otp) {
    $mail = new PHPMailer(true); // Enable exceptions
    
    try {
        // Server settings
        $mail->SMTPDebug = 0;                      // Enable verbose debug output (set to 2 for debugging)
        $mail->isSMTP();                           // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';      // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                  // Enable SMTP authentication
        $mail->Username   = 'sadikshyamunankarmi7@gmail.com'; // SMTP username
        $mail->Password   = 'vavdcnxrimfmtwxb';    // SMTP password (app password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
        $mail->Port       = 587;                   // TCP port to connect to
        
        // Recipients
        $mail->setFrom('sadikshyamunankarmi7@gmail.com', 'DineAmaze');
        $mail->addAddress($recipientEmail);        // Add a recipient
        
        // Content
        $mail->isHTML(true);                       // Set email format to HTML
        $mail->Subject = 'Your DineAmaze Password Reset OTP';
        
        // Sweet HTML layout for OTP email
        $mail->Body = '
        <div style="font-family: \'Poppins\', Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); background: linear-gradient(to right, #f8f9fa, #e9ecef);">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #764ba2; margin-bottom: 5px;">DineAmaze</h1>
                <p style="color: #4CAF50; font-size: 18px; margin-top: 0;">Password Reset</p>
            </div>
            
            <div style="background-color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <p style="color: #333; font-size: 16px; line-height: 1.5;">Hello,</p>
                <p style="color: #333; font-size: 16px; line-height: 1.5;">We received a request to reset your password. Please use the following One-Time Password (OTP) to verify your identity:</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <div style="font-size: 28px; font-weight: bold; letter-spacing: 8px; padding: 15px; background: linear-gradient(to right, #764ba2, #667eea); color: white; border-radius: 8px; display: inline-block;">
                        ' . $otp . '
                    </div>
                </div>
                
                <p style="color: #333; font-size: 16px; line-height: 1.5;">This OTP is valid for 15 minutes. If you did not request a password reset, please ignore this email.</p>
            </div>
            
            <div style="text-align: center; color: #666; font-size: 14px;">
                <p>© 2024 DineAmaze. All rights reserved.</p>
                <p>Srijananagar, Bhaktapur</p>
            </div>
        </div>
        ';
        
        $mail->AltBody = 'Your OTP for password reset is: ' . $otp;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>
