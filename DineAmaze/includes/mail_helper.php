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

/**
 * Send order pickup notification email
 * 
 * @param string $recipientEmail Email address of the recipient
 * @param string $userName Name of the user
 * @param string $orderId Order ID
 * @param array $orderDetails Order details array
 * @param string $orderStatus Current order status
 * @return bool True if email sent successfully, false otherwise
 */
function sendOrderPickupNotification($recipientEmail, $userName, $orderId, $orderDetails, $orderStatus) {
    // Check if order is verified
    if ($orderStatus !== 'verified') {
        error_log("Order #$orderId is not verified. Email not sent.");
        return false;
    }
    
    // Calculate time difference
    $orderTime = strtotime($orderDetails['order_date']);
    $currentTime = time();
    $timeDifference = $orderTime - $currentTime;
    
    // Only send notification if it's 10 minutes before order time
    if ($timeDifference < 600 || $timeDifference > 660) { // Between 10-11 minutes before pickup
        error_log("Order #$orderId notification not sent - not within 10-minute window. Time difference: " . ($timeDifference/60) . " minutes");
        return false;
    }
    
    $mail = new PHPMailer(true); // Enable exceptions
    
    try {
        // Server settings - reusing the same settings as OTP email
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sadikshyamunankarmi7@gmail.com';
        $mail->Password   = 'vavdcnxrimfmtwxb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('sadikshyamunankarmi7@gmail.com', 'DineAmaze');
        $mail->addAddress($recipientEmail, $userName);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'DineAmaze - Your Order #' . $orderId . ' is Ready for Pickup!';
        
        // Format order time
        $pickupTime = date('h:i A', $orderTime);
        
        // HTML layout for order pickup notification
        $mail->Body = '
        <div style="font-family: \'Poppins\', Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); background: linear-gradient(to right, #f8f9fa, #e9ecef);">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #764ba2; margin-bottom: 5px;">DineAmaze</h1>
                <p style="color: #4CAF50; font-size: 18px; margin-top: 0;">Order Ready for Pickup</p>
            </div>
            
            <div style="background-color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <p style="color: #333; font-size: 16px; line-height: 1.5;">Hello ' . $userName . ',</p>
                <p style="color: #333; font-size: 16px; line-height: 1.5;">Your order is verified and ready for pickup! Please collect your order within 10 minutes of your scheduled time.</p>
                
                <div style="background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin: 20px 0;">
                    <h3 style="color: #764ba2; margin-top: 0;">Order Details</h3>
                    <p><strong>Order ID:</strong> #' . $orderId . '</p>
                    <p><strong>Item:</strong> ' . $orderDetails['item_name'] . '</p>
                    <p><strong>Quantity:</strong> ' . $orderDetails['quantity'] . '</p>
                    <p><strong>Price:</strong> Rs. ' . $orderDetails['price'] . '</p>
                    <p><strong>Pickup Time:</strong> ' . $pickupTime . '</p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <div style="font-size: 18px; font-weight: bold; padding: 15px; background-color: #4CAF50; color: white; border-radius: 8px; display: inline-block;">
                        Please arrive at ' . $pickupTime . ' to collect your order
                    </div>
                </div>
                
                <p style="color: #333; font-size: 16px; line-height: 1.5;">Thank you for choosing DineAmaze. We hope you enjoy your meal!</p>
            </div>
            
            <div style="text-align: center; color: #666; font-size: 14px;">
                <p>© 2024 DineAmaze. All rights reserved.</p>
                <p>Srijananagar, Bhaktapur</p>
            </div>
        </div>
        ';
        
        $mail->AltBody = 'Your order #' . $orderId . ' is ready for pickup at ' . $pickupTime . '. Please collect your order within 10 minutes of your scheduled time. Item: ' . $orderDetails['item_name'] . ', Quantity: ' . $orderDetails['quantity'] . ', Price: Rs. ' . $orderDetails['price'];
        
        $mail->send();
        error_log("Pickup notification sent for order #$orderId");
        return true;
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Order pickup notification email failed: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send notification email when admin responds to a contact message
 * 
 * @param string $recipientEmail Email address of the recipient
 * @param string $userName Name of the user
 * @param string $originalMessage User's original message
 * @param string $adminResponse Admin's response to the message
 * @return bool True if email sent successfully, false otherwise
 */
function sendContactResponseNotification($recipientEmail, $userName, $originalMessage, $adminResponse) {
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
        $mail->Subject = 'DineAmaze - Response to Your Inquiry';
        
        // Format the original message and response for display
        $formattedOriginalMessage = nl2br(htmlspecialchars($originalMessage));
        $formattedAdminResponse = nl2br(htmlspecialchars($adminResponse));
        
        // Sweet HTML layout for response notification email
        $mail->Body = '
        <div style="font-family: \'Poppins\', Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); background: linear-gradient(to right, #f8f9fa, #e9ecef);">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #764ba2; margin-bottom: 5px;">DineAmaze</h1>
                <p style="color: #4CAF50; font-size: 18px; margin-top: 0;">Message Response</p>
            </div>
            
            <div style="background-color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <p style="color: #333; font-size: 16px; line-height: 1.5;">Hello ' . htmlspecialchars($userName) . ',</p>
                <p style="color: #333; font-size: 16px; line-height: 1.5;">Thank you for contacting us. We have responded to your message:</p>
                
                <div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #ccc; border-radius: 4px;">
                    <p style="color: #666; font-size: 14px; margin-bottom: 5px;"><strong>Your message:</strong></p>
                    <p style="color: #333; font-size: 15px;">' . $formattedOriginalMessage . '</p>
                </div>
                
                <div style="margin: 20px 0; padding: 15px; background-color: #f0f7ff; border-left: 4px solid #764ba2; border-radius: 4px;">
                    <p style="color: #666; font-size: 14px; margin-bottom: 5px;"><strong>Our response:</strong></p>
                    <p style="color: #333; font-size: 15px;">' . $formattedAdminResponse . '</p>
                </div>
                
                <p style="color: #333; font-size: 16px; line-height: 1.5;">If you have any further questions, please don\'t hesitate to contact us again.</p>
            </div>
            
            <div style="text-align: center; color: #666; font-size: 14px;">
                <p>© 2024 DineAmaze. All rights reserved.</p>
                <p>Srijananagar, Bhaktapur</p>
            </div>
        </div>
        ';
        
        $mail->AltBody = "Hello $userName,\n\nThank you for contacting us. We have responded to your message:\n\nYour message: $originalMessage\n\nOur response: $adminResponse\n\nIf you have any further questions, please don't hesitate to contact us again.\n\nBest regards,\nDineAmaze Team";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Contact response email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>
