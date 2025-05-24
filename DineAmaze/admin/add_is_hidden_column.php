<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add is_hidden Column to Offers Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .success {
            color: green;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Update Tool</h1>
        
        <?php
        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "dineamaze_database";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            echo "<div class='error'>Connection failed: " . $conn->connect_error . "</div>";
        } else {
            // Check if is_hidden column exists
            $checkColumnQuery = "SHOW COLUMNS FROM `offers` LIKE 'is_hidden'";
            $columnResult = $conn->query($checkColumnQuery);

            if (!$columnResult) {
                echo "<div class='error'>Error checking for column: " . $conn->error . "</div>";
                
                // Check if the offers table exists
                $checkTableQuery = "SHOW TABLES LIKE 'offers'";
                $tableResult = $conn->query($checkTableQuery);
                
                if (!$tableResult || $tableResult->num_rows == 0) {
                    echo "<div class='error'>The 'offers' table does not exist. Creating it now...</div>";
                    
                    // Create the offers table
                    $createTableQuery = "CREATE TABLE `offers` (
                        `offer_id` int(11) NOT NULL AUTO_INCREMENT,
                        `title` varchar(255) NOT NULL,
                        `description` text NOT NULL,
                        `badge` varchar(50) NOT NULL,
                        `image` varchar(255) NOT NULL,
                        `valid_until` date DEFAULT NULL,
                        `is_ongoing` tinyint(1) DEFAULT 0,
                        `section` varchar(50) NOT NULL,
                        `how_to_take` text NOT NULL,
                        `is_hidden` tinyint(1) NOT NULL DEFAULT 0,
                        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                        PRIMARY KEY (`offer_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
                    
                    if ($conn->query($createTableQuery) === TRUE) {
                        echo "<div class='success'>The 'offers' table was created successfully.</div>";
                    } else {
                        echo "<div class='error'>Error creating table: " . $conn->error . "</div>";
                    }
                }
            } else if ($columnResult->num_rows == 0) {
                // Column doesn't exist, add it
                $alterQuery = "ALTER TABLE `offers` ADD `is_hidden` TINYINT(1) NOT NULL DEFAULT 0";
                
                if ($conn->query($alterQuery) === TRUE) {
                    echo "<div class='success'>Column 'is_hidden' added successfully to offers table.</div>";
                } else {
                    echo "<div class='error'>Error adding column: " . $conn->error . "</div>";
                }
            } else {
                echo "<div class='success'>Column 'is_hidden' already exists in offers table.</div>";
            }

            // Update the section values which appear to be '0' instead of proper section names
            $updateSectionQuery = "UPDATE `offers` SET `section` = 'current-offers' WHERE `section` = '0'";
            if ($conn->query($updateSectionQuery) === TRUE) {
                echo "<div class='success'>Updated section values from '0' to 'current-offers'.</div>";
            } else {
                echo "<div class='error'>Error updating section values: " . $conn->error . "</div>";
            }
            
            // Check if there are any offers in the table
            $countQuery = "SELECT COUNT(*) as count FROM offers";
            $countResult = $conn->query($countQuery);
            $countRow = $countResult->fetch_assoc();
            
            if ($countRow['count'] == 0) {
                echo "<div class='error'>No offers found in the database. Adding sample offers...</div>";
                
                // Add sample offers
                $sampleOffersQuery = "INSERT INTO `offers` (`title`, `description`, `badge`, `image`, `valid_until`, `is_ongoing`, `section`, `how_to_take`, `is_hidden`) VALUES 
                    ('Family Meal Deal', 'Order any 4 main courses and get 20% off your total bill. Perfect for family gatherings!', '20% OFF', 'images/offers/family-meal.jpg', NULL, 1, 'current-offers', 'Simply mention this offer to your server when dining with your family at DineAmaze.', 0),
                    ('Free Dessert', 'Spend over Rs. 1500 on your meal and receive a complimentary dessert of your choice.', 'FREE', 'images/offers/free-dessert.jpg', '2025-12-31', 0, 'special-deals', 'Your server will automatically offer you a free dessert when your bill exceeds Rs. 1500.', 0),
                    ('Happy Hour Special', 'Enjoy 15% off on all beverages between 4PM and 6PM, Monday and Thursday.', 'HAPPY HOUR', 'images/offers/happy-hour.png', NULL, 1, 'celebration-offers', 'Visit us during happy hours and the discount will be automatically applied to your beverage order.', 0)";
                
                if ($conn->query($sampleOffersQuery) === TRUE) {
                    echo "<div class='success'>Sample offers added successfully!</div>";
                } else {
                    echo "<div class='error'>Error adding sample offers: " . $conn->error . "</div>";
                }
            } else {
                echo "<div class='success'>Found " . $countRow['count'] . " offers in the database.</div>";
            }

            // Close connection
            $conn->close();
        }
        ?>
        
        <a href="manage_offers.php" class="btn">Return to Offers Management</a>
    </div>
</body>
</html>
