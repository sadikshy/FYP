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

<!-- Link to the external CSS file -->
<link rel="stylesheet" href="css/header.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
<header>
    <a href="Homepage.php" class="logo">Dine<span>Amaze</span></a>
    
    <button class="mobile-menu-btn" id="mobile-menu-btn">
    <i class="fi fi-rr-bars-staggered" id="menu-icon"></i>
    </button>
    
    <nav id="nav-menu">
        <ul>
            <li><a href="Homepage.php" <?php echo ($currentPage == 'Homepage.php') ? 'class="active"' : ''; ?>>HOME</a></li>
            <li><a href="AboutUs.php" <?php echo ($currentPage == 'AboutUs.php') ? 'class="active"' : ''; ?>>ABOUT US</a></li>
            <li><a href="Menu.php" <?php echo ($currentPage == 'Menu.php') ? 'class="active"' : ''; ?>>MENU</a></li>
            <li><a href="Customization.php" <?php echo ($currentPage == 'Customization.php') ? 'class="active"' : ''; ?>>CUSTOMIZATION</a></li>
            <li><a href="Offers.php" <?php echo ($currentPage == 'Offers.php') ? 'class="active"' : ''; ?>>OFFERS</a></li>
            <li><a href="Takeout.php" <?php echo ($currentPage == 'Takeout.php') ? 'class="active"' : ''; ?>>TAKEOUT</a></li>
            <li><a href="ContactUs.php" <?php echo ($currentPage == 'ContactUs.php') ? 'class="active"' : ''; ?>>CONTACT US</a></li>
        </ul>
    </nav>
    
    <div class="user-actions">
       
        <?php if($isLoggedIn): ?>
            <a href="account_settings.php" class="user-profile">
                <?php if(isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image']) && file_exists($_SESSION['profile_image'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Profile" class="profile-image" id="profile-image">
                <?php else: ?>
                    <img src="assets/images/profile/default-profile.png" alt="Profile" class="profile-image" id="profile-image">
                <?php endif; ?>
                <span><?php echo htmlspecialchars($userName); ?></span>
            </a>
            <a href="logout.php" class="logout">LOGOUT</a>
        <?php else: ?>
            <a href="Login.php" class="login" <?php echo ($currentPage == 'Login.php') ? 'class="active"' : ''; ?>>LOGIN</a>
        <?php endif; ?>
        
        <!-- Add cart icon with correct count -->
        <a href="cart.php" class="cart-icon">
            <i class="fi fi-rr-cart-shopping-fast"></i>
            <span class="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : '0'; ?></span>
        </a>
    </div>
    
    <div class="overlay" id="overlay"></div>
</header>

<!-- Include Ionicons for the icons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<script src="js/header.js"></script>

<!-- Remove the entire duplicate user profile section below -->