<?php
// Script to set up the database and required tables for food customization

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dineamaze_database";

echo "<h2>DineAmaze Food Customization Setup</h2>";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<p>Connection failed: " . $conn->connect_error . "</p>");
}

echo "<p>Connected to database successfully.</p>";

// Create food_customizations table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS `food_customizations` (
  `customization_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `customization_name` varchar(50) NOT NULL,
  `toppings` varchar(255) DEFAULT NULL,
  `removed_ingredients` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `special_instructions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_used` timestamp NULL DEFAULT NULL,
  `use_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customization_id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql) === TRUE) {
    echo "<p>✅ Table 'food_customizations' created successfully or already exists.</p>";
} else {
    echo "<p>❌ Error creating table: " . $conn->error . "</p>";
}

// Add sample customization for testing if none exist
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM food_customizations");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // Add a sample customization
    $stmt = $conn->prepare("INSERT INTO food_customizations 
                      (user_id, item_id, customization_name, toppings, removed_ingredients, quantity, special_instructions)
                      VALUES (1, 1, 'My Favorite Pizza', 'Extra Cheese, Pepperoni', 'Onions', 1, 'Cook it well done please')");
    
    if ($stmt->execute()) {
        echo "<p>✅ Sample customization added for testing.</p>";
    } else {
        echo "<p>❌ Error adding sample customization: " . $stmt->error . "</p>";
    }
}

// Verify includes directory and db_connect.php file
if (!file_exists('includes')) {
    mkdir('includes', 0755);
    echo "<p>✅ Created includes directory.</p>";
}

if (!file_exists('includes/db_connect.php')) {
    $db_content = '<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dineamaze_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Log error to file instead of displaying it
    error_log("Connection failed: " . $conn->connect_error, 3, "db_error.log");
    
    // Return JSON error for AJAX requests
    if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest") {
        header("Content-Type: application/json");
        echo json_encode(["status" => "error", "message" => "Database connection failed"]);
        exit;
    }
    
    // For regular page loads, show a user-friendly message
    die("We\'re experiencing technical difficulties. Please try again later.");
}

// Set character set
$conn->set_charset("utf8mb4");
?>';
    file_put_contents('includes/db_connect.php', $db_content);
    echo "<p>✅ Created database connection file.</p>";
} else {
    echo "<p>✅ Database connection file already exists.</p>";
}

// Check if script is included in the Customization.php
$customization_file = file_get_contents('Customization.php');
if (strpos($customization_file, 'food-customization.js') === false) {
    echo "<p>❌ Warning: food-customization.js might not be included in Customization.php.</p>";
} else {
    echo "<p>✅ food-customization.js is included in Customization.php.</p>";
}

// Check if buttons exist in Customization.php
if (strpos($customization_file, 'save-customization-btn') === false) {
    echo "<p>❌ Warning: Save Custom button might be missing in Customization.php.</p>";
} else {
    echo "<p>✅ Save Custom button is present in Customization.php.</p>";
}

// Add sample session data for testing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set user_id in session if not already set
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Use user ID 1 for testing
    echo "<p>✅ Set user_id=1 in session for testing.</p>";
} else {
    echo "<p>✅ Session already has user_id: " . $_SESSION['user_id'] . "</p>";
}

echo "<h3>Setup Complete!</h3>";
echo "<p>The food customization feature should now be fully functional.</p>";
echo "<p>You can now <a href='Customization.php'>return to the Customization page</a> and test the feature.</p>";

// Close the connection
$conn->close();
?>
