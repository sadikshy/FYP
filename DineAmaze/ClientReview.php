<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: Login.php");
    exit();
}

// Include database connection
include 'includes/db_connection.php';

// Ensure user_name is set in the session, fetch if necessary (optional but good practice)
if (!isset($_SESSION['user_name']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql_user = "SELECT name FROM user WHERE user_id = ?";
    $stmt_user = $conn->prepare($sql_user);
    if ($stmt_user) {
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        if ($row_user = $result_user->fetch_assoc()) {
            $_SESSION['user_name'] = $row_user['name'];
        }
        $stmt_user->close();
    }
}


// Query to get the latest review for each user that is not hidden
$sql = "SELECT r.*, u.name
        FROM review r
        JOIN user u ON r.user_id = u.user_id
        JOIN (
            SELECT user_id, MAX(review_id) as latest_review_id
            FROM review
            WHERE (isHidden IS NULL OR isHidden != 'Yes')
            GROUP BY user_id
        ) latest ON r.user_id = latest.user_id AND r.review_id = latest.latest_review_id
        WHERE (r.isHidden IS NULL OR r.isHidden != 'Yes')
        ORDER BY r.review_date DESC";
$result = $conn->query($sql);

// Check if the currently logged-in user has already submitted a review
$user_id = $_SESSION['user_id'];
$user_review_sql = "SELECT * FROM review WHERE user_id = ? ORDER BY review_id DESC LIMIT 1";
$user_review_stmt = $conn->prepare($user_review_sql);
$has_review = false;
$user_review = null;

if ($user_review_stmt) { // Check if prepare was successful
    $user_review_stmt->bind_param("i", $user_id);
    $user_review_stmt->execute();
    $user_review_result = $user_review_stmt->get_result();
    if ($user_review_result) { // Check if get_result was successful
        $has_review = $user_review_result->num_rows > 0;
        $user_review = $has_review ? $user_review_result->fetch_assoc() : null;
    }
    $user_review_stmt->close();
}

// Debug information - remove this in production
// echo "Has review: " . ($has_review ? "Yes" : "No");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineAmaze - Client Reviews</title>
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/ClientReview.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include './includes/header.php'; ?>

    <div class="review-banner">
        <div class="banner-overlay">
            <h1>Our Client Reviews</h1>
            <p>Discover what our valued customers have to say about their dining experiences</p>
        </div>
    </div>

    <section class="client-reviews-section">
        <div class="reviews-header">
            <h2 class="reviews-title">Our<span>Reviews</span></h2>
            <p class="reviews-description">
                We value the feedback from our customers and strive to provide the best dining experience
            </p>
        </div>

        <?php if(isset($_SESSION['review_success'])): ?>
            <div class="alert alert-success">
                <?php
                    echo $_SESSION['review_success'];
                    unset($_SESSION['review_success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['review_error'])): ?>
            <div class="alert alert-error">
                <?php
                    echo $_SESSION['review_error'];
                    unset($_SESSION['review_error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="reviews-container">
            <?php
            // Display reviews from the database
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Determine which star icons to display based on rating
                    $full_stars = floor($row['rating']);
                    $half_star = ($row['rating'] - $full_stars) >= 0.5;
                    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

                    // Format the date
                    $review_date = date('F j, Y', strtotime($row['review_date']));

                    // Get user profile image from database instead of using default for all
                    $profile_image = 'images/reviews/default-profile.jpg'; // Default fallback
                    
                    // Check if user has a profile image in the database
                    $user_profile_sql = "SELECT profile_image FROM user WHERE user_id = ?";
                    $user_profile_stmt = $conn->prepare($user_profile_sql);
                    if ($user_profile_stmt) {
                        $user_profile_stmt->bind_param("i", $row['user_id']);
                        $user_profile_stmt->execute();
                        $user_profile_result = $user_profile_stmt->get_result();
                        if ($user_profile_row = $user_profile_result->fetch_assoc()) {
                            if (!empty($user_profile_row['profile_image']) && file_exists($user_profile_row['profile_image'])) {
                                $profile_image = $user_profile_row['profile_image'];
                            }
                        }
                        $user_profile_stmt->close();
                    }
            ?>
                <div class="review-card">
                    <div class="review-profile">
                        <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <h3 class="reviewer-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                    </div>
                    <div class="review-content">
                        <div class="review-stars">
                            <?php
                            // Display full stars
                            for ($i = 0; $i < $full_stars; $i++) {
                                echo '<i class="fas fa-star"></i>';
                            }

                            // Display half star if needed
                            if ($half_star) {
                                echo '<i class="fas fa-star-half-alt"></i>';
                            }

                            // Display empty stars
                            for ($i = 0; $i < $empty_stars; $i++) {
                                echo '<i class="far fa-star"></i>';
                            }
                            ?>
                        </div>
                        <p class="review-text"><?php echo htmlspecialchars($row['review_text']); ?></p>
                        <p class="review-date"><?php echo $review_date; ?></p>
                    </div>
                </div>
            <?php
                }
            } else {
                echo '<div class="no-reviews">No reviews available yet. Be the first to share your experience!</div>';
            }
            ?>
        </div>

        <div class="submit-review-section">
            <h2><?php echo $has_review ? 'Update Your Review' : 'Share Your Experience'; ?></h2>
            <p>We'd love to hear about your dining experience at DineAmaze</p>

            <form class="review-form" action="submit_review.php" method="post">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Your Rating</label>
                    <div class="rating-select">
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" <?php echo ($has_review && $user_review['rating'] == 5) ? 'checked' : ''; ?> required>
                            <label for="star5" title="5 stars"><i class="fas fa-star"></i></label>

                            <input type="radio" id="star4" name="rating" value="4" <?php echo ($has_review && $user_review['rating'] == 4) ? 'checked' : ''; ?>>
                            <label for="star4" title="4 stars"><i class="fas fa-star"></i></label>

                            <input type="radio" id="star3" name="rating" value="3" <?php echo ($has_review && $user_review['rating'] == 3) ? 'checked' : ''; ?>>
                            <label for="star3" title="3 stars"><i class="fas fa-star"></i></label>

                            <input type="radio" id="star2" name="rating" value="2" <?php echo ($has_review && $user_review['rating'] == 2) ? 'checked' : ''; ?>>
                            <label for="star2" title="2 stars"><i class="fas fa-star"></i></label>

                            <input type="radio" id="star1" name="rating" value="1" <?php echo ($has_review && $user_review['rating'] == 1) ? 'checked' : ''; ?>>
                            <label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="review">Your Review</label>
                    <textarea id="review" name="review" rows="5" required placeholder="Tell us about your experience at DineAmaze..."><?php echo $has_review ? htmlspecialchars($user_review['review_text']) : ''; ?></textarea>
                </div>

                <button type="submit" class="submit-btn"><?php echo $has_review ? 'Update Review' : 'Submit Review'; ?></button>
            </form>
        </div>
    </section>

    <?php include './includes/footer.php'; ?>

    <script>
        // Star rating functionality
        const starRatingContainer = document.querySelector('.star-rating');
        const stars = starRatingContainer.querySelectorAll('input[type="radio"]');
        const labels = starRatingContainer.querySelectorAll('label');

        // Function to update star appearance based on selected rating or hover
        function updateStars(rating) {
            labels.forEach((label, index) => {
                if (5 - index <= rating) {
                    label.querySelector('i').classList.add('fas');
                    label.querySelector('i').classList.remove('far');
                } else {
                    label.querySelector('i').classList.add('far');
                    label.querySelector('i').classList.remove('fas');
                }
            });
        }

        // Initial state based on pre-selected radio button (for update)
        const initialRating = starRatingContainer.querySelector('input[type="radio"]:checked')?.value;
        if (initialRating) {
            updateStars(parseInt(initialRating));
        } else {
             // Default to empty stars if no rating is checked initially
             updateStars(0);
        }


        labels.forEach((label, index) => {
            label.addEventListener('mouseover', () => {
                // Highlight stars on hover
                updateStars(5 - index); // Highlight up to the star being hovered
            });

            label.addEventListener('mouseout', () => {
                // Revert to selected rating state on mouseout
                const selectedStar = starRatingContainer.querySelector('input[type="radio"]:checked');
                if (selectedStar) {
                    updateStars(parseInt(selectedStar.value));
                } else {
                    // Revert to empty if nothing is selected
                    updateStars(0);
                }
            });

            label.addEventListener('click', () => {
                // Update stars immediately on click
                 const selectedStar = starRatingContainer.querySelector('input[type="radio"]:checked');
                 if (selectedStar) {
                     updateStars(parseInt(selectedStar.value));
                 }
            });
        });

        // Ensure stars reflect the checked radio button when the page loads or when selected by keyboard
        starRatingContainer.addEventListener('change', () => {
             const selectedStar = starRatingContainer.querySelector('input[type="radio"]:checked');
             if (selectedStar) {
                 updateStars(parseInt(selectedStar.value));
             }
        });

    </script>
</body>
</html>
<?php
// Close the database connection
if ($conn) {
    $conn->close();
}
?>