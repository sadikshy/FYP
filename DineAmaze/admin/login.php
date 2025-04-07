<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        // For debugging
        $check_query = "SELECT * FROM admin";
        $check_result = $conn->query($check_query);
        
        // Check if admin table has any records
        if ($check_result->num_rows === 0) {
            // Create a default admin if none exists
            $default_name = "Admin";
            $default_email = "admin@dineamaze.com";
            $default_password = password_hash("admin123", PASSWORD_DEFAULT);
            
            $insert_query = "INSERT INTO admin (name, email, password) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("sss", $default_name, $default_email, $default_password);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
        
        // Now try to authenticate
        $stmt = $conn->prepare("SELECT admin_id, email, password, name FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // For development, allow plain text password comparison and also try with the default password
            if (password_verify($password, $admin['password']) || 
                $password === $admin['password'] || 
                ($email === "admin@dineamaze.com" && $password === "admin123")) {
                
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_username'] = $admin['name']; 
                $_SESSION['admin_email'] = $admin['email'];

                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - DineAmaze</title>
    <link rel="stylesheet" href="../css/admin/style.css">
    <link rel="stylesheet" href="../css/admin/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-form-container">
            <div class="login-header">
                <h1>DineAmaze</h1>
                <p>Admin Panel</p>
            </div>

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="login-btn">Login</button>
                </div>
            </form>

            <div class="login-footer">
                <p>&copy; <?php echo date('Y'); ?> DineAmaze. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
