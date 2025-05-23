<?php
session_start();
include '../includes/db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get admin data from database
$admin_id = $_SESSION['admin_id'];
$query = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Handle profile update
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        
        // Update profile in database
        $update_query = "UPDATE admin SET name = ?, email = ?, phone = ? WHERE admin_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sssi", $name, $email, $phone, $admin_id);
        
        if ($update_stmt->execute()) {
            $success_message = "Profile updated successfully!";
            
            // Refresh admin data
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
        } else {
            $error_message = "Failed to update profile: " . $conn->error;
        }
    }
    
    // Handle password change
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Verify current password
        $password_query = "SELECT password FROM admin WHERE admin_id = ?";
        $password_stmt = $conn->prepare($password_query);
        $password_stmt->bind_param("i", $admin_id);
        $password_stmt->execute();
        $password_result = $password_stmt->get_result();
        $password_data = $password_result->fetch_assoc();
        
        // For debugging (comment out in production)
        // error_log("Current password: " . $current_password);
        // error_log("Stored hash: " . $password_data['password']);
        // error_log("Verification result: " . (password_verify($current_password, $password_data['password']) ? 'true' : 'false'));
        
        // Check if the password meets requirements
        $password_regex = '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/';
        
        if (password_verify($current_password, $password_data['password'])) {
            if ($new_password === $confirm_password) {
                if (preg_match($password_regex, $new_password)) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    // Update password
                    $update_password_query = "UPDATE admin SET password = ? WHERE admin_id = ?";
                    $update_password_stmt = $conn->prepare($update_password_query);
                    $update_password_stmt->bind_param("si", $hashed_password, $admin_id);
                    
                    if ($update_password_stmt->execute()) {
                        $success_message = "Password changed successfully!";
                    } else {
                        $error_message = "Failed to change password: " . $conn->error;
                    }
                } else {
                    $error_message = "Password must be at least 8 characters and include letters, numbers, and special characters.";
                }
            } else {
                $error_message = "New passwords do not match!";
            }
        } else {
            $error_message = "Current password is incorrect!";
        }
    }
    
    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (in_array($_FILES['profile_picture']['type'], $allowed_types) && $_FILES['profile_picture']['size'] <= $max_size) {
            $upload_dir = '../assets/admin/images/profile_pictures/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = $admin_id . '_' . time() . '_' . basename($_FILES['profile_picture']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                // Update profile picture in database
                $update_pic_query = "UPDATE admin SET profile_picture = ? WHERE admin_id = ?";
                $update_pic_stmt = $conn->prepare($update_pic_query);
                $update_pic_stmt->bind_param("si", $file_name, $admin_id);
                
                if ($update_pic_stmt->execute()) {
                    $success_message = "Profile picture updated successfully!";
                    
                    // Refresh admin data
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $admin = $result->fetch_assoc();
                } else {
                    $error_message = "Failed to update profile picture in database: " . $conn->error;
                }
            } else {
                $error_message = "Failed to upload profile picture!";
            }
        } else {
            $error_message = "Invalid file! Please upload a JPG or PNG image under 2MB.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - DineAmaze</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../css/admin/sidebar.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4 py-4">
                <h1 class="h2 mb-4">Admin Profile Settings</h1>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="profile-picture-container mb-3">
                                    <?php if (!empty($admin['profile_picture'])): ?>
                                        <img src="../assets/admin/images/profile_pictures/<?php echo $admin['profile_picture']; ?>" alt="Profile Picture" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="../assets/images/default-profile.png" alt="Default Profile" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                    <?php endif; ?>
                                </div>
                                <h5 class="card-title"><?php echo htmlspecialchars($admin['name']); ?></h5>
                                <p class="card-text text-muted">Administrator</p>
                                
                                <form action="" method="post" enctype="multipart/form-data">
                                    <div class="custom-file mb-3">
                                        <input type="file" class="custom-file-input" id="profile_picture" name="profile_picture">
                                        <label class="custom-file-label" for="profile_picture">Choose new picture</label>
                                    </div>
                                    <button type="submit" name="upload_picture" class="btn btn-primary btn-sm">Update Picture</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Profile Information</h5>
                            </div>
                            <div class="card-body">
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label for="name">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>">
                                    </div>
                                    
                                    <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Change Password</h5>
                            </div>
                            <div class="card-body">
                                <form action="" method="post" id="passwordForm">
                                    <div class="form-group">
                                        <label for="current_password">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        <div class="invalid-feedback" id="currentPasswordError">Please enter your current password</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <div class="invalid-feedback" id="newPasswordError">Password must be at least 8 characters with letters and numbers</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <div class="invalid-feedback" id="confirmPasswordError">Passwords do not match</div>
                                    </div>
                                    
                                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Show filename when file is selected
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
        
        // Password validation
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Current password validation
            const currentPassword = document.getElementById('current_password').value;
            if (currentPassword.length < 1) {
                document.getElementById('current_password').classList.add('is-invalid');
                document.getElementById('currentPasswordError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('current_password').classList.remove('is-invalid');
                document.getElementById('currentPasswordError').style.display = 'none';
            }
            
            // New password validation
            const newPassword = document.getElementById('new_password').value;
            const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;
            if (!passwordRegex.test(newPassword)) {
                document.getElementById('new_password').classList.add('is-invalid');
                document.getElementById('newPasswordError').style.display = 'block';
                document.getElementById('newPasswordError').textContent = 'Password must be at least 8 characters and include letters, numbers, and special characters.';
                isValid = false;
            } else {
                document.getElementById('new_password').classList.remove('is-invalid');
                document.getElementById('newPasswordError').style.display = 'none';
            }
            
            // Confirm password validation
            const confirmPassword = document.getElementById('confirm_password').value;
            if (confirmPassword !== newPassword) {
                document.getElementById('confirm_password').classList.add('is-invalid');
                document.getElementById('confirmPasswordError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('confirm_password').classList.remove('is-invalid');
                document.getElementById('confirmPasswordError').style.display = 'none';
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>