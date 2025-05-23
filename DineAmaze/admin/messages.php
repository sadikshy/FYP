<?php
// Start the session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Include the mail helper functions
require_once '../includes/mail_helper.php';

// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mark message as read
if (isset($_GET['action']) && $_GET['action'] == 'read' && isset($_GET['id'])) {
    $message_id = $_GET['id'];
    $read_sql = "UPDATE contact_message SET is_read = 'Yes' WHERE message_id = ?";
    $read_stmt = $conn->prepare($read_sql);
    $read_stmt->bind_param("i", $message_id);
    
    if ($read_stmt->execute()) {
        $_SESSION['success_message'] = "Message marked as read.";
    } else {
        $_SESSION['error_message'] = "Error updating message status.";
    }
    
    $read_stmt->close();
    header("Location: messages.php");
    exit;
}

// Send response to message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message_id']) && isset($_POST['admin_response'])) {
    $message_id = $_POST['message_id'];
    $admin_response = trim($_POST['admin_response']);
    $current_date = date('Y-m-d H:i:s');
    
    if (!empty($admin_response)) {
        // First, get the user's email and name and the original message
        $user_sql = "SELECT cm.message, u.email, u.name 
                    FROM contact_message cm
                    JOIN user u ON cm.user_id = u.user_id
                    WHERE cm.message_id = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("i", $message_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        
        if ($user_row = $user_result->fetch_assoc()) {
            $user_email = $user_row['email'];
            $user_name = $user_row['name'];
            $original_message = $user_row['message'];
            
            // Update the message in the database
            $response_sql = "UPDATE contact_message SET admin_response = ?, response_date = ?, is_read = 'Yes' WHERE message_id = ?";
            $response_stmt = $conn->prepare($response_sql);
            $response_stmt->bind_param("ssi", $admin_response, $current_date, $message_id);
            
            if ($response_stmt->execute()) {
                // Send email notification to the user
                $email_sent = sendContactResponseNotification($user_email, $user_name, $original_message, $admin_response);
                
                if ($email_sent) {
                    $_SESSION['success_message'] = "Response sent successfully and notification email sent to user.";
                } else {
                    $_SESSION['success_message'] = "Response saved successfully but there was an issue sending the notification email.";
                }
            } else {
                $_SESSION['error_message'] = "Error sending response.";
            }
            
            $response_stmt->close();
        } else {
            $_SESSION['error_message'] = "Could not find user information.";
        }
        
        $user_stmt->close();
    } else {
        $_SESSION['error_message'] = "Response cannot be empty.";
    }
    
    header("Location: messages.php");
    exit;
}

// Get filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$read_filter = isset($_GET['read_status']) ? $_GET['read_status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build query with filters
$sql = "SELECT cm.*, u.name, u.email, u.profile_image 
        FROM contact_message cm
        JOIN user u ON cm.user_id = u.user_id
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR message LIKE ?)";
    $searchParam = "%" . $search . "%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

if (!empty($read_filter)) {
    $sql .= " AND is_read = ?";
    $params[] = $read_filter;
    $types .= "s";
}

if (!empty($date_from)) {
    $sql .= " AND submission_date >= ?";
    $params[] = $date_from . " 00:00:00";
    $types .= "s";
}

if (!empty($date_to)) {
    $sql .= " AND submission_date <= ?";
    $params[] = $date_to . " 23:59:59";
    $types .= "s";
}

$sql .= " ORDER BY submission_date DESC";

// Prepare and execute the query
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$messages = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - DineAmaze Admin</title>
    <link rel="stylesheet" href="../css/admin/style.css">
    <link rel="stylesheet" href="../css/admin/sidebar.css">
    <link rel="stylesheet" href="../css/admin/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .filter-form {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .filter-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .filter-btn {
            padding: 10px 20px;
            background-color: #6a5acd;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .filter-btn:hover {
            background-color: #5a49c0;
        }
        
        .reset-btn {
            padding: 10px 20px;
            background-color: #f0f0f0;
            color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-left: 10px;
        }
        
        .reset-btn:hover {
            background-color: #e0e0e0;
        }
        
        .message-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
        }
        
        .user-profile {
            width: 80px;
            padding: 15px;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-right: 1px solid #eee;
        }
        
        .user-profile img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .message-wrapper {
            flex: 1;
        }
        
        .message-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        
        .message-sender {
            font-weight: 600;
            color: #333;
        }
        
        .message-date {
            color: #777;
            font-size: 14px;
        }
        
        .message-content {
            padding: 20px;
        }
        
        .message-text {
            margin-bottom: 20px;
            line-height: 1.6;
            color: #444;
        }
        
        .message-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .action-btn {
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        
        .action-btn i {
            margin-right: 8px;
        }
        
        .read-btn {
            background-color: #6a5acd;
            color: #fff;
        }
        
        .read-btn:hover {
            background-color: #5a49c0;
        }
        
        .reply-btn {
            background-color: #28a745;
            color: #fff;
        }
        
        .reply-btn:hover {
            background-color: #218838;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 10px;
        }
        
        .status-read {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-unread {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 60%;
            max-width: 600px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #333;
        }
        
        .response-form {
            margin-top: 20px;
        }
        
        .response-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-height: 150px;
            margin-bottom: 15px;
            font-family: inherit;
        }
        
        .response-form button {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .response-form button:hover {
            background-color: #218838;
        }
        
        .response-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .response-header {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        
        .response-text {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            line-height: 1.6;
        }
        
        .response-date {
            color: #777;
            font-size: 14px;
            text-align: right;
        }
        
        .no-messages {
            text-align: center;
            padding: 50px 0;
            color: #777;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="dashboard-content">
                <div class="content-header">
                    <h1>Contact Messages</h1>
                </div>
                
                <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                    ?>
                </div>
                <?php endif; ?>
                
                <div class="filter-form">
                    <h3>Filter Messages</h3>
                    <form method="GET" action="">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="search">Search</label>
                                <input type="text" id="search" name="search" class="filter-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, email or message">
                            </div>
                            
                            <div class="filter-group">
                                <label for="read_status">Read Status</label>
                                <select id="read_status" name="read_status" class="filter-control">
                                    <option value="">All Messages</option>
                                    <option value="No" <?php echo $read_filter == 'No' ? 'selected' : ''; ?>>Unread</option>
                                    <option value="Yes" <?php echo $read_filter == 'Yes' ? 'selected' : ''; ?>>Read</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="date_from">Date From</label>
                                <input type="date" id="date_from" name="date_from" class="filter-control" value="<?php echo htmlspecialchars($date_from); ?>">
                            </div>
                            
                            <div class="filter-group">
                                <label for="date_to">Date To</label>
                                <input type="date" id="date_to" name="date_to" class="filter-control" value="<?php echo htmlspecialchars($date_to); ?>">
                            </div>
                            
                            <div class="filter-buttons">
                                <button type="submit" class="filter-btn">Filter</button>
                                <a href="messages.php" class="reset-btn">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="messages-container">
                    <?php if (count($messages) > 0): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="message-card <?php echo $message['is_read'] == 'No' ? 'unread' : ''; ?>">
                                <div class="user-profile">
                                    <?php 
                                    $profile_image = !empty($message['profile_image']) && file_exists('../' . $message['profile_image']) 
                                        ? '../' . $message['profile_image'] 
                                        : '../images/reviews/default-profile.jpg';
                                    ?>
                                    <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="<?php echo htmlspecialchars($message['name']); ?>">
                                    <span class="user-id">#<?php echo $message['user_id']; ?></span>
                                </div>
                                <div class="message-wrapper">
                                <div class="message-header">
                                    <div>
                                        <span class="message-sender"><?php echo htmlspecialchars($message['name']); ?></span>
                                        <span class="message-email">(<?php echo htmlspecialchars($message['email']); ?>)</span>
                                        <span class="status-badge <?php echo $message['is_read'] == 'Yes' ? 'status-read' : 'status-unread'; ?>">
                                            <?php echo $message['is_read'] == 'Yes' ? 'Read' : 'Unread'; ?>
                                        </span>
                                    </div>
                                    <span class="message-date"><?php echo date('M d, Y H:i', strtotime($message['submission_date'])); ?></span>
                                </div>
                                <div class="message-content">
                                    <div class="message-text"><?php echo nl2br(htmlspecialchars($message['message'])); ?></div>
                                    
                                    <?php if (!empty($message['admin_response'])): ?>
                                    <div class="response-section">
                                        <div class="response-header">Your Response:</div>
                                        <div class="response-text"><?php echo nl2br(htmlspecialchars($message['admin_response'])); ?></div>
                                        <div class="response-date">Sent on: <?php echo date('M d, Y H:i', strtotime($message['response_date'])); ?></div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="message-actions">
                                        <?php if ($message['is_read'] == 'No'): ?>
                                        <a href="messages.php?action=read&id=<?php echo $message['message_id']; ?>" class="action-btn read-btn">
                                            <i class="fas fa-check"></i> Mark as Read
                                        </a>
                                        <?php endif; ?>
                                        
                                        <a href="#" class="action-btn reply-btn" onclick="openReplyModal(<?php echo $message['message_id']; ?>, '<?php echo addslashes(htmlspecialchars($message['name'])); ?>', '<?php echo addslashes(htmlspecialchars($message['message'])); ?>', '<?php echo !empty($message['admin_response']) ? addslashes(htmlspecialchars($message['admin_response'])) : ''; ?>')">
                                            <i class="fas fa-reply"></i> <?php echo !empty($message['admin_response']) ? 'Edit Response' : 'Reply'; ?>
                                        </a>
                                    </div>
                                </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-messages">
                            <i class="fas fa-inbox fa-3x" style="margin-bottom: 15px; color: #ddd;"></i>
                            <p>No messages found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reply Modal -->
    <div id="replyModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Reply to Message</h2>
            
            <div class="message-details">
                <p><strong>From:</strong> <span id="modal-sender"></span></p>
                <p><strong>Message:</strong></p>
                <div id="modal-message" style="background-color: #f9f9f9; padding: 10px; border-radius: 5px; margin-bottom: 20px;"></div>
            </div>
            
            <form class="response-form" method="POST" action="messages.php">
                <input type="hidden" id="message_id" name="message_id" value="">
                <label for="admin_response">Your Response:</label>
                <textarea id="admin_response" name="admin_response" required></textarea>
                <button type="submit">Send Response</button>
            </form>
        </div>
    </div>
    
    <script>
        // Modal functionality
        const modal = document.getElementById("replyModal");
        const closeBtn = document.getElementsByClassName("close")[0];
        
        function openReplyModal(id, name, message, response) {
            document.getElementById("message_id").value = id;
            document.getElementById("modal-sender").textContent = name;
            document.getElementById("modal-message").innerHTML = message.replace(/\n/g, '<br>');
            document.getElementById("admin_response").value = response;
            
            modal.style.display = "block";
            return false;
        }
        
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
<?php $conn->close(); ?>
</body>
</html>
