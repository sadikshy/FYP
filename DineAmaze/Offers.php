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

// Mock data for offers
$offerCategories = [
    [
        'id' => 1,
        'name' => 'Student Offers',
        'description' => 'Special discounts for students with valid ID',
        'icon' => 'fas fa-graduation-cap',
        'offers' => [
            [
                'id' => 101,
                'title' => 'Student Lunch Special',
                'description' => '20% off on all lunch items from Monday to Friday',
                'discount' => '20%',
                'code' => 'STUDENT20',
                'valid_until' => '2024-12-31',
                'image' => 'images/offers/student-lunch.jpg'
            ],
            [
                'id' => 102,
                'title' => 'Weekend Study Group',
                'description' => 'Buy 3 meals, get 1 free when you come with your study group',
                'discount' => 'Buy 3 Get 1',
                'code' => 'STUDY4',
                'valid_until' => '2024-12-31',
                'image' => 'images/offers/study-group.jpg'
            ],
            [
                'id' => 103,
                'title' => 'Exam Week Boost',
                'description' => 'Free coffee with any meal purchase during exam weeks',
                'discount' => 'Free Coffee',
                'code' => 'EXAMBOOST',
                'valid_until' => '2024-12-31',
                'image' => 'images/offers/exam-boost.jpg'
            ]
        ]
    ],
    [
        'id' => 2,
        'name' => 'Day Offers',
        'description' => 'Special deals for each day of the week',
        'icon' => 'fas fa-calendar-day',
        'offers' => [
            [
                'id' => 201,
                'title' => 'Monday Momo Madness',
                'description' => '25% off on all momo varieties every Monday',
                'discount' => '25%',
                'code' => 'MOMOMONDAY',
                'valid_until' => '2024-12-31',
                'image' => 'images/offers/momo-monday.jpg'
            ],
            [
                'id' => 202,
                'title' => 'Taco Tuesday',
                'description' => 'Buy 1 Get 1 Free on all tacos every Tuesday',
                'discount' => 'BOGO',
                'code' => 'TACOTUES',
                'valid_until' => '2024-12-31',
                'image' => 'images/offers/taco-tuesday.jpg'
            ],
            [
                'id' => 203,
                'title' => 'Weekend Family Special',
                'description' => '15% off on family platters on Saturday and Sunday',
                'discount' => '15%',
                'code' => 'FAMILYWEEKEND',
                'valid_until' => '2024-12-31',
                'image' => 'images/offers/family-weekend.jpg'
            ]
        ]
    ],
    [
        'id' => 3,
        'name' => 'Seasonal Offers',
        'description' => 'Limited-time offers for special seasons and festivals',
        'icon' => 'fas fa-snowflake',
        'offers' => [
            [
                'id' => 301,
                'title' => 'Dashain Festival Special',
                'description' => '30% off on traditional Nepali thali during Dashain',
                'discount' => '30%',
                'code' => 'DASHAIN30',
                'valid_until' => '2024-10-31',
                'image' => 'images/offers/dashain-special.jpg'
            ],
            [
                'id' => 302,
                'title' => 'Summer Cooler',
                'description' => 'Buy any meal and get a free cold beverage during summer months',
                'discount' => 'Free Drink',
                'code' => 'SUMMERCOOL',
                'valid_until' => '2024-08-31',
                'image' => 'images/offers/summer-cooler.jpg'
            ],
            [
                'id' => 303,
                'title' => 'Winter Warmer',
                'description' => '20% off on all hot soups and beverages during winter',
                'discount' => '20%',
                'code' => 'WINTERWARM',
                'valid_until' => '2024-02-28',
                'image' => 'images/offers/winter-warmer.jpg'
            ]
        ]
    ],
    [
        'id' => 4,
        'name' => 'Loyalty Rewards',
        'description' => 'Special offers for our loyal customers',
        'icon' => 'fas fa-award',
        'offers' => [
            [
                'id' => 401,
                'title' => 'Birthday Special',
                'description' => 'Free dessert on your birthday with any meal purchase',
                'discount' => 'Free Dessert',
                'code' => 'BIRTHDAY',
                'valid_until' => '2024-12-31',
                'image' => 'images/offers/birthday-special.jpg'
            ],
            [
                'id' => 402,
                'title' => 'Anniversary Celebration',
                'description' => '25% off on your bill on your anniversary',
                'discount' => '25%',
                'code' => 'ANNIVERSARY25',
                'valid_until' => '2024-12-31',
                'image' => 'images/offers/anniversary.jpg'
            ],
            [
                'id' => 403,
                'title' => 'Frequent Diner',
                'description' => 'Every 10th meal is on us when you register with our loyalty program',
                'discount' => 'Free Meal',
                'code' => 'LOYAL10',
                'valid_until' => '2024-12-31',
                'image' => 'images/offers/frequent-diner.jpg'
            ]
        ]
    ]
];

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
    
    <section class="offer-section" id="current-offers">
        <div class="container">
            <div class="offer-header">
                <h2 class="offer-title">Our<span>Offers</span></h2>
                <p class="offer-description">
                    Discover our exclusive deals and promotions designed to enhance your dining experience at DineAmaze.
                    Don't miss out on these limited-time offers!
                </p>
            </div>
            
            <div class="offer-categories">
                <ul class="nav nav-tabs" id="offerTabs" role="tablist">
                    <?php foreach ($offerCategories as $index => $category): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($index === 0) ? 'active' : ''; ?>" 
                           id="tab-<?php echo $category['id']; ?>" 
                           data-toggle="tab" 
                           href="#category-<?php echo $category['id']; ?>" 
                           role="tab">
                            <i class="<?php echo $category['icon']; ?>"></i> <?php echo $category['name']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="tab-content" id="offerTabContent">
                <?php foreach ($offerCategories as $index => $category): ?>
                <div class="tab-pane fade <?php echo ($index === 0) ? 'show active' : ''; ?>" 
                     id="category-<?php echo $category['id']; ?>" 
                     role="tabpanel">
                    
                    <div class="category-description">
                        <h3><?php echo $category['name']; ?></h3>
                        <p><?php echo $category['description']; ?></p>
                    </div>
                    
                    <div class="offer-container">
                        <?php foreach ($category['offers'] as $offer): ?>
                        <div class="offer-card">
                            <div class="offer-badge"><?php echo $offer['discount']; ?></div>
                            <div class="offer-image">
                                <img src="<?php echo $offer['image']; ?>" alt="<?php echo $offer['title']; ?>" onerror="this.src='images/offers/default-offer.jpg'">
                            </div>
                            <div class="offer-content">
                                <h3><?php echo $offer['title']; ?></h3>
                                <p><?php echo $offer['description']; ?></p>
                                <p class="offer-validity">Valid until: <?php echo date('d M Y', strtotime($offer['valid_until'])); ?></p>
                                <p class="promo-code">Code: <strong><?php echo $offer['code']; ?></strong></p>
                                <a href="Menu.php" class="view-all-btn">Order Now</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <section class="how-to-use">
        <div class="container">
            <div class="offer-header">
                <h2 class="offer-title">How to<span>Redeem</span></h2>
                <p class="offer-description">
                    Follow these simple steps to enjoy our exclusive deals and make the most of your dining experience
                </p>
            </div>
            
            <div class="steps-container">
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4>Find an Offer</h4>
                    <p>Browse through our available offers and find one that suits you</p>
                </div>
                
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-copy"></i>
                    </div>
                    <h4>Copy the Code</h4>
                    <p>Note down the promo code for your selected offer</p>
                </div>
                
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h4>Order Your Meal</h4>
                    <p>Select your favorite items from our menu</p>
                </div>
                
                <div class="step">
                    <div class="step-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h4>Apply the Code</h4>
                    <p>Enter the promo code during checkout to get your discount</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="offers-faq">
        <div class="container">
            <div class="offer-header">
                <h2 class="offer-title">FAQ<span>About Offers</span></h2>
                <p class="offer-description">
                    Get answers to common questions about our special deals and promotions
                </p>
            </div>
            
            <div class="accordion" id="offersFAQ">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Can I combine multiple offers?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#offersFAQ">
                        <div class="card-body">
                            No, only one offer can be applied per order. Please choose the offer that provides the best value for your order.
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header" id="headingTwo">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Do I need to show my student ID for student offers?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#offersFAQ">
                        <div class="card-body">
                            Yes, a valid student ID must be presented when redeeming student offers, either in person for dine-in or takeout orders.
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header" id="headingThree">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                How do I redeem my birthday offer?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#offersFAQ">
                        <div class="card-body">
                            To redeem your birthday offer, you must be registered in our system with your birth date. The offer is valid 7 days before and after your birthday. Simply mention the offer when placing your order and show a valid ID.
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header" id="headingFour">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Are offers valid for both dine-in and takeout?
                            </button>
                        </h2>
                    </div>
                    <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#offersFAQ">
                        <div class="card-body">
                            Yes, most offers are valid for both dine-in and takeout orders unless specifically mentioned otherwise in the offer details.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
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