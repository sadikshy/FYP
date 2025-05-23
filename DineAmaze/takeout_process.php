<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dineamaze_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Debug the POST data
        if(empty($_POST)) {
            throw new Exception("No data received");
        }
        
        // Define menu items and prices
        $prices = [
            'Momo' => 150.00,
            'Dal Bhat Tarkari' => 250.00,
            'Chicken Burger' => 200.00,
            'Fresh Juice' => 120.00,
            'Milkshake' => 150.00
        ];
        
        // Initialize total and items array
        $totalAmount = 0;
        $orderItems = [];
        
        // Process each selected item
        if (isset($_POST['menuItems']) && is_array($_POST['menuItems'])) {
            foreach ($_POST['menuItems'] as $itemName) {
                $quantityKey = "quantity[$itemName]";
                
                if (isset($_POST['quantity'][$itemName]) && $_POST['quantity'][$itemName] > 0) {
                    $quantity = (int)$_POST['quantity'][$itemName];
                    $price = $prices[$itemName] ?? 0;
                    $itemTotal = $price * $quantity;
                    $totalAmount += $itemTotal;
                    
                    // Insert into database with individual item price (not multiplied)
                    $stmt = $conn->prepare("INSERT INTO takeout_order_items (item_name, quantity, price) VALUES (?, ?, ?)");
                    $stmt->bind_param("sid", $itemName, $quantity, $price);
                    $stmt->execute();
                    
                    // Add to items array
                    $orderItems[] = [
                        'item_name' => $itemName,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $itemTotal
                    ];
                }
            }
        }
        
        // Store the selected items data in the session
        if (isset($_POST['selectedItemsData'])) {
            $selectedItemsData = $_POST['selectedItemsData'];
        }
        
        // Store order details in session - use the format expected by Verification.php
        $_SESSION['takeout_order'] = [
            'fullName' => $_POST['fullName'],
            'email' => $_POST['email'],
            'contactNumber' => $_POST['contactNumber'],
            'pickupTime' => $_POST['time'],
            'items' => $orderItems,
            'total' => $totalAmount,
            'selectedItemsData' => $selectedItemsData ?? null
        ];
        
        // Check if user is already verified and skip_verification is set
        if (isset($_POST['skip_verification']) && $_POST['skip_verification'] == 1 && isset($_SESSION['is_verified']) && $_SESSION['is_verified'] == 1) {
            // Skip verification and go straight to confirmation
            $orderGroupId = uniqid('order_');
            $userEmail = $_SESSION['user_email'];
            $userId = $_SESSION['user_id'];
            $fullName = $_POST['fullName'];
            $contactNumber = $_POST['contactNumber'];
            $pickupTime = $_POST['time'];
            
            // Insert order items directly
            foreach ($_SESSION['cart'] as $item) {
                $itemName = mysqli_real_escape_string($conn, $item['name']);
                $quantity = (int)$item['quantity'];
                $price = (float)$item['price'];
                
                $sql = "INSERT INTO takeout_order_items (item_name, quantity, price, email, order_group_id, status, pickup_time) 
                        VALUES ('$itemName', $quantity, $price, '$userEmail', '$orderGroupId', 'verified', '$pickupTime')";
                
                if (!$conn->query($sql)) {
                    echo "Error inserting item: " . $conn->error;
                }
            }
            
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
                        VALUES ('$orderGroupId', '$fullName', '$userEmail', '$contactNumber', '$pickupTime')";
                
                if ($conn->query($sql)) {
                    // Get the last inserted order ID
                    $orderId = $conn->insert_id;
                    
                    // Redirect to confirmation page
                    header("Location: takeout_confirmation.php?order_id=$orderId&status=verified");
                    exit();
                } else {
                    echo "Error inserting customer record: " . $conn->error;
                }
            } else {
                echo "Error creating customers table: " . $conn->error;
            }
        } else {
            // Redirect to verification page for non-verified users
            header("Location: Verification.php");
            exit();
        }
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        // Debug output
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
    }
}

$conn->close();
?>