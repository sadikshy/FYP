<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}

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
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    
    $sql = "UPDATE user SET name = '$name', email = '$email', phone_number = '$phone' WHERE user_id = '$userId'";
    
    if ($conn->query($sql) === TRUE) {
        $updateMessage = "<div class='alert alert-success'>Profile updated successfully!</div>";
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
                $updateMessage = "<div class='alert alert-success'>Password changed successfully!</div>";
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - DineAmaze</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="Homepage.css">
    <style>
        .account-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .nav-tabs .nav-link {
            color: #555;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link.active {
            color: #764ba2;
            font-weight: 600;
        }
        
        .tab-content {
            padding: 30px 15px;
        }
        
        .form-group label {
            font-weight: 500;
            color: #555;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, #764ba2, #667eea);
        }
        
        .alert {
            margin-top: 20px;
        }
        
        .user-info {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .user-info h4 {
            color: #764ba2;
            margin-bottom: 15px;
        }
        
        .user-info p {
            margin-bottom: 10px;
        }
        
        .user-info strong {
            display: inline-block;
            width: 120px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="account-container">
        <h2 class="text-center mb-4">Account Settings</h2>
        
        <?php echo $updateMessage; ?>
        
        <div class="user-info">
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
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
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
                
                // Get user's order history - using email instead of user_id
                $userEmail = $user['email'];
                $orderSql = "SELECT * FROM takeout_order_items WHERE email = '$userEmail' ORDER BY order_date DESC";
                $orderResult = $conn->query($orderSql);
                
                if ($orderResult && $orderResult->num_rows > 0) {
                    while ($order = $orderResult->fetch_assoc()) {
                        $statusClass = '';
                        if ($order['status'] == 'verified') {
                            $statusClass = 'text-success';
                        } else if ($order['status'] == 'pending') {
                            $statusClass = 'text-warning';
                        }
                        
                        echo '<div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Order #' . $order['order_id'] . '</strong> - 
                                        <span class="' . $statusClass . '">' . ucfirst($order['status']) . '</span>
                                    </div>
                                    <div>
                                        ' . date('M d, Y', strtotime($order['order_date'])) . '
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
                                        </div>
                                    </div>';
                        
                        echo '</div></div>';
                    }
                } else {
                    echo '<p>You haven\'t placed any orders yet.</p>';
                }
                
                // Close the connection
                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <!-- Add Bootstrap JS for tab functionality -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>