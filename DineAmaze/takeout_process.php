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
        
        // Redirect to verification page
        header("Location: Verification.php");
        exit();
        
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