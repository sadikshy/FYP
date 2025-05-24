<?php
// Start the session
session_start();

// Set the default timezone to match your location (adjust this to your timezone)
date_default_timezone_set('Asia/Kathmandu'); // Change this to your timezone

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}

// Include mail helper
require_once 'includes/mail_helper.php';


// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dineamaze_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user information
$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM user WHERE user_id = '$userId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Redirect to login page if user not found
    session_destroy();
    header("Location: Login.php");
    exit();
}

// Handle form submission for profile update
$updateMessage = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    
    // Initialize SQL query without email (since it's now disabled)
    $sql = "UPDATE user SET name = '$name', phone_number = '$phone'";
    
    // Handle profile image upload
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        // Create profile directory if it doesn't exist
        $uploadDir = 'assets/images/profile/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Get file extension
        $fileExt = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        
        // Generate unique filename
        $newFileName = 'profile_' . $userId . '_' . time() . '.' . $fileExt;
        $targetFile = $uploadDir . $newFileName;
        
        // Check if file is an image
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if(in_array($fileExt, $validExtensions)) {
            // Move uploaded file
            if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                // Delete old profile image if exists
                if(!empty($user['profile_image']) && file_exists($user['profile_image']) && strpos($user['profile_image'], 'default-profile.png') === false) {
                    unlink($user['profile_image']);
                }
                
                // Add profile image to SQL update
                $sql .= ", profile_image = '$targetFile'";
                
                // Update session with new profile image path
                $_SESSION['profile_image'] = $targetFile;
            } else {
                $updateMessage = "<div class='alert alert-danger'>Error uploading profile image.</div>";
            }
        } else {
            $updateMessage = "<div class='alert alert-danger'>Invalid file type. Only JPG, JPEG, PNG and GIF are allowed.</div>";
        }
    }
    
    // Complete the SQL query
    $sql .= " WHERE user_id = '$userId'";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['profile_updated'] = true;
        // Refresh user data
        $sql = "SELECT * FROM user WHERE user_id = '$userId'";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
    } else {
        $updateMessage = "<div class='alert alert-danger'>Error updating profile: " . $conn->error . "</div>";
    }
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Get the current password from database
    $checkSql = "SELECT password FROM user WHERE user_id = '$userId'";
    $checkResult = $conn->query($checkSql);
    $currentPasswordHash = $checkResult->fetch_assoc()['password'];
    
    // Verify current password - check if passwords are stored as plain text or hashed
    if (password_verify($currentPassword, $currentPasswordHash) || $currentPassword === $currentPasswordHash) {
        // Check if new passwords match
        if ($newPassword === $confirmPassword) {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password in database
            $sql = "UPDATE user SET password = '$hashedPassword' WHERE user_id = '$userId'";
            
            if ($conn->query($sql) === TRUE) {
                $_SESSION['password_updated'] = true;
            } else {
                $updateMessage = "<div class='alert alert-danger'>Error changing password: " . $conn->error . "</div>";
            }
        } else {
            $updateMessage = "<div class='alert alert-danger'>New passwords do not match!</div>";
        }
    } else {
        $updateMessage = "<div class='alert alert-danger'>Current password is incorrect!</div>";
    }
}

// Handle order cancellation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_order'])) {
    $orderId = intval($_POST['order_id']);
    
    // Get order information
    $orderSql = "SELECT * FROM takeout_order_items WHERE order_id = ?";
    $stmt = $conn->prepare($orderSql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $orderResult = $stmt->get_result();
    
    if ($orderResult && $orderResult->num_rows > 0) {
        $order = $orderResult->fetch_assoc();
        
        // Check if order was placed within the last 10 minutes
        $orderTime = strtotime($order['order_date']);
        $currentTime = time();
        $timeDifference = $currentTime - $orderTime;
        
        // Allow cancellation only within 10 minutes of placing the order
        if ($timeDifference <= 600) { // 600 seconds = 10 minutes
            // Update order status to cancelled
            $updateSql = "UPDATE takeout_order_items SET status = 'cancelled' WHERE order_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("i", $orderId);
            
            if ($updateStmt->execute()) {
                $_SESSION['order_cancelled'] = true;
                $_SESSION['cancellation_message'] = "Your order has been successfully cancelled.";
            } else {
                $_SESSION['cancellation_error'] = "Error cancelling order: " . $conn->error;
            }
            $updateStmt->close();
        } else {
            $_SESSION['cancellation_error'] = "Sorry, orders can only be cancelled within 10 minutes of placing them.";
        }
    } else {
        $_SESSION['cancellation_error'] = "Order not found.";
    }
    $stmt->close();
    
    // Redirect to refresh the page and avoid form resubmission
    header("Location: account_settings.php#orders");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - DineAmaze</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/account_settings.css">
    <style>
        .countdown-timer {
            font-weight: 600;
            color: #e74a3b;
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.8; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <br> <br>
    <!-- Bootstrap Alert for Profile Update -->
    <?php if(isset($_SESSION['profile_updated']) && $_SESSION['profile_updated']): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="profileAlert">
        <strong>Success!</strong> Profile updated successfully!
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php 
        // Clear the session variable
        unset($_SESSION['profile_updated']);
    ?>
    <?php endif; ?>
    
    <!-- Bootstrap Alert for Password Update -->
    <?php if(isset($_SESSION['password_updated']) && $_SESSION['password_updated']): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="passwordAlert">
        <strong>Success!</strong> Password changed successfully!
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php 
        // Clear the session variable
        unset($_SESSION['password_updated']);
    ?>
    <?php endif; ?>

    <!-- Bootstrap Alert for Order Cancellation -->
    <?php if(isset($_SESSION['order_cancelled']) && $_SESSION['order_cancelled']): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="orderCancelAlert">
        <strong>Success!</strong> <?php echo isset($_SESSION['cancellation_message']) ? $_SESSION['cancellation_message'] : 'Your order has been cancelled successfully!'; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php 
        // Clear the session variables
        unset($_SESSION['order_cancelled']);
        unset($_SESSION['cancellation_message']);
    ?>
    <?php endif; ?>
    
    <!-- Bootstrap Alert for Order Cancellation Error -->
    <?php if(isset($_SESSION['cancellation_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="orderCancelErrorAlert">
        <strong>Error!</strong> <?php echo $_SESSION['cancellation_error']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php 
        // Clear the session variable
        unset($_SESSION['cancellation_error']);
    ?>
    <?php endif; ?>
    
    <div class="account-container">
        <h2 class="text-center mb-4">Account Settings</h2>
        
        <?php echo $updateMessage; ?>
        
        <div class="user-info">
            <!-- Change this in the user info section -->
            <!-- Update the user info section (around line 213) -->
            <div class="account-profile-image-container">
                <?php if(!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="account-profile-image" id="account-profile-preview">
                <?php else: ?>
                    <img src="images/profile/default-profile.png" alt="Default Profile" class="account-profile-image" id="account-profile-preview">
                <?php endif; ?>
            </div>
            <h4>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h4>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
        </div>
        
        <ul class="nav nav-tabs" id="accountTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab">Edit Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab">Change Password</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="orders-tab" data-toggle="tab" href="#orders" role="tab">Order History</a>
            </li>
        </ul>
        
        <div class="tab-content" id="accountTabsContent">
            <!-- Profile Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="profile-image-container">
                        <?php if(!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="profile-image" id="profile-preview" width="150px">
                        <?php else: ?>
                            <img src="images/profile/default-profile.png" alt="Default Profile" class="profile-image" id="profile-preview">
                        <?php endif; ?>
                        
                        <div class="profile-upload">
                            <label for="profile_image" class="change-profile-btn">
                                <i class="fas fa-camera"></i> Change Profile Picture
                            </label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(this)">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control disabled-input" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        <small class="form-text text-muted">Email cannot be changed for security reasons.</small>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
            
            <!-- Password Tab -->
            <div class="tab-pane fade" id="password" role="tabpanel">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
            
            <!-- Orders Tab -->
            <div class="tab-pane fade" id="orders" role="tabpanel">
                <h4>Your Order History</h4>
                <?php
                // Reconnect to database for order history
                $conn = new mysqli($servername, $username, $password, $dbname);
                
                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                
                // Process order cancellation if requested
                if(isset($_POST['cancel_order']) && isset($_POST['order_id'])) {
                    $orderIdToCancel = $_POST['order_id'];
                    
                    // Get user's email from the user array
                    $userEmail = $user['email'];
                    
                    // Get the order details to check if it can be cancelled
                    $checkOrderSql = "SELECT * FROM takeout_order_items WHERE order_id = '$orderIdToCancel' AND email = '$userEmail'";
                    $checkOrderResult = $conn->query($checkOrderSql);
                    
                    if($checkOrderResult && $checkOrderResult->num_rows > 0) {
                        $orderToCancel = $checkOrderResult->fetch_assoc();
                        $orderTime = strtotime($orderToCancel['order_date']);
                        $currentTime = time();
                        $cutoffTime = $orderTime - (10 * 60); // 10 minutes before order time
                        
                        // Only allow cancellation if current time is before cutoff time
                        if($currentTime < $cutoffTime) {
                            $cancelSql = "UPDATE takeout_order_items SET status = 'cancelled' WHERE order_id = '$orderIdToCancel' AND email = '$userEmail'";
                            
                            if($conn->query($cancelSql) === TRUE) {
                                $_SESSION['order_cancelled'] = true;
                                
                                // Send email notification about order cancellation using mail_helper
                                require_once 'includes/mail_helper.php';
                                sendOrderPickupNotification($userEmail, $user['name'], $order['order_id'], $order, $order['status']);
                                
                                // Add the order ID to the success message
                                echo '<div class="alert alert-success">Order #'.$orderIdToCancel.' has been cancelled successfully.</div>';
                                
                                // Add JavaScript to refresh the page after a short delay
                                echo '<script>
                                    setTimeout(function() {
                                        window.location.href = "account_settings.php#orders";
                                    }, 750);
                                </script>';
                            } else {
                                echo '<div class="alert alert-danger">Error cancelling order: ' . $conn->error . '</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger">This order cannot be cancelled as it is less than 10 minutes before the scheduled delivery/pickup time.</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger">Order not found or you do not have permission to cancel it.</div>';
                    }
                }
                
                // Get user's order history - using email instead of user_id
                $userEmail = $user['email'];
                $orderSql = "SELECT * FROM takeout_order_items WHERE email = '$userEmail' ORDER BY order_date DESC";
                $orderResult = $conn->query($orderSql);
                
                // Remove the debug messages and directly display orders
                if ($orderResult && $orderResult->num_rows > 0) {
                    while ($order = $orderResult->fetch_assoc()) {
                        $statusClass = '';
                        if ($order['status'] == 'verified') {
                            $statusClass = 'text-success';
                            
                            // Check if pickup notification should be sent and hasn't been sent already
                            if (empty($order['pickup_notification_sent']) || $order['pickup_notification_sent'] == 0) {
                                $orderTime = strtotime($order['order_date']);
                                $currentTime = time();
                                $timeDifference = $orderTime - $currentTime;
                                
                                // If it's around 10 minutes before pickup time, send notification
                                if ($timeDifference >= 600 && $timeDifference <= 660) {
                                    // Send pickup notification
                                    $notificationSent = sendOrderPickupNotification($userEmail, $user['name'], $order['order_id'], $order, $order['status']);
                                }
                            }
                        } else if ($order['status'] == 'pending') {
                            $statusClass = 'text-warning';
                        } else if ($order['status'] == 'cancelled') {
                            $statusClass = 'text-cancelled';
                        }
                        
                        // Calculate time difference for cancellation eligibility
                        $orderTime = strtotime($order['order_date']);
                        $currentTime = time();
                        
                        // Allow cancellation only if current time is at least 10 minutes before order time
                        $cutoffTime = $orderTime - (10 * 60); // 10 minutes before order time
                        $canCancel = ($currentTime < $cutoffTime && ($order['status'] == 'pending' || $order['status'] == 'verified'));
                        
                        // Calculate time left for cancellation in minutes
                        $timeLeftSeconds = $cutoffTime - $currentTime;
                        $timeLeftMinutes = ceil($timeLeftSeconds / 60);
                        
                        echo '<div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Order #' . $order['order_id'] . '</strong> - 
                                        <span class="' . $statusClass . '">' . ucfirst($order['status']) . '</span>
                                    </div>
                                    <div>
                                        ' . date('M d, Y - h:i A', strtotime($order['order_date'])) . '
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p><strong>Item:</strong> ' . $order['item_name'] . '</p>
                                            <p><strong>Quantity:</strong> ' . $order['quantity'] . '</p>
                                            <p><strong>Price:</strong> Rs. ' . $order['price'] . '</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Email:</strong> ' . $order['email'] . '</p>
                                            <p><strong>Status:</strong> ' . ucfirst($order['status']) . '</p>
                                            <p><strong>Pickup Time:</strong> ' . (isset($order['pickup_time']) && !empty($order['pickup_time']) ? date('H:i A', strtotime($order['pickup_time'])) : 'Not specified') . '</p>';
                        
                        // Order information note and cancel button
                        if ($order['status'] == 'pending' || $order['status'] == 'verified') {
                            echo '<p class="text-muted mt-2 small">Your order is scheduled for pickup at ' . (isset($order['pickup_time']) && !empty($order['pickup_time']) ? date('H:i A', strtotime($order['pickup_time'])) : 'Not specified') . '.</p>';
                            
                            // Check if order was placed within the last 10 minutes
                            $orderTime = strtotime($order['order_date']);
                            $currentTime = time();
                            $timeDifference = $currentTime - $orderTime;
                            $canCancel = ($timeDifference <= 600); // 600 seconds = 10 minutes
                            
                            // Calculate time left for cancellation
                            $timeLeftSeconds = 600 - $timeDifference;
                            $timeLeftMinutes = floor($timeLeftSeconds / 60);
                            $timeLeftSecondsRemainder = $timeLeftSeconds % 60;
                            
                            echo '<form method="POST" action="" class="mt-3">';
                            echo '<input type="hidden" name="order_id" value="' . $order['order_id'] . '">';
                            
                            if ($canCancel) {
                                echo '<button type="submit" name="cancel_order" class="btn btn-danger btn-sm">Cancel Order</button>';
                                echo '<p class="text-muted mt-2 small">You can cancel this order for the next <span id="timer-' . $order['order_id'] . '" class="countdown-timer" data-seconds-left="' . $timeLeftSeconds . '">' . $timeLeftMinutes . ' minutes and ' . $timeLeftSecondsRemainder . ' seconds</span>.</p>';
                                
                                // Add script for this specific timer
                                echo '<script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        // Initialize timer for order #' . $order['order_id'] . '
                                        initializeTimer("timer-' . $order['order_id'] . '", ' . $timeLeftSeconds . ');
                                    });
                                </script>';
                            } else {
                                echo '<button type="button" class="btn btn-danger btn-sm" disabled>Cancel Order</button>';
                                echo '<p class="text-muted mt-2 small">Orders can only be cancelled within 10 minutes of placing them.</p>';
                            }
                            
                            echo '</form>';
                        }
                        
                        echo '</div>
                                    </div>
                                </div>
                            </div>';
                    }
                } 

else {
                    echo '<p>You haven\'t placed any orders yet.</p>';
                }
                
                // Close the connection
                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/account_settings.js"></script>
    
    <script>
        // Function to initialize countdown timers
        function initializeTimer(timerId, secondsLeft) {
            const timerElement = document.getElementById(timerId);
            if (!timerElement) return;
            
            // Update the timer every second
            const timerInterval = setInterval(function() {
                secondsLeft--;
                
                if (secondsLeft <= 0) {
                    // Time's up - disable the button and update message
                    clearInterval(timerInterval);
                    timerElement.textContent = '0 minutes and 0 seconds';
                    
                    // Find the parent form and disable the button
                    const form = timerElement.closest('form');
                    if (form) {
                        const button = form.querySelector('button[name="cancel_order"]');
                        if (button) {
                            button.disabled = true;
                            button.textContent = 'Cancel Order (Expired)';
                        }
                        
                        // Update the message
                        const messageElement = timerElement.parentElement;
                        if (messageElement) {
                            messageElement.textContent = 'Orders can only be cancelled within 10 minutes of placing them.';
                        }
                    }
                    
                    // Reload the page to reflect the updated state
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                    
                    return;
                }
                
                // Calculate minutes and seconds
                const minutes = Math.floor(secondsLeft / 60);
                const seconds = secondsLeft % 60;
                
                // Update the display
                timerElement.textContent = minutes + ' minutes and ' + seconds + ' seconds';
            }, 1000);
        }
    </script>
</body>
</html>