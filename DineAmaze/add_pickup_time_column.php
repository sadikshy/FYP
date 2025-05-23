<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dineamaze_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if pickup_time column exists in takeout_order_items table
$sql = "SHOW COLUMNS FROM takeout_order_items LIKE 'pickup_time'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // Add pickup_time column if it doesn't exist
    $sql = "ALTER TABLE takeout_order_items ADD COLUMN pickup_time VARCHAR(50)";
    if ($conn->query($sql)) {
        echo "Successfully added pickup_time column to takeout_order_items table.<br>";
    } else {
        echo "Error adding pickup_time column: " . $conn->error . "<br>";
    }
} else {
    echo "pickup_time column already exists in takeout_order_items table.<br>";
}

// Close connection
$conn->close();
echo "Database update completed.";
?>
