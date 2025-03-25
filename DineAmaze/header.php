<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';

// Get current page for highlighting active link
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<header>
    <div class="logo">DineAmaze</div>
    <nav>
        <ul>
            <li><a href="Homepage.php" <?php echo ($currentPage == 'Homepage.php') ? 'class="active"' : ''; ?>>Home</a></li>
            <li><a href="AboutUs.php" <?php echo ($currentPage == 'AboutUs.php') ? 'class="active"' : ''; ?>>About Us</a></li>
            <li><a href="Menu.php" <?php echo ($currentPage == 'Menu.php') ? 'class="active"' : ''; ?>>Menu</a></li>
            <li><a href="Customization.php" <?php echo ($currentPage == 'Customization.php') ? 'class="active"' : ''; ?>>Customization</a></li>
            <li><a href="Takeout.php" <?php echo ($currentPage == 'Takeout.php') ? 'class="active"' : ''; ?>>TakeOut</a></li>
            <li><a href="ContactUs.php" <?php echo ($currentPage == 'ContactUs.php') ? 'class="active"' : ''; ?>>Contact Us</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="account_settings.php" <?php echo ($currentPage == 'account_settings.php') ? 'class="active"' : ''; ?>>
                    <span class="user-indicator">👤 <?php echo htmlspecialchars($userName); ?></span>
                </a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="Login.php" <?php echo ($currentPage == 'Login.php') ? 'class="active"' : ''; ?>>Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<style>
    .user-indicator {
        background-color: rgb(255, 255, 255);
        padding: 5px 10px;
        border-radius: 15px;
        border: 1px solid rgba(76, 175, 79, 0.41);
        color: #4CAF50;
        font-weight: bold;
    }
</style>