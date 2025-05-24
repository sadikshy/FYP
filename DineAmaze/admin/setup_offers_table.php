<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Offers Table - DineAmaze</title>
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
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
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
        .info {
            color: #0c5460;
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
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
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>DineAmaze Offers Table Setup</h1>
        
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
            exit;
        }
        
        echo "<div class='info'>Connected to database: " . $dbname . "</div>";

        // Check if offers table exists
        $tableExists = false;
        $result = $conn->query("SHOW TABLES LIKE 'offers'");
        if ($result->num_rows > 0) {
            $tableExists = true;
            echo "<div class='info'>The offers table already exists.</div>";
        }

        // If table doesn't exist, create it
        if (!$tableExists) {
            echo "<div class='info'>Creating offers table...</div>";
            
            $sql = "CREATE TABLE `offers` (
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
            
            if ($conn->query($sql) === TRUE) {
                echo "<div class='success'>Offers table created successfully!</div>";
                $tableExists = true;
            } else {
                echo "<div class='error'>Error creating table: " . $conn->error . "</div>";
            }
        }

        // Check if is_hidden column exists
        if ($tableExists) {
            $result = $conn->query("SHOW COLUMNS FROM `offers` LIKE 'is_hidden'");
            if ($result->num_rows == 0) {
                echo "<div class='info'>Adding is_hidden column to offers table...</div>";
                
                $sql = "ALTER TABLE `offers` ADD `is_hidden` TINYINT(1) NOT NULL DEFAULT 0";
                if ($conn->query($sql) === TRUE) {
                    echo "<div class='success'>is_hidden column added successfully!</div>";
                } else {
                    echo "<div class='error'>Error adding is_hidden column: " . $conn->error . "</div>";
                }
            } else {
                echo "<div class='info'>is_hidden column already exists.</div>";
            }
        }

        // Check if there are any offers in the table
        if ($tableExists) {
            $result = $conn->query("SELECT COUNT(*) as count FROM offers");
            $row = $result->fetch_assoc();
            $offerCount = $row['count'];
            
            echo "<div class='info'>Found " . $offerCount . " offers in the database.</div>";
            
            // If no offers, add sample offers
            if ($offerCount == 0) {
                echo "<div class='info'>Adding sample offers...</div>";
                
                // Create images/offers directory if it doesn't exist
                if (!file_exists("../images/offers")) {
                    mkdir("../images/offers", 0777, true);
                    echo "<div class='info'>Created images/offers directory.</div>";
                }
                
                $sql = "INSERT INTO `offers` (`title`, `description`, `badge`, `image`, `valid_until`, `is_ongoing`, `section`, `how_to_take`, `is_hidden`) VALUES 
                ('Family Meal Deal', 'Order any 4 main courses and get 20% off your total bill. Perfect for family gatherings!', '20% OFF', 'images/offers/family-meal.jpg', NULL, 1, 'current-offers', 'Simply mention this offer to your server when dining with your family at DineAmaze.', 0),
                ('Free Dessert', 'Spend over Rs. 1500 on your meal and receive a complimentary dessert of your choice.', 'FREE', 'images/offers/free-dessert.jpg', '2025-12-31', 0, 'special-deals', 'Your server will automatically offer you a free dessert when your bill exceeds Rs. 1500.', 0),
                ('Happy Hour Special', 'Enjoy 15% off on all beverages between 4PM and 6PM, Monday and Thursday.', 'HAPPY HOUR', 'images/offers/happy-hour.png', NULL, 1, 'celebration-offers', 'Visit us during happy hours and the discount will be automatically applied to your beverage order.', 0)";
                
                if ($conn->query($sql) === TRUE) {
                    echo "<div class='success'>Sample offers added successfully!</div>";
                } else {
                    echo "<div class='error'>Error adding sample offers: " . $conn->error . "</div>";
                }
            }
        }

        // Display current offers
        if ($tableExists) {
            $result = $conn->query("SELECT * FROM offers");
            
            if ($result->num_rows > 0) {
                echo "<h2>Current Offers in Database</h2>";
                echo "<pre>";
                echo "ID | Title | Badge | Section | Is Hidden\n";
                echo "-------------------------------------------\n";
                
                while ($row = $result->fetch_assoc()) {
                    echo $row['offer_id'] . " | " . $row['title'] . " | " . $row['badge'] . " | " . $row['section'] . " | " . ($row['is_hidden'] ? "Yes" : "No") . "\n";
                }
                
                echo "</pre>";
            }
        }

        // Close connection
        $conn->close();
        ?>
        
        <p>This script has checked and set up your offers table. You can now use the offers management system.</p>
        
        <a href="manage_offers.php" class="btn">Go to Offers Management</a>
    </div>
</body>
</html>
