<?php
// Start the session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "dineamaze_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with filters
$sql = "SELECT * FROM user WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $searchParam = "%" . $search . "%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "ss";
}

$sql .= " ORDER BY user_id DESC";

// Prepare and execute the query
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$users = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - DineAmaze Admin</title>
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
        }
        
        .filter-group {
            flex: 1;
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
        
        .user-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .user-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .user-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .view-btn {
            display: inline-block;
            padding: 5px 10px;
            background-color: #6a5acd;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            transition: background-color 0.3s;
        }
        
        .view-btn:hover {
            background-color: #5a49c0;
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
                    <h1>Manage Users</h1>
                </div>
                
                <div class="filter-form">
                    <h3>Search Users</h3>
                    <form method="GET" action="">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="search">Search by Name or Email</label>
                                <input type="text" id="search" name="search" class="filter-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Enter name or email">
                            </div>
                            
                            <div class="filter-buttons">
                                <button type="submit" class="filter-btn">Search</button>
                                <a href="users.php" class="reset-btn">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($users) > 0): ?>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(substr($user['user_id'], 0, 8) . '...'); ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                                    <td>
                                        <a href="user-details.php?id=<?php echo $user['user_id']; ?>" class="view-btn">View Details</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="no-data">No users found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
