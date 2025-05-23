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

// Handle review hiding/unhiding if requested
if (isset($_GET['action']) && isset($_GET['id'])) {
    $review_id = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'hide') {
        $sql = "UPDATE review SET isHidden = 'Yes' WHERE review_id = ?";
        $success_message = "Review hidden successfully.";
        $error_message = "Error hiding review.";
    } elseif ($action == 'unhide') {
        $sql = "UPDATE review SET isHidden = 'No' WHERE review_id = ?";
        $success_message = "Review unhidden successfully.";
        $error_message = "Error unhiding review.";
    } else {
        header("Location: reviews.php");
        exit;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $review_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = $success_message;
    } else {
        $_SESSION['error_message'] = $error_message;
    }
    
    $stmt->close();
    header("Location: reviews.php");
    exit;
}

// Get filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$rating_filter = isset($_GET['rating']) ? $_GET['rating'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build query with filters
$sql = "SELECT r.*, u.name, u.email 
        FROM review r
        JOIN user u ON r.user_id = u.user_id
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (u.name LIKE ? OR r.review_text LIKE ?)";
    $searchParam = "%" . $search . "%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "ss";
}

if (!empty($rating_filter)) {
    $sql .= " AND r.rating = ?";
    $params[] = $rating_filter;
    $types .= "i";
}

if (!empty($date_from)) {
    $sql .= " AND r.review_date >= ?";
    $params[] = $date_from;
    $types .= "s";
}

if (!empty($date_to)) {
    $sql .= " AND r.review_date <= ?";
    $params[] = $date_to;
    $types .= "s";
}

$sql .= " ORDER BY r.review_date DESC";

// Prepare and execute the query
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$reviews = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - DineAmaze Admin</title>
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
        
        .star-rating {
            color: #ffc107;
            font-size: 16px;
        }
        
        .review-text {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .action-btn {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            transition: background-color 0.3s;
            margin-right: 5px;
        }
        
        .view-btn {
            background-color: #6a5acd;
            color: #fff;
        }
        
        .view-btn:hover {
            background-color: #5a49c0;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: #fff;
        }
        
        .delete-btn:hover {
            background-color: #c82333;
        }
        
        .unhide-btn {
            background-color: #28a745;
            color: #fff;
        }
        
        .unhide-btn:hover {
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
        
        .review-detail {
            margin-bottom: 15px;
        }
        
        .review-detail label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }
        
        .review-detail-text {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #eee;
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
                    <h1>Manage Reviews</h1>
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
                    <h3>Search Reviews</h3>
                    <form method="GET" action="">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="search">Search by Name or Review Text</label>
                                <input type="text" id="search" name="search" class="filter-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Enter name or review text">
                            </div>
                            
                            <div class="filter-group">
                                <label for="rating">Filter by Rating</label>
                                <select id="rating" name="rating" class="filter-control">
                                    <option value="">All Ratings</option>
                                    <option value="5" <?php echo $rating_filter == '5' ? 'selected' : ''; ?>>5 Stars</option>
                                    <option value="4" <?php echo $rating_filter == '4' ? 'selected' : ''; ?>>4 Stars</option>
                                    <option value="3" <?php echo $rating_filter == '3' ? 'selected' : ''; ?>>3 Stars</option>
                                    <option value="2" <?php echo $rating_filter == '2' ? 'selected' : ''; ?>>2 Stars</option>
                                    <option value="1" <?php echo $rating_filter == '1' ? 'selected' : ''; ?>>1 Star</option>
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
                                <button type="submit" class="filter-btn">Search</button>
                                <a href="reviews.php" class="reset-btn">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($reviews) > 0): ?>
                                <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($review['review_id']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($review['name']); ?><br>
                                        <small><?php echo htmlspecialchars($review['email']); ?></small>
                                    </td>
                                    <td class="star-rating">
                                        <?php 
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $review['rating']) {
                                                echo '<i class="fas fa-star"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($review['review_date'])); ?></td>
                                    <td>
                                        <?php 
                                        $status = isset($review['isHidden']) && $review['isHidden'] == 'Yes' ? 'Hidden' : 'Visible';
                                        $statusClass = $status == 'Hidden' ? 'text-danger' : 'text-success';
                                        echo "<span class='$statusClass'>$status</span>";
                                        ?>
                                    </td>
                                    <td>
                                        <a href="#" class="action-btn view-btn" onclick="viewReview(<?php echo $review['review_id']; ?>, '<?php echo addslashes(htmlspecialchars($review['name'])); ?>', <?php echo $review['rating']; ?>, '<?php echo addslashes(htmlspecialchars($review['review_text'])); ?>', '<?php echo date('M d, Y', strtotime($review['review_date'])); ?>')">View</a>
                                        <?php if ($status == 'Hidden'): ?>
                                            <a href="reviews.php?action=unhide&id=<?php echo $review['review_id']; ?>" class="action-btn view-btn" style="background-color: #28a745;" onclick="return confirm('Are you sure you want to unhide this review?')">Unhide</a>
                                        <?php else: ?>
                                            <a href="reviews.php?action=hide&id=<?php echo $review['review_id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to hide this review?')">Hide</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="no-data">No reviews found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Review Detail Modal -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Review Details</h2>
            
            <div class="review-detail">
                <label>User:</label>
                <div id="modal-user" class="review-detail-text"></div>
            </div>
            
            <div class="review-detail">
                <label>Rating:</label>
                <div id="modal-rating" class="star-rating review-detail-text"></div>
            </div>
            
            <div class="review-detail">
                <label>Review Text:</label>
                <div id="modal-review-text" class="review-detail-text"></div>
            </div>
            
            <div class="review-detail">
                <label>Date:</label>
                <div id="modal-date" class="review-detail-text"></div>
            </div>
        </div>
    </div>
    
    <script>
        // Modal functionality
        const modal = document.getElementById("reviewModal");
        const closeBtn = document.getElementsByClassName("close")[0];
        
        function viewReview(id, name, rating, reviewText, date) {
            document.getElementById("modal-user").textContent = name;
            
            // Set rating stars
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    starsHtml += '<i class="fas fa-star"></i>';
                } else {
                    starsHtml += '<i class="far fa-star"></i>';
                }
            }
            document.getElementById("modal-rating").innerHTML = starsHtml;
            
            document.getElementById("modal-review-text").textContent = reviewText;
            document.getElementById("modal-date").textContent = date;
            
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

</body>
</html>
