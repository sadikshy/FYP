<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - DineAmaze</title>
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/Menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-solid-straight/css/uicons-solid-straight.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-chubby/css/uicons-solid-chubby.css'>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="hero-section">
        <h1 class="hero-title">Our Menu</h1>
        <div>
            <span class="diamond"></span>
            <span class="hero-subtitle">Cooking Since</span>
            <span class="diamond"></span>
        </div>
    </div>

    <div class="menu-container">
        <div class="menu-sidebar">
            <div class="menu-filter">
                <h3>Categories</h3>
                <div class="filter-item active" data-filter="all">
                    <i class="fas fa-utensils"></i>
                    <span>All Items</span>
                </div>
                <?php
                // Database connection
                $conn = new mysqli("localhost", "root", "", "dineamaze_database");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                
                // Fetch categories for sidebar
                $sidebar_sql = "SELECT * FROM menu_category ORDER BY category_id";
                $sidebar_result = $conn->query($sidebar_sql);
                
                if ($sidebar_result->num_rows > 0) {
                    while ($category = $sidebar_result->fetch_assoc()) {
                        $cat_name = $category['category_name'];
                        $filter_class = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '', $cat_name)));
                        
                        // Determine icon based on category name (you can customize this)
                        $icon = "fas fa-utensils"; // default icon
                        if (stripos($cat_name, "nepali") !== false) {
                            $icon = "fi fi-sc-plate";
                        } elseif (stripos($cat_name, "street") !== false) {
                            $icon = "fas fa-hotdog";
                        } elseif (stripos($cat_name, "pizza") !== false || stripos($cat_name, "burger") !== false) {
                            $icon = "fas fa-hamburger";
                        } elseif (stripos($cat_name, "noodle") !== false || stripos($cat_name, "bowl") !== false) {
                            $icon = "fi fi-ss-bowl-chopsticks-noodles";
                        } elseif (stripos($cat_name, "dessert") !== false) {
                            $icon = "fas fa-ice-cream";
                        } elseif (stripos($cat_name, "beverage") !== false) {
                            $icon = "fas fa-mug-hot";
                        }
                        
                        echo "<div class='filter-item' data-filter='{$filter_class}'>";
                        echo "<i class='{$icon}'></i>";
                        echo "<span>{$cat_name}</span>";
                        echo "</div>";
                    }
                }
                ?>
            </div>
        </div>
        
        <div class="menu-content">
            <?php
            // 1. Database connection already established above
            
            // 2. Fetch categories
            $category_sql = "SELECT * FROM menu_category ORDER BY category_id";
            $category_result = $conn->query($category_sql);

            if ($category_result->num_rows > 0) {
                while ($category = $category_result->fetch_assoc()) {
                    $cat_id = $category['category_id'];
                    $cat_name = $category['category_name'];

                    // Convert category name to filter class
                    $filter_class = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '', $cat_name)));

                    echo "<div class='menu-category' data-category='{$filter_class}'>";
                    echo "<h3>{$cat_id}. {$cat_name}</h3>";
                    echo "<div class='dish-grid'>";

                    // 3. Fetch items in this category
                    $item_sql = "SELECT * FROM menu_item WHERE category_id = $cat_id";
                    $item_result = $conn->query($item_sql);

                    if ($item_result->num_rows > 0) {
                        while ($item = $item_result->fetch_assoc()) {
                            $name = $item['item_name'];
                            $image = $item['image_name'];
                            $ingredients = $item['ingredients'];
                            $is_custom = $item['is_customizable'] ? "Yes" : "No";
                            $item_id = $item['item_id']; // Get the item ID for customization
                            $price = number_format($item['price'], 2);
                            $offer = $item['offer_price'] ? number_format($item['offer_price'], 2) : null;
                            $final_price = $offer ?: $price;

                            echo "<div class='dish-item'>";
                            echo "<img src='images/Menu Photos/{$image}' alt='{$name}'>";
                            echo "<h4>{$name} - Rs. {$final_price}</h4>";
                            echo "<p>Ingredients: {$ingredients}</p>";
                            
                            // Display customization button only for customizable items
                            if ($item['is_customizable']) {
                                echo "<div class='item-actions'>";
                                echo "<a href='Customization.php?item_id={$item_id}' class='customize-btn'>Customize</a>";
                                echo "</div>";
                            } else {
                                echo "<p>Customizable: No</p>";
                            }
                            
                            if ($offer) {
                                echo "<p><s>Rs. {$price}</s> <strong style='color: green;'>Offer: Rs. {$offer}</strong></p>";
                            }
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No items in this category.</p>";
                    }

                    echo "</div></div>"; // Close dish-grid and category div
                }
            } else {
                echo "<p>No categories found.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript for filtering -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterItems = document.querySelectorAll('.filter-item');
            const menuCategories = document.querySelectorAll('.menu-category');
            
            filterItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all filter items
                    filterItems.forEach(filter => filter.classList.remove('active'));
                    
                    // Add active class to clicked filter
                    this.classList.add('active');
                    
                    const filterValue = this.getAttribute('data-filter');
                    
                    // Show/hide menu categories based on filter
                    if (filterValue === 'all') {
                        menuCategories.forEach(category => {
                            category.style.display = 'block';
                        });
                    } else {
                        menuCategories.forEach(category => {
                            if (category.getAttribute('data-category') === filterValue) {
                                category.style.display = 'block';
                            } else {
                                category.style.display = 'none';
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>