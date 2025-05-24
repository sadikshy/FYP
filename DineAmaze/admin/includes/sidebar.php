


<div class="sidebar">
    <div class="sidebar-header">
        <h2>DineAmaze</h2>
        <p>Admin Panel</p>
    </div>
    
    <div class="sidebar-menu">
        <a href="index.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <div class="menu-category">Menu Management</div>
        
        <a href="menu-items.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'menu-items.php') ? 'active' : ''; ?>">
            <i class="fas fa-utensils"></i> Menu Items
        </a>
        
        <a href="categories.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'active' : ''; ?>">
            <i class="fas fa-list"></i> Categories
        </a>
        
        <a href="manage_offers.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_offers.php') ? 'active' : ''; ?>">
            <i class="fas fa-tags"></i> Offers
        </a>
        
        <div class="menu-category">Orders</div>
        
        <a href="orders.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Orders
        </a>
        
        <a href="manual_notifications.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'manual_notifications.php') ? 'active' : ''; ?>">
            <i class="fas fa-bell"></i> Order Notifications
        </a>
        
        <div class="menu-category">Users</div>
        
        <a href="users.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Users
        </a>
        
        <a href="reviews.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'reviews.php') ? 'active' : ''; ?>">
            <i class="fas fa-star"></i> Reviews
        </a>
        
        <div class="menu-category">System</div>
        
        <a href="settings.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i> Settings
        </a>
        

    </div>
    
    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<button class="sidebar-toggle">
    <i class="fas fa-bars"></i>
</button>
