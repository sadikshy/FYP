<?php
// Get admin profile picture and name from database
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$admin_id = $_SESSION['admin_id'];
$sql = "SELECT profile_picture, name FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$profile_picture = $row['profile_picture'] ?? 'default.jpg';
$admin_name = $row['name'] ?? 'Admin User';
$picture_path = "../assets/admin/images/profile_pictures/" . $profile_picture;

// Check if file exists, otherwise use default
if (!file_exists($picture_path)) {
    $picture_path = "../assets/admin/images/profile_pictures/default.jpg";
}

// Get count of unread messages
$unread_messages = 0;
$message_sql = "SELECT COUNT(*) as unread_count FROM contact_message WHERE is_read = 'No'";
$message_result = $conn->query($message_sql);
if ($message_result && $message_row = $message_result->fetch_assoc()) {
    $unread_messages = $message_row['unread_count'];
}
$conn->close();
?>

<div class="admin-header">
    <div class="header-title">
        <h1><?php echo ucfirst(str_replace('.php', '', basename($_SERVER['PHP_SELF']))); ?></h1>
    </div>
    <div class="header-actions">
        <div class="notification-bell">
            <a href="messages.php" class="notification-link">
                <i class="fas fa-bell"></i>
                <?php if ($unread_messages > 0): ?>
                <span class="notification-badge"><?php echo $unread_messages; ?></span>
                <?php endif; ?>
            </a>
        </div>
        <img src="<?php echo htmlspecialchars($picture_path); ?>" alt="Admin" class="user-avatar">
        <span class="user-name"><?php echo htmlspecialchars($admin_name); ?></span>
        <div class="header-links">
            <a href="messages.php" class="header-link"><i class="fas fa-envelope"></i> Messages</a>
            <a href="profile.php" class="header-link"><i class="fas fa-user"></i> Profile</a>
            <a href="settings.php" class="header-link"><i class="fas fa-cog"></i> Settings</a>
            <a href="logout.php" class="header-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</div>