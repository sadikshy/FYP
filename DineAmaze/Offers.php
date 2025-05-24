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

// Fetch offers from the database
$sql = "SELECT * FROM offers WHERE is_hidden = 0 ORDER BY offer_id ASC";
$result = $conn->query($sql);

// Check if the query was successful
$offers = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $offers[] = $row;
    }
}

// Group offers by section
$offerSections = [
    [
        'id' => 1,
        'name' => 'Current Offers',
        'description' => 'Discover our exclusive deals and promotions designed to enhance your dining experience at DineAmaze.',
        'icon' => 'fas fa-tags',
        'offers' => []
    ],
    [
        'id' => 2,
        'name' => 'Special Deals',
        'description' => 'Exclusive deals for our dine-in customers. These offers cannot be combined with other promotions.',
        'icon' => 'fas fa-percent',
        'offers' => []
    ],
    [
        'id' => 3,
        'name' => 'Celebration Offers',
        'description' => 'Make your special occasions even more memorable with our celebration offers.',
        'icon' => 'fas fa-gift',
        'offers' => []
    ]
];

// Distribute offers to their respective sections
foreach ($offers as $offer) {
    // Determine which section to put the offer in
    // Since the database has section as '0', we'll use a different approach
    // We'll distribute based on offer_id for now (1-3 in first section, 4-5 in second, 6-7 in third)
    $sectionIndex = 0; // Default to first section
    
    if ($offer['offer_id'] >= 4 && $offer['offer_id'] <= 5) {
        $sectionIndex = 1; // Second section
    } else if ($offer['offer_id'] >= 6) {
        $sectionIndex = 2; // Third section
    }
    
    // Add the offer to the appropriate section
    $offerSections[$sectionIndex]['offers'][] = [
        'id' => $offer['offer_id'],
        'title' => $offer['title'],
        'description' => $offer['description'],
        'badge' => $offer['badge'],
        'valid_until' => $offer['valid_until'],
        'is_ongoing' => $offer['is_ongoing'],
        'how_to_take' => $offer['how_to_take'],
        'image' => $offer['image']
    ];
}

// Create offers directory if it doesn't exist
$offersDir = 'images/offers/';
if (!file_exists($offersDir)) {
    mkdir($offersDir, 0777, true);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Offers - DineAmaze</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/offers.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="offers-hero">
        <div class="slider">
            <img src="images/offers/offers-hero.jpg" alt="Special Offers" class="active">
            <img src="images/offers/offers-slider2.jpg" alt="Food Discounts">
            <img src="images/offers/offers-slider3.jpg" alt="Meal Deals">
        </div>
        <div class="hero-content">
            <h1 class="curved-text">SPECIAL OFFERS</h1>
            <p>Exclusive Deals & Discounts</p>
            <a href="#current-offers" class="cta-button">VIEW OFFERS</a>
        </div>
    </section>
    
    <?php foreach ($offerSections as $section): ?>
    <section class="offer-section" id="<?php echo strtolower(str_replace(' ', '-', $section['name'])); ?>">
        <div class="container">
            <div class="offer-header">
                <h2 class="offer-title"><?php echo $section['name']; ?></h2>
                <p class="offer-description"><?php echo $section['description']; ?></p>
            </div>
            
            <div class="offer-container">
                <?php if (!empty($section['offers'])): ?>
                    <?php foreach ($section['offers'] as $offer): ?>
                        <div class="offer-card">
                            <div class="offer-badge"><?php echo htmlspecialchars($offer['badge']); ?></div>
                            <div class="offer-image">
                                <img src="<?php echo htmlspecialchars($offer['image']); ?>" alt="<?php echo htmlspecialchars($offer['title']); ?>">
                            </div>
                            <div class="offer-content">
                                <h3><?php echo htmlspecialchars($offer['title']); ?></h3>
                                <p><?php echo htmlspecialchars($offer['description']); ?></p>
                                <p class="offer-validity">
                                    <?php if ($offer['is_ongoing']): ?>
                                        Ongoing
                                    <?php elseif (!empty($offer['valid_until']) && $offer['valid_until'] != '0000-00-00'): ?>
                                        Valid until: <?php echo date('M d, Y', strtotime($offer['valid_until'])); ?>
                                    <?php endif; ?>
                                </p>
                                <div class="how-to-take">
                                    <h4>How to Take the Offer:</h4>
                                    <p><?php echo htmlspecialchars($offer['how_to_take']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-offers-message">
                        <p>No offers available in this category at this time. Please check back later!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Image slider functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sliderImages = document.querySelectorAll('.slider img');
            let currentImageIndex = 0;
            
            function showNextImage() {
                // Hide current image
                sliderImages[currentImageIndex].classList.remove('active');
                
                // Update index to next image
                currentImageIndex = (currentImageIndex + 1) % sliderImages.length;
                
                // Show next image
                sliderImages[currentImageIndex].classList.add('active');
            }
            
            // Change image every 5 seconds
            setInterval(showNextImage, 5000);
            
            // Add animation classes to hero content elements
            const heroContent = document.querySelector('.hero-content');
            if (heroContent) {
                setTimeout(() => {
                    const ctaButton = heroContent.querySelector('.cta-button');
                    if (ctaButton) {
                        ctaButton.style.opacity = '1';
                    }
                }, 600);
            }
        });
    </script>
</body>
</html>