<?php
/**
 * Simple script to run the order notification system
 * This file can be accessed directly from the browser or set up as a cron job
 */

// Set error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Run the notification cron script
require_once 'order_notification_cron.php';

// Output success message if accessed directly
echo "Notification check completed successfully at " . date('Y-m-d H:i:s');
?>
