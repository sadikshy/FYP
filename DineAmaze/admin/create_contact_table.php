<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to create contact_message table
$sql = "CREATE TABLE IF NOT EXISTS contact_message (
    message_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    submission_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_read ENUM('Yes', 'No') DEFAULT 'No',
    admin_response TEXT,
    response_date DATETIME
)";

if ($conn->query($sql) === TRUE) {
    echo "Table contact_message created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
