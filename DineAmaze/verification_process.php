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
            // Instead of looking for existing items, let's create a new entry
            // using the information from the session
            
            // Add email column to takeout_order_items if it doesn't exist
            $sql = "SHOW COLUMNS FROM takeout_order_items LIKE 'email'";
            $result = $conn->query($sql);
            
            if ($result->num_rows == 0) {
                // Email column doesn't exist, add it
                $sql = "ALTER TABLE takeout_order_items ADD COLUMN email VARCHAR(255)";
                if (!$conn->query($sql)) {
                    echo "Error adding email column: " . $conn->error;
                    exit();
                }
            }
            
            // First, let's insert the order items from the session
            // Before inserting items, generate a unique order group ID
            $orderGroupId = uniqid('order_');
            
            // Add order_group_id column if it doesn't exist
            $sql = "SHOW COLUMNS FROM takeout_order_items LIKE 'order_group_id'";
            $result = $conn->query($sql);
            
            if ($result->num_rows == 0) {
                // order_group_id column doesn't exist, add it
                $sql = "ALTER TABLE takeout_order_items ADD COLUMN order_group_id VARCHAR(50)";
                if (!$conn->query($sql)) {
                    echo "Error adding order_group_id column: " . $conn->error;
                    exit();
                }
            }
            
            // Then when inserting items, include the order group ID
            if (isset($order['items']) && is_array($order['items']) && count($order['items']) > 0) {
                // Insert each item
                foreach ($order['items'] as $item) {
                    $itemName = mysqli_real_escape_string($conn, $item['item_name']);
                    $quantity = (int)$item['quantity'];
                    $price = (float)$item['price'];
                    $email = mysqli_real_escape_string($conn, $order['email']);
                    
                    $sql = "INSERT INTO takeout_order_items (item_name, quantity, price, email, order_group_id) 
                            VALUES ('$itemName', $quantity, $price, '$email', '$orderGroupId')";
                    $conn->query($sql);
                }
                
                // Get the ID of the last inserted item
                $orderId = $conn->insert_id;
            } else {
                // If no items in session, check if we have any information about selected items
                $fullName = mysqli_real_escape_string($conn, $order['fullName']);
                $email = mysqli_real_escape_string($conn, $order['email']);
                
                // Debug the session data
                error_log("Session order data: " . print_r($order, true));
                
                // Get the menu prices from the same array used in takeout_process.php
                $prices = [
                    'Momo' => 150,
                    'Dal Bhat Tarkari' => 250,
                    'Chicken Burger' => 200,
                    'Fresh Juice' => 120,
                    'Milkshake' => 150
                ];
                
                // Check if we have selectedItemsData in the session
                if (isset($order['selectedItemsData']) && !empty($order['selectedItemsData'])) {
                    $selectedItems = json_decode($order['selectedItemsData'], true);
                    if (!empty($selectedItems)) {
                        // Process all selected items instead of just the first one
                        foreach ($selectedItems as $item) {
                            $selectedItem = mysqli_real_escape_string($conn, $item['name']);
                            $quantity = (int)$item['quantity'];
                            $price = (float)$item['price'];
                            $email = mysqli_real_escape_string($conn, $order['email']);
                            
                            $sql = "INSERT INTO takeout_order_items (item_name, quantity, price, email, order_group_id) 
                                    VALUES ('$selectedItem', $quantity, $price, '$email', '$orderGroupId')";
                            $conn->query($sql);
                        }
                        
                        // Get the ID of the last inserted item
                        $orderId = $conn->insert_id;
                    } else {
                        // Fallback to Milkshake if selected items is empty
                        $selectedItem = 'Milkshake';
                        $quantity = 1;
                        $price = $prices[$selectedItem];
                        $email = mysqli_real_escape_string($conn, $order['email']);
                        
                        $sql = "INSERT INTO takeout_order_items (item_name, quantity, price, email, order_group_id) 
                                VALUES ('$selectedItem', $quantity, $price, '$email', '$orderGroupId')";
                        $conn->query($sql);
                        $orderId = $conn->insert_id;
                    }
                } 
                // Check if we have any item information in the POST data
                else if (isset($_POST['menuItems']) && is_array($_POST['menuItems']) && !empty($_POST['menuItems'])) {
                    $selectedItem = $_POST['menuItems'][0]; // Get the first selected item
                    $quantity = isset($_POST['quantity'][$selectedItem]) ? (int)$_POST['quantity'][$selectedItem] : 1;
                    $price = isset($prices[$selectedItem]) ? $prices[$selectedItem] : 150;
                } else {
                    // Fallback to a default item from the menu
                    $selectedItem = 'Milkshake'; // Changed from array_key_first($prices)
                    $quantity = 1;
                    $price = $prices[$selectedItem];
                }
                
                $sql = "INSERT INTO takeout_order_items (item_name, quantity, price, email, order_group_id) 
                        VALUES ('$selectedItem', $quantity, $price, '$email', '$orderGroupId')";
                
                $conn->query($sql);
                $orderId = $conn->insert_id;
            }
            
            // Create a table for ID documents if it doesn't exist
            $sql = "CREATE TABLE IF NOT EXISTS id_documents (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                order_item_id INT(11) NOT NULL,
                document_path VARCHAR(255) NOT NULL,
                upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
                
            if ($conn->query($sql) !== TRUE) {
                echo "Error creating table: " . $conn->error;
                exit();
            }
            
            // Insert file path into database
            $filePath = mysqli_real_escape_string($conn, $targetFilePath);
            $sql = "INSERT INTO id_documents (order_id, document_path) VALUES ('$orderId', '$filePath')";
                
                if ($conn->query($sql) === TRUE) {
                    // Add status column to takeout_order_items if it doesn't exist
                    $sql = "SHOW COLUMNS FROM takeout_order_items LIKE 'status'";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows == 0) {
                        // Status column doesn't exist, add it
                        $sql = "ALTER TABLE takeout_order_items ADD COLUMN status ENUM('pending', 'verified', 'completed') NOT NULL DEFAULT 'pending'";
                        $conn->query($sql);
                    }
                    
                    // Update order status to 'verified'
                    // Around line 161, there's an UPDATE query using an 'id' column that doesn't exist
                    // Let's modify it to use the correct primary key column
                    
                    // First, let's check what columns are available in the table
                    $checkPrimaryKey = "SHOW COLUMNS FROM takeout_order_items";
                    $primaryKeyResult = $conn->query($checkPrimaryKey);
                    $primaryKeyColumn = "";
                    
                    while ($column = $primaryKeyResult->fetch_assoc()) {
                        if ($column['Key'] == 'PRI') {
                            $primaryKeyColumn = $column['Field'];
                            break;
                        }
                    }
                    
                    // If we found a primary key, use it; otherwise, use a combination of fields
                    if (!empty($primaryKeyColumn)) {
                        $sql = "UPDATE takeout_order_items SET status = 'verified' WHERE $primaryKeyColumn = '$orderId'";
                    } else {
                        // Use a combination of email and order_date as fallback
                        $sql = "UPDATE takeout_order_items SET status = 'verified' WHERE email = '$email' AND order_date = '$orderDate'";
                    }
                    
                    $conn->query($sql);
                    
                    if ($conn->query($sql) === TRUE) {
                        // Clear the session data
                        unset($_SESSION['takeout_order']);
                        
                        // Redirect to confirmation page
                        header("Location: takeout_confirmation.php?order_id=" . $orderId);
                        exit();
                    } else {
                        echo "Error updating order status: " . $conn->error;
                    }
                } else {
                    echo "Error saving document information: " . $conn->error;
                }
            } else {
                echo "Error: No order items found in database.";
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Sorry, only JPG, JPEG, and PNG files are allowed to upload.";
    }

$conn->close();
?>