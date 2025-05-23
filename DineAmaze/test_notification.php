<?php
/**
 * Test script to send a notification directly
 * This will help diagnose mail server issues
 */

// Set error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the notification system
require_once 'includes/file_notifications.php';

echo "<h1>Notification Test</h1>";

// Test data
$email = "test@example.com"; // Replace with your actual email for testing
$name = "Test User";
$orderId = 12345;
$orderDetails = [
    'pickup_time' => date('Y-m-d H:i:s', strtotime('+1 hour')),
    'items' => [
        [
            'name' => 'Test Pizza',
            'quantity' => 1,
            'price' => 10.99
        ],
        [
            'name' => 'Test Drink',
            'quantity' => 2,
            'price' => 2.50
        ]
    ]
];

// Test preparation notification
echo "<h2>Testing Preparation Notification</h2>";
$prepResult = sendOrderPreparationNotification($email, $name, $orderId, $orderDetails);
echo "<p>Preparation notification " . ($prepResult ? "sent successfully" : "failed to send") . "</p>";

// Test ready notification
echo "<h2>Testing Ready Notification</h2>";
$readyResult = sendOrderReadyNotification($email, $name, $orderId, $orderDetails);
echo "<p>Ready notification " . ($readyResult ? "sent successfully" : "failed to send") . "</p>";

// Check mail server configuration
echo "<h2>Mail Server Configuration</h2>";
echo "<pre>";
$mailConfig = ini_get('sendmail_path');
echo "sendmail_path: " . ($mailConfig ? $mailConfig : "Not configured") . "\n";

$smtpHost = ini_get('SMTP');
echo "SMTP host: " . ($smtpHost ? $smtpHost : "Not configured") . "\n";

$smtpPort = ini_get('smtp_port');
echo "SMTP port: " . ($smtpPort ? $smtpPort : "Not configured") . "\n";

// Try a direct mail() function call
echo "\n<h2>Direct mail() Test</h2>";
$to = "test@example.com"; // Replace with your actual email
$subject = "DineAmaze Test Email";
$message = "This is a test email from DineAmaze.";
$headers = "From: DineAmaze <noreply@dineamaze.com>\r\n";

$mailResult = mail($to, $subject, $message, $headers);
echo "Direct mail() result: " . ($mailResult ? "Success" : "Failed") . "\n";

// Check for mail errors
if (!$mailResult) {
    echo "Mail error: " . error_get_last()['message'] . "\n";
}

echo "</pre>";
?>
