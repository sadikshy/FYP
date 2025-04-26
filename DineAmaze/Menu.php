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
    <style>
        /* Pagination styles */
        .pagination-container {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 30px 0;
        }
        
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .page-link {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 3px;
            color: #333;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .page-link:hover {
            background-color: #e9ecef;
            color: #000;
        }
        
        .page-link.active {
            background-color: #6a5acd;
            color: white;
            border-color: #6a5acd;
        }
        
        .page-ellipsis {
            padding: 8px 12px;
            color: #6c757d;
        }
        
        /* Hero search and filter styles */
        .hero-search-filters {
            margin-top: 30px;
            width: 80%;
            max-width: 800px;
        }
        
        .search-container {
            display: flex;
            margin-bottom: 15px;
        }
        
        #dish-search {
            flex: 1;
            padding: 12px 15px;
            border: none;
            border-radius: 30px 0 0 30px;
            font-size: 16px;
            outline: none;
        }
        
        #search-button {
            background-color: #ff8c00; /* Orange color */
            color: #000; /* Black color for icon */
            border: none;
            border-radius: 0 30px 30px 0;
            padding: 0 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        #search-button:hover {
            background-color: #e67e00;
        }
        
        .dietary-filter {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        
        .diet-btn {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            padding: 8px 15px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .diet-btn:hover, .diet-btn.active {
            background-color: #ff8c00;
            color: #000;
            border-color: #ff8c00;
        }
        
        /* Diet type indicators */
        .diet-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .vegetarian-indicator {
            background-color: #4CAF50;
        }
        
        .ovo-vegetarian-indicator {
            background-color: #FFC107;
        }
        
        .non-vegetarian-indicator {
            background-color: #F44336;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="hero-section">
        <h1 class="hero-title">Our Menu</h1>
        <div>
            <span class="hero-subtitle">Select your desire items</span>
        </div>
        
        <!-- Search and filter options moved to hero section -->
        <div class="hero-search-filters">
            <div class="search-container">
                <input type="text" id="dish-search" placeholder="Search dishes...">
                <button id="search-button"><i class="fas fa-search"></i></button>
            </div>
            <div class="dietary-filter">
                <button class="diet-btn active" data-diet="all">All</button>
                <button class="diet-btn" data-diet="vegetarian">Vegetarian</button>
                <button class="diet-btn" data-diet="ovo-vegetarian">Ovo-Vegetarian</button>
                <button class="diet-btn" data-diet="non-vegetarian">Non-Vegetarian</button>
            </div>
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
                // Connecting with database
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
            <!-- Search and filter options removed from here -->
            
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
                    
                    // Pagination setup
                    $items_per_page = 6;
                    $total_items = $item_result->num_rows;
                    $total_pages = ceil($total_items / $items_per_page);
                    
                    // Get current page or set to 1 if not specified
                    $current_page = isset($_GET['page_' . $cat_id]) ? $_GET['page_' . $cat_id] : 1;
                    $current_page = max(1, min($current_page, $total_pages)); // Ensure valid page number
                    
                    // Calculate starting item index
                    $start_index = ($current_page - 1) * $items_per_page;
                    
                    // Modify query to include pagination
                    $item_sql = "SELECT * FROM menu_item WHERE category_id = $cat_id LIMIT $start_index, $items_per_page";
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

                            // Add data attributes for filtering
                            $customizable_attr = $item['is_customizable'] ? 'true' : 'false';
                            $offer_attr = $offer ? 'true' : 'false';
                            
                            // Determine diet type based on ingredients (simplified logic)
                            $diet_type = "non-vegetarian"; // Default
                            $ingredients_lower = strtolower($ingredients);
                            if (!preg_match('/(chicken|beef|pork|mutton|fish|seafood|meat)/i', $ingredients_lower)) {
                                if (preg_match('/(egg)/i', $ingredients_lower)) {
                                    $diet_type = "ovo-vegetarian";
                                } else {
                                    $diet_type = "vegetarian";
                                }
                            }

                            echo "<div class='dish-item' data-customizable='{$customizable_attr}' data-offer='{$offer_attr}' data-name='{$name}' data-ingredients='{$ingredients}' data-diet='{$diet_type}'>";
                            // Check if image exists and display from the correct directory
                            if (!empty($image)) {
                                echo "<img src='assets/images/menu/{$image}' alt='{$name}'>";
                            } else {
                                echo "<img src='assets/images/default-food.jpg' alt='{$name}'>";
                            }
                            echo "<h4>{$name} - Rs. {$final_price}</h4>";
                            echo "<p>Ingredients: {$ingredients}</p>";
                            
                            // Display customization button only for customizable items
                            if ($item['is_customizable']) {
                                echo "<div class='item-actions'>";
                                echo "<a href='Customization.php?item_id={$item_id}' class='customize-btn'>Customize</a>";
                                echo "</div>";
                            } else {
                                echo "<div class='item-actions'>";
                                echo "<button class='add-to-cart-direct' data-id='{$item_id}' data-name='{$name}' data-price='{$final_price}' data-image='{$image}'>Add to Cart</button>";
                                echo "</div>";
                            }
                            
                            if ($offer) {
                                echo "<p><s>Rs. {$price}</s> <strong style='color: green;'>Offer: Rs. {$offer}</strong></p>";
                            }
                            echo "</div>";
                        }
                        
                        echo "</div>"; // Close dish-grid before pagination
                        
                        // Add pagination controls if there are multiple pages
                        if ($total_pages > 1) {
                            echo "<div class='pagination-container'>";
                            echo "<div class='pagination'>";
                            
                            // Previous page link
                            if ($current_page > 1) {
                                echo "<a href='?page_{$cat_id}=" . ($current_page - 1) . "' class='page-link'>&laquo; Prev</a>";
                            }
                            
                            // Page numbers
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($total_pages, $current_page + 2);
                            
                            if ($start_page > 1) {
                                echo "<a href='?page_{$cat_id}=1' class='page-link'>1</a>";
                                if ($start_page > 2) {
                                    echo "<span class='page-ellipsis'>...</span>";
                                }
                            }
                            
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                $active_class = ($i == $current_page) ? " active" : "";
                                echo "<a href='?page_{$cat_id}={$i}' class='page-link{$active_class}'>{$i}</a>";
                            }
                            
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo "<span class='page-ellipsis'>...</span>";
                                }
                                echo "<a href='?page_{$cat_id}={$total_pages}' class='page-link'>{$total_pages}</a>";
                            }
                            
                            // Next page link
                            if ($current_page < $total_pages) {
                                echo "<a href='?page_{$cat_id}=" . ($current_page + 1) . "' class='page-link'>Next &raquo;</a>";
                            }
                            
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "</div>"; // Close dish-grid if no items
                        echo "<p>No items in this category.</p>";
                    }

                    echo "</div>"; // Close menu-category div
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
    <script src="js/menu-filter.js"></script>
    </body>
    </html>