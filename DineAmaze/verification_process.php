<?php
// Start the session
session_start();

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

// Check if takeout order details exist in session
if (!isset($_SESSION['takeout_order'])) {
    // Redirect to takeout page if no order details found
    header("Location: Takeout.php");
    exit();
}

// Check if cart exists in session
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    // Redirect to cart page if cart is empty
    header("Location: cart.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get order details from session
    $order = $_SESSION['takeout_order'];
    
    // Handle file upload
    $targetDir = "uploads/";
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = basename($_FILES["id_document"]["name"]);
    $targetFilePath = $targetDir . time() . '_' . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    
    // Allow only jpeg and png formats
    $allowTypes = array('jpg', 'jpeg', 'png');
    
    if (in_array($fileType, $allowTypes)) {
        // Upload file to server
        if (move_uploaded_file($_FILES["id_document"]["tmp_name"], $targetFilePath)) {
            // Generate a unique order group ID
            $orderGroupId = uniqid('order_');
            
            // Add necessary columns if they don't exist
            $columnsToCheck = [
                'email' => 'VARCHAR(255)',
                'order_group_id' => 'VARCHAR(50)',
                'status' => "ENUM('pending', 'verified', 'completed') DEFAULT 'pending'"
            ];
            
            foreach ($columnsToCheck as $column => $definition) {
                $sql = "SHOW COLUMNS FROM takeout_order_items LIKE '$column'";
                $result = $conn->query($sql);
                
                if ($result->num_rows == 0) {
                    $sql = "ALTER TABLE takeout_order_items ADD COLUMN $column $definition";
                    if (!$conn->query($sql)) {
                        echo "Error adding $column column: " . $conn->error;
                        exit();
                    }
                }
            }
            
            // Get cart items from session
            $cartItems = $_SESSION['cart'];
            $email = mysqli_real_escape_string($conn, $order['email']);
            $fullName = mysqli_real_escape_string($conn, $order['fullName']);
            $contactNumber = mysqli_real_escape_string($conn, $order['contactNumber']);
            $pickupTime = isset($order['pickupTime']) ? mysqli_real_escape_string($conn, $order['pickupTime']) : 
                         (isset($order['time']) ? mysqli_real_escape_string($conn, $order['time']) : '');
            
            // Insert each cart item into the database
            foreach ($cartItems as $item) {
                $itemName = mysqli_real_escape_string($conn, $item['name']);
                $quantity = (int)$item['quantity'];
                $price = (float)$item['price'];
                
                $sql = "INSERT INTO takeout_order_items (item_name, quantity, price, email, order_group_id, status) 
                        VALUES ('$itemName', $quantity, $price, '$email', '$orderGroupId', 'pending')";
                
                if (!$conn->query($sql)) {
                    echo "Error inserting item: " . $conn->error;
                }
            }
            
            // Store ID document information
            $sql = "CREATE TABLE IF NOT EXISTS id_documents (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                document_path VARCHAR(255) NOT NULL,
                upload_date DATETIME DEFAULT CURRENT_TIMESTAMP
            )";
            
            if ($conn->query($sql)) {
                // Check if order_group_id column exists in id_documents table
                $sql = "SHOW COLUMNS FROM id_documents LIKE 'order_group_id'";
                $result = $conn->query($sql);
                
                if ($result->num_rows == 0) {
                    // Add order_group_id column if it doesn't exist
                    $sql = "ALTER TABLE id_documents ADD COLUMN order_group_id VARCHAR(50)";
                    if (!$conn->query($sql)) {
                        echo "Error adding order_group_id column to id_documents: " . $conn->error;
                        exit();
                    }
                }
                
                // Check if order_id column exists in id_documents table
                $sql = "SHOW COLUMNS FROM id_documents LIKE 'order_id'";
                $result = $conn->query($sql);
                
                if ($result->num_rows == 0) {
                    // Add order_id column if it doesn't exist
                    $sql = "ALTER TABLE id_documents ADD COLUMN order_id INT(11)";
                    if (!$conn->query($sql)) {
                        echo "Error adding order_id column to id_documents: " . $conn->error;
                        exit();
                    }
                }
                
                // Now insert document record with order_group_id
                $sql = "INSERT INTO id_documents (document_path, order_group_id) VALUES ('$targetFilePath', '$orderGroupId')";
                if ($conn->query($sql)) {
                    // Store customer information
                    $sql = "CREATE TABLE IF NOT EXISTS takeout_customers (
                        id INT(11) AUTO_INCREMENT PRIMARY KEY,
                        order_group_id VARCHAR(50) NOT NULL,
                        full_name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL,
                        contact_number VARCHAR(20) NOT NULL,
                        pickup_time VARCHAR(50),
                        order_date DATETIME DEFAULT CURRENT_TIMESTAMP
                    )";
                    
                    if ($conn->query($sql)) {
                        // Insert customer record
                        $sql = "INSERT INTO takeout_customers (order_group_id, full_name, email, contact_number, pickup_time) 
                                VALUES ('$orderGroupId', '$fullName', '$email', '$contactNumber', '$pickupTime')";
                        
                        if ($conn->query($sql)) {
                            // Get the last inserted order ID
                            $orderId = $conn->insert_id;
                            
                            // Redirect to confirmation page
                            header("Location: takeout_confirmation.php?order_id=$orderId&status=pending");
                            exit();
                        } else {
                            echo "Error inserting customer record: " . $conn->error;
                        }
                    } else {
                        echo "Error creating customers table: " . $conn->error;
                    }
                } else {
                    echo "Error inserting document record: " . $conn->error;
                }
            } else {
                echo "Error creating documents table: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Sorry, only JPG, JPEG & PNG files are allowed.";
    }
} else {
    // Redirect to verification page if not a POST request
    header("Location: Verification.php");
    exit();
}

$conn->close();
?>