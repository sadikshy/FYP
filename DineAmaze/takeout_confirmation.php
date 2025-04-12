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

// Get order ID and status from URL
$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : 'pending';

// Initialize variables to prevent null access errors
$orderDetails = [
    'order_id' => $orderId,
    'order_date' => date('d M Y H:i'),
    'name' => '',
    'email' => '',
    'phone_number' => '',
    'order_group_id' => ''
];

// Get customer information
if ($orderId > 0) {
    $sql = "SELECT * FROM takeout_customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        $orderDetails['name'] = $customer['full_name'];
        $orderDetails['email'] = $customer['email'];
        $orderDetails['phone_number'] = $customer['contact_number'];
        $orderDetails['order_date'] = date('d M Y H:i', strtotime($customer['order_date']));
        $orderDetails['order_group_id'] = $customer['order_group_id'];
    }
    $stmt->close();
    
    // If we have an order_group_id, get all items for this order
    if (!empty($orderDetails['order_group_id'])) {
        $sql = "SELECT item_name, quantity, price 
                FROM takeout_order_items 
                WHERE order_group_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $orderDetails['order_group_id']);
        $stmt->execute();
        $items_result = $stmt->get_result();
        $orderItems = [];
        while ($item = $items_result->fetch_assoc()) {
            $orderItems[] = $item;
        }
        $stmt->close();
    } else {
        // Fallback to get items by order_id if no order_group_id
        $sql = "SELECT item_name, quantity, price 
                FROM takeout_order_items 
                WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $items_result = $stmt->get_result();
        $orderItems = [];
        while ($item = $items_result->fetch_assoc()) {
            $orderItems[] = $item;
        }
        $stmt->close();
    }
} else {
    // No valid order ID, set empty order items
    $orderItems = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - DineAmaze</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/confirmation.css">
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .order-header {
            text-align: center;
            margin-bottom: 30px;
            color: #4CAF50;
        }
        
        .order-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-row {
            display: flex;
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #555;
        }
        
        .info-value {
            flex: 1;
        }
        
        .order-items {
            margin-bottom: 30px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .items-table th {
            background-color: #f5f5f5;
            padding: 12px;
            text-align: left;
        }
        
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .total-row td {
            font-weight: bold;
            border-top: 2px solid #ddd;
        }
        
        .verification-process {
            background-color: #f9f9f9;
            padding: 20px;
            border-left: 4px solid #4CAF50;
            margin-bottom: 30px;
        }
        
        .verification-process h3 {
            color: #4CAF50;
            margin-top: 0;
        }
        
        .verification-process ol {
            padding-left: 20px;
        }
        
        .verification-process li {
            margin-bottom: 10px;
        }
        
        .back-button {
            display: inline-block;
            background-color: #6c5ce7;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        .back-button:hover {
            background-color: #5649c0;
        }
        
        .pending-badge {
            display: inline-block;
            background-color: #ffc107;
            color: #333;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="confirmation-container">
        <div class="order-header">
            <h2>Order Information <span class="pending-badge"><?php echo ucfirst($status); ?></span></h2>
        </div>
        
        <div class="order-info">
            <div>
                <div class="info-row">
                    <div class="info-label">Order ID:</div>
                    <div class="info-value">#<?php echo $orderDetails['order_id']; ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Date:</div>
                    <div class="info-value"><?php echo $orderDetails['order_date']; ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Name:</div>
                    <div class="info-value"><?php echo htmlspecialchars($orderDetails['name']); ?></div>
                </div>
            </div>
            
            <div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value"><?php echo htmlspecialchars($orderDetails['email']); ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Phone:</div>
                    <div class="info-value"><?php echo htmlspecialchars($orderDetails['phone_number']); ?></div>
                </div>
            </div>
        </div>
        
        <div class="order-items">
            <h3>Order Items</h3>
            
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($orderItems as $item): 
                        $subtotal = $item['quantity'] * $item['price'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                        <td>Rs. <?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <tr class="total-row">
                        <td colspan="3">Total:</td>
                        <td>Rs. <?php echo number_format($total, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="verification-process">
            <h3>Verification Process</h3>
            <p>Your order has been submitted and is currently pending verification by our staff. Here's what happens next:</p>
            <ol>
                <li>Our staff will review your ID document to verify your age.</li>
                <li>Once verified, your order status will be updated to "Verified".</li>
                <li>You will receive an email notification when your order is verified.</li>
                <li>You can then proceed to pick up your order at our location.</li>
            </ol>
            <p>This process typically takes 15-30 minutes during business hours. Thank you for your patience!</p>
        </div>
        
        <div style="text-align: center;">
            <a href="Homepage.php" class="back-button">Back to Home</a>
            
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>