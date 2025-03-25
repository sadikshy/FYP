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

// Check if order_id is provided in URL
if (!isset($_GET['order_id'])) {
    header("Location: Takeout.php");
    exit();
}

$orderId = $_GET['order_id'];

// Get the order group ID for this order
$sql = "DESCRIBE takeout_order_items";
$result = $conn->query($sql);
$primaryKeyColumn = "";

while ($row = $result->fetch_assoc()) {
    if ($row['Key'] == 'PRI') {
        $primaryKeyColumn = $row['Field'];
        break;
    }
}

// If we found the primary key, use it
if (!empty($primaryKeyColumn)) {
    $sql = "SELECT order_group_id FROM takeout_order_items WHERE $primaryKeyColumn = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $orderGroupId = $row['order_group_id'];
        
        // Get all items in this order group
        $sql = "SELECT * FROM takeout_order_items WHERE order_group_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $orderGroupId);
        $stmt->execute();
        $orderItems = $stmt->get_result();
        
        // Get the first item for the top section details
        $sql = "SELECT * FROM takeout_order_items WHERE $primaryKeyColumn = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $orderDetails = $stmt->get_result()->fetch_assoc();
        
        // Calculate total
        $total = 0;
        $allItems = [];
        
        // In the PHP section, modify the unique items handling
        // Store all items to display in the top section
        while ($item = $orderItems->fetch_assoc()) {
            $allItems[] = $item;
            $total += $item['price'] * $item['quantity'];
        }
        
        // Reset the result pointer to use it again in the summary section
        $orderItems->data_seek(0);
        
        // Create a better array to track unique items by name only
        $uniqueItems = [];
        while ($item = $orderItems->fetch_assoc()) {
            $itemName = $item['item_name'];
            if (!isset($uniqueItems[$itemName])) {
                $uniqueItems[$itemName] = [
                    'name' => $itemName,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }
        }
    } else {
        echo "Order not found.";
        exit();
    }
} else {
    echo "Could not determine primary key column.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="styles.css">
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .order-details, .order-summary {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
        }
        .order-details p, .order-summary p {
            margin: 5px 0;
        }
        .order-summary {
            margin-top: 30px;
        }
        .total {
            font-weight: bold;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Confirmation</h1>
        
        <div class="order-details">
            <h2>Order Details:</h2>
            <p><strong>Order ID:</strong> <?php echo $orderId; ?></p>
            
            <!-- Display all items in the top section without duplicates -->
            <p><strong>Item:</strong> 
                <?php 
                $itemNames = [];
                foreach ($uniqueItems as $item) {
                    $itemNames[] = $item['name'] . " (x" . $item['quantity'] . ")";
                }
                echo implode(", ", $itemNames);
                ?>
            </p>
            
            <!-- Calculate and display total quantity of all items -->
            <p><strong>Quantity:</strong> <?php 
                // Reset the result pointer to use it again
                $orderItems->data_seek(0);
                
                // Create a better counting mechanism
                $totalQuantity = 0;
                $processedItems = [];
                
                while ($item = $orderItems->fetch_assoc()) {
                    $itemKey = $item['item_name'] . '_' . $item['quantity'];
                    if (!isset($processedItems[$itemKey])) {
                        $totalQuantity += $item['quantity'];
                        $processedItems[$itemKey] = true;
                    }
                }
                echo $totalQuantity; 
            ?></p>
            
            <!-- Show the total price instead of just the first item's price -->
            <p><strong>Price:</strong> Rs. <?php echo number_format($total, 2); ?></p>
            <p><strong>Email:</strong> <?php echo $orderDetails['email']; ?></p>
            <p><strong>Status:</strong> 
                <?php 
                if ($orderDetails['status'] == 'verified') {
                    echo '<span style="color: green;">✓ ' . $orderDetails['status'] . '</span>';
                } else {
                    echo $orderDetails['status'];
                }
                ?>
            </p>
            <p><strong>Order Date:</strong> <?php echo $orderDetails['order_date']; ?></p>
        </div>
        
        <div class="order-summary">
            <h2>Order Summary</h2>
            
            <?php
            // Create an array to track unique items
            $uniqueItems = [];
            
            // Group items by name and quantity
            while ($item = $orderItems->fetch_assoc()) {
                $key = $item['item_name'] . '_' . $item['quantity'];
                if (!isset($uniqueItems[$key])) {
                    $uniqueItems[$key] = [
                        'name' => $item['item_name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ];
                }
            }
            
            // Display unique items
            foreach ($uniqueItems as $item) {
                echo '<div class="item-row">';
                echo '<span>' . $item['name'] . '</span>';
                echo '<span>x' . $item['quantity'] . '</span>';
                echo '<span>Rs. ' . number_format($item['price'], 2) . '</span>';
                echo '</div>';
            }
            ?>
            
            <div class="total">
                <div class="item-row">
                    <span>Total:</span>
                    <span></span>
                    <span>Rs. <?php echo number_format($total, 2); ?></span>
                </div>
            </div>
            </div>
            <!-- Fix the duplicate closing div by removing one of them -->
            <div style="text-align: center; margin-top: 20px;">
                <a href="Takeout.php" class="btn" style="display: inline-block; margin-right: 10px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">Back to Takeout</a>
                <a href="Homepage.php" class="btn" style="display: inline-block; padding: 10px 20px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 4px;">Return to Home</a>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>