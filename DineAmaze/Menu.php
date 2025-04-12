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
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="hero-section">
        <h1 class="hero-title">Our Menu</h1>
        <div>
            <span class="hero-subtitle">Select your desire items</span>
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
            <!-- Search and filter options -->
            <div class="menu-search-filters">
                <div class="search-container">
                    <input type="text" id="dish-search" placeholder="Search dishes...">
                    <button id="search-button"><i class="fas fa-search"></i></button>
                </div>
                <div class="additional-filters">
                    <label class="filter-checkbox">
                        <input type="checkbox" id="customizable-filter">
                        <span class="checkmark"></span>
                        Customizable Items
                    </label>
                    <label class="filter-checkbox">
                        <input type="checkbox" id="offer-filter">
                        <span class="checkmark"></span>
                        Special Offers
                    </label>
                </div>
            </div>
            
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

                            echo "<div class='dish-item' data-customizable='{$customizable_attr}' data-offer='{$offer_attr}' data-name='{$name}' data-ingredients='{$ingredients}'>";
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
    

   
   
    document.addEventListener('DOMContentLoaded', function() {
        // Add to cart functionality for direct add buttons
        const addToCartButtons = document.querySelectorAll('.add-to-cart-direct');
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                const itemName = this.getAttribute('data-name');
                const itemPrice = parseFloat(this.getAttribute('data-price'));
                const itemImage = this.getAttribute('data-image');
                
                // Create cart item
                const cartItem = {
                    id: itemId,
                    name: itemName,
                    image: itemImage,
                    quantity: 1,
                    price: itemPrice,
                    toppings: [],
                    removed_ingredients: [],
                    special_instructions: ''
                };
                
                // Send AJAX request to add item to cart
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(cartItem)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart count in header
                        const cartCount = document.querySelector('.cart-count');
                        cartCount.textContent = data.cartCount;
                        
                        // Show success message
                        alert(data.message);
                    } else {
                        alert('Error adding item to cart: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while adding the item to cart.');
                });
            });
        });
    });
    </script>
    </body>
    </html>