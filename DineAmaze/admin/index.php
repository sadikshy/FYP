<?php
// Start the session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DineAmaze</title>
    <link rel="stylesheet" href="../css/admin/style.css">
    <link rel="stylesheet" href="../css/admin/sidebar.css">
    <link rel="stylesheet" href="../css/admin/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
   <div class="admin-container">
        <!-- Include Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <!-- Include Header -->
            <div class="admin-header">
                <div class="header-title">
                    <h1>Dashboard</h1>
                </div>
                <div class="header-actions">
                    <img src="../assets/images/admin-avatar.png" alt="Admin" class="user-avatar">
                    <span class="user-name">Admin User</span>
                    <div class="header-links">
                        <a href="profile.php" class="header-link"><i class="fas fa-user"></i> Profile</a>
                        <a href="settings.php" class="header-link"><i class="fas fa-cog"></i> Settings</a>
                        <a href="logout.php" class="header-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
            
            <!-- Rest of your dashboard content -->
            
            <div class="dashboard-content">
                <h1>Dashboard</h1>
                
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Menu Items</h3>
                            <p class="stat-number">
                                <?php
                                // Database connection
                                $conn = new mysqli("localhost", "root", "", "dineamaze_database");
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }
                                
                                $result = $conn->query("SELECT COUNT(*) as total FROM menu_item");
                                $row = $result->fetch_assoc();
                                echo $row['total'];
                                ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Users</h3>
                            <p class="stat-number">
                                <?php
                                $result = $conn->query("SELECT COUNT(*) as total FROM user");
                                $row = $result->fetch_assoc();
                                echo $row['total'];
                                ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Orders</h3>
                            <p class="stat-number">
                                <?php
                                $result = $conn->query("SELECT COUNT(*) as total FROM takeout_order_items");
                                if ($result) {
                                    $row = $result->fetch_assoc();
                                    echo $row['total'];
                                } else {
                                    echo "0";
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Reviews</h3>
                            <p class="stat-number">
                                <?php
                                // Updated query for reviews
                                $result = $conn->query("SELECT COUNT(*) as total FROM review");
                                if ($result) {
                                    $row = $result->fetch_assoc();
                                    echo $row['total'];
                                } else {
                                    echo "0";
                                }
                                $conn->close();
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="recent-section">
                    <h2>Recent Orders</h2>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Database connection
                                $conn = new mysqli("localhost", "root", "", "dineamaze_database");
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }
                                
                                // Get unique order group IDs to show distinct orders
                                $sql = "SELECT DISTINCT order_group_id, MIN(order_id) as order_id, email, 
                                        order_date, SUM(price * quantity) as total_amount, 
                                        MAX(status) as status
                                        FROM takeout_order_items 
                                        GROUP BY order_group_id, email, order_date
                                        ORDER BY order_date DESC LIMIT 5";
                                
                                $result = $conn->query($sql);
                                
                                if ($result && $result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>#" . $row["order_id"] . "</td>";
                                        
                                        // Format email to be more readable
                                        $email = $row["email"];
                                        echo "<td>" . $email . "</td>";
                                        
                                        // Format date to match your screenshot (26 Mar 2025)
                                        echo "<td>" . date("d M Y", strtotime($row["order_date"])) . "</td>";
                                        
                                        // Format amount with Rs. prefix
                                        echo "<td>Rs. " . number_format($row["total_amount"], 2) . "</td>";
                                        
                                        // Improve status badges with better styling
                                        $statusClass = "";
                                        $statusText = $row["status"];
                                        
                                        switch($statusText) {
                                            case "pending":
                                                $statusClass = "status-pending";
                                                $statusText = "pending";
                                                break;
                                            case "processing":
                                                $statusClass = "status-processing";
                                                $statusText = "processing";
                                                break;
                                            case "verified":
                                                $statusClass = "status-completed";
                                                $statusText = "verified";
                                                break;
                                            case "cancelled":
                                                $statusClass = "status-cancelled";
                                                $statusText = "cancelled";
                                                break;
                                        }
                                        
                                        echo "<td><span class='status-badge " . $statusClass . "'>" . $statusText . "</span></td>";
                                        
                                        // Improve the View button to match your screenshot
                                        echo "<td><a href='order-details.php?id=" . $row["order_group_id"] . "' class='view-btn'>View</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='no-data'>No recent orders found</td></tr>";
                                }
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the sidebar and toggle button
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        
        // Add click event to toggle sidebar
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !event.target.classList.contains('sidebar-toggle') &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
        
        // Highlight current page in sidebar
        const currentPage = window.location.pathname.split('/').pop();
        const menuItems = document.querySelectorAll('.menu-item');
        
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href === currentPage || (currentPage === '' && href === 'index.php')) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    });
</script>