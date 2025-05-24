<?php
// Start the session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Message array to store results
$messages = [];

// Check if preparation_notification_sent column exists
$checkPrepColumn = "SHOW COLUMNS FROM takeout_order_items LIKE 'preparation_notification_sent'";
$prepColumnResult = $conn->query($checkPrepColumn);

if ($prepColumnResult->num_rows == 0) {
    // Add preparation_notification_sent column
    $addPrepColumn = "ALTER TABLE takeout_order_items ADD COLUMN preparation_notification_sent TINYINT(1) NOT NULL DEFAULT 0";
    if ($conn->query($addPrepColumn)) {
        $messages[] = "Successfully added preparation_notification_sent column.";
    } else {
        $messages[] = "Error adding preparation_notification_sent column: " . $conn->error;
    }
} else {
    $messages[] = "preparation_notification_sent column already exists.";
}

// Check if pickup_notification_sent column exists
$checkPickupColumn = "SHOW COLUMNS FROM takeout_order_items LIKE 'pickup_notification_sent'";
$pickupColumnResult = $conn->query($checkPickupColumn);

if ($pickupColumnResult->num_rows == 0) {
    // Add pickup_notification_sent column
    $addPickupColumn = "ALTER TABLE takeout_order_items ADD COLUMN pickup_notification_sent TINYINT(1) NOT NULL DEFAULT 0";
    if ($conn->query($addPickupColumn)) {
        $messages[] = "Successfully added pickup_notification_sent column.";
    } else {
        $messages[] = "Error adding pickup_notification_sent column: " . $conn->error;
    }
} else {
    $messages[] = "pickup_notification_sent column already exists.";
}

// Create logs directory if it doesn't exist
$logsDir = dirname(__DIR__) . '/logs';
if (!file_exists($logsDir)) {
    if (mkdir($logsDir, 0777, true)) {
        $messages[] = "Successfully created logs directory.";
    } else {
        $messages[] = "Error creating logs directory.";
    }
} else {
    $messages[] = "Logs directory already exists.";
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Update for Notifications - DineAmaze Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background-color: #764ba2;
            color: white;
        }
        .btn-primary {
            background-color: #764ba2;
            border-color: #764ba2;
        }
        .btn-primary:hover {
            background-color: #663a91;
            border-color: #663a91;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Database Update for Email Notifications</h4>
            </div>
            <div class="card-body">
                <h5 class="card-title">Update Results</h5>
                
                <ul class="list-group mb-4">
                    <?php foreach ($messages as $message): ?>
                        <li class="list-group-item">
                            <?php echo $message; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle mr-2"></i>Next Steps</h5>
                    <p>The database has been updated to support automated email notifications for orders. The system will now:</p>
                    <ul>
                        <li>Send an email when an order starts being prepared</li>
                        <li>Send another email 10 minutes before the scheduled pickup time</li>
                    </ul>
                    <p>To ensure notifications are sent automatically, set up a scheduled task to run the following script every 5 minutes:</p>
                    <pre class="bg-light p-3">php <?php echo dirname(__DIR__); ?>/cron/send_order_notifications.php</pre>
                </div>
                
                <a href="index.php" class="btn btn-primary">Return to Dashboard</a>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
