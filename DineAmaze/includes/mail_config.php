<?php
/**
 * Mail configuration settings for DineAmaze
 * Used by PHPMailer for sending emails
 */

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer autoloader
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');  // Change to your SMTP server
define('SMTP_USERNAME', 'sadikshyamunankarmi7@gmail.com');  // Change to your email
define('SMTP_PASSWORD', 'vavdcnxrimfmtwxb');  // Change to your app password
define('SMTP_ENCRYPTION', 'tls');  // tls or ssl
define('SMTP_PORT', 587);  // 587 for TLS, 465 for SSL

// Sender information
define('SMTP_FROM_EMAIL', 'sadikshyamunankarmi7@gmail.com');  // Change to your email
define('SMTP_FROM_NAME', 'DineAmaze Restaurant');  // Change to your restaurant name
?>
