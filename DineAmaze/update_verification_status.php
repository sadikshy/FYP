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

// Check if is_verified column exists in user table
$sql = "SHOW COLUMNS FROM user LIKE 'is_verified'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // Add is_verified column if it doesn't exist
    $sql = "ALTER TABLE user ADD COLUMN is_verified TINYINT(1) DEFAULT 0";
    if ($conn->query($sql)) {
        echo "Successfully added is_verified column to user table.<br>";
    } else {
        echo "Error adding is_verified column: " . $conn->error . "<br>";
    }
} else {
    echo "is_verified column already exists in user table.<br>";
}

// Close connection
$conn->close();
echo "Database update completed.";
?>
