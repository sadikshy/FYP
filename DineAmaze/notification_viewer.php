<?php
/**
 * Notification Viewer for DineAmaze
 * This page displays all saved notifications in a user-friendly format
 */

// Set error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Base URL for assets
define('BASE_URL', 'http://localhost/dashboard/Kachuwafyp/Development/DineAmaze');

// Directory where notifications are stored
$notificationsDir = __DIR__ . '/notifications';

// Get all notification files
$notificationFiles = [];
if (file_exists($notificationsDir)) {
    $files = scandir($notificationsDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
            $notificationFiles[] = $file;
        }
    }
    
    // Sort by newest first
    usort($notificationFiles, function($a, $b) use ($notificationsDir) {
        return filemtime($notificationsDir . '/' . $b) - filemtime($notificationsDir . '/' . $a);
    });
}

// Get notification type and order ID from filename
function getNotificationInfo($filename) {
    $info = [];
    
    // Extract type (preparation or ready)
    if (strpos($filename, 'preparation') !== false) {
        $info['type'] = 'Preparation';
        $info['badge_color'] = 'warning';
    } else if (strpos($filename, 'ready') !== false) {
        $info['type'] = 'Ready for Pickup';
        $info['badge_color'] = 'success';
    } else {
        $info['type'] = 'Unknown';
        $info['badge_color'] = 'secondary';
    }
    
    // Extract order ID
    preg_match('/_(\d+)_/', $filename, $matches);
    $info['order_id'] = isset($matches[1]) ? $matches[1] : 'Unknown';
    
    // Extract timestamp
    preg_match('/_(\d+)\.html$/', $filename, $matches);
    $timestamp = isset($matches[1]) ? $matches[1] : 0;
    $info['timestamp'] = $timestamp ? date('Y-m-d H:i:s', $timestamp) : 'Unknown';
    
    return $info;
}

// Handle viewing a specific notification
$viewNotification = isset($_GET['view']) ? $_GET['view'] : null;
$notificationContent = null;

if ($viewNotification && file_exists($notificationsDir . '/' . $viewNotification)) {
    $notificationContent = file_get_contents($notificationsDir . '/' . $viewNotification);
    
    // Extract email content (everything after the headers)
    $parts = explode("\n\n", $notificationContent, 2);
    if (count($parts) > 1) {
        $notificationContent = $parts[1];
    }
}

// Handle deleting notifications
if (isset($_POST['delete']) && isset($_POST['file'])) {
    $fileToDelete = $_POST['file'];
    $fullPath = $notificationsDir . '/' . $fileToDelete;
    
    if (file_exists($fullPath) && unlink($fullPath)) {
        $deleteMessage = "Notification deleted successfully.";
    } else {
        $deleteError = "Failed to delete notification.";
    }
    
    // Redirect to avoid resubmission
    header("Location: notification_viewer.php");
    exit;
}

// Handle deleting all notifications
if (isset($_POST['delete_all']) && $_POST['delete_all'] === 'yes') {
    $deletedCount = 0;
    
    foreach ($notificationFiles as $file) {
        $fullPath = $notificationsDir . '/' . $file;
        if (file_exists($fullPath) && unlink($fullPath)) {
            $deletedCount++;
        }
    }
    
    if ($deletedCount > 0) {
        $deleteMessage = "$deletedCount notifications deleted successfully.";
        $notificationFiles = []; // Clear the array
    } else {
        $deleteError = "No notifications were deleted.";
    }
    
    // Redirect to avoid resubmission
    header("Location: notification_viewer.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineAmaze - Notification Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
            background-color: #f8f9fa;
        }
        .notification-list {
            max-height: 600px;
            overflow-y: auto;
        }
        .notification-preview {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 15px;
            background-color: white;
            min-height: 400px;
        }
        .notification-item {
            cursor: pointer;
        }
        .notification-item:hover {
            background-color: #f1f1f1;
        }
        .notification-item.active {
            background-color: #e9ecef;
            border-left: 3px solid #0d6efd;
        }
        .notification-meta {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .empty-state {
            text-align: center;
            padding: 50px 0;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h1>DineAmaze Notification Viewer</h1>
                <p class="text-muted">View all notifications generated by the system</p>
            </div>
            <div class="col-auto">
                <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-primary">Back to DineAmaze</a>
            </div>
        </div>
        
        <?php if (isset($deleteMessage)): ?>
        <div class="alert alert-success"><?php echo $deleteMessage; ?></div>
        <?php endif; ?>
        
        <?php if (isset($deleteError)): ?>
        <div class="alert alert-danger"><?php echo $deleteError; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Notifications</h5>
                        <?php if (count($notificationFiles) > 0): ?>
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete all notifications?');">
                            <input type="hidden" name="delete_all" value="yes">
                            <button type="submit" class="btn btn-sm btn-danger">Delete All</button>
                        </form>
                        <?php endif; ?>
                    </div>
                    <div class="notification-list list-group list-group-flush">
                        <?php if (count($notificationFiles) > 0): ?>
                            <?php foreach ($notificationFiles as $file): ?>
                                <?php $info = getNotificationInfo($file); ?>
                                <a href="?view=<?php echo urlencode($file); ?>" class="notification-item list-group-item list-group-item-action <?php echo ($viewNotification === $file) ? 'active' : ''; ?>">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Order #<?php echo $info['order_id']; ?></h6>
                                        <span class="badge bg-<?php echo $info['badge_color']; ?>"><?php echo $info['type']; ?></span>
                                    </div>
                                    <p class="mb-1 notification-meta">
                                        <small>Generated: <?php echo $info['timestamp']; ?></small>
                                    </p>
                                    <div class="d-flex justify-content-end mt-2">
                                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this notification?');">
                                            <input type="hidden" name="file" value="<?php echo $file; ?>">
                                            <button type="submit" name="delete" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <p>No notifications found</p>
                                <small>Notifications will appear here when they are generated</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <?php if ($viewNotification): ?>
                                <?php $info = getNotificationInfo($viewNotification); ?>
                                Notification for Order #<?php echo $info['order_id']; ?> (<?php echo $info['type']; ?>)
                            <?php else: ?>
                                Notification Preview
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body notification-preview">
                        <?php if ($notificationContent): ?>
                            <?php echo $notificationContent; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <p>Select a notification to view</p>
                                <small>The content will be displayed here</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
