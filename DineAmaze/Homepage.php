<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineAmaze - Exquisite Dining Experience</title>
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/reviews.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="hero">
        <div class="slider">
            <img src="images/Menu Photos/Sliderimage1.jpg" alt="Delicious Pasta Dish" class="active">
            <img src="images/Menu Photos/Sliderimage2.jpg" alt="Gourmet Burger with Fries">
            <img src="images/Menu Photos/Sliderimage3.jpg" alt="Fresh Salad with Grilled Chicken">
        </div>
        <div class="hero-content">
            <!-- Removed the title-ribbon div -->
            <h1 class="curved-text">DINEAMAZE</h1>
            <p>Flavors That Feel Like Home!</p>
            <a href="AboutUs.php" class="cta-button">VIEW DETAILS</a>
        </div>
    </section>

    <section class="about" id="about">
        <div class="about-container">
            <div class="about-text">
                <h3 class="welcome-text">Welcome!</h3>
                <h4 class="to-text">TO</h4>
                <h2 class="restaurant-name">DINEAMAZE</h2>
                
                <p class="about-description">
                    A wonderful serenity has taken possession of our kitchen, 
                    where we craft exquisite dishes with passion and 
                    creativity that delight the senses.
                </p>
            </div>
            <div class="about-images">
                <div class="image-container">
                    <img src="images/Menu Photos/Coffee.jpg" alt="Coffee Cup" class="about-img img1">
                    <img src="images/Menu Photos/DeliciousFood.jpg" alt="Indian Thali" class="about-img img2">
                </div>
            </div>
        </div>
    </section>

    <section class="menu-showcase" id="menu">
        <div class="menu-container">
            <div class="menu-image-grid">
                <div class="menu-overlay">
                    <h3 class="menu-subtitle">Our</h3>
                    <h2 class="menu-title">MENU</h2>
                </div>
            </div>
            <div class="menu-text">
                <h3 class="delicious-text">Delicious!</h3>
                <h2 class="dishes-text">DISHES</h2>
                
                <p class="menu-description">
                    A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot.
                </p>
                
                <a href="menu.php" class="view-menu-btn">VIEW FULL MENU</a>
            </div>
        </div>
    </section>

    <section class="gallery-section" id="gallery">
        <div class="gallery-header">
            <h2 class="gallery-title">Our<span>Gallery</span></h2>
            <p class="gallery-description">
                A feast for the eyes before the first bite.Explore our gallery and discover the artistry behind every dish. From sizzling street food to elegant platters, each photo captures the passion, color, and creativity that define the DineAmaze experience. Let your cravings begin here.
            </p>
        </div>
        
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="images/gallery/bread.jpg" alt="Freshly Baked Bread">
                <div class="gallery-overlay">
                    <div class="overlay-content">
                        <h3>Fuel Your Morning</h3>
                        <p>BREAKFAST</p>
                    </div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="images/gallery/GrilledChickenSalad.jpg" alt="Fresh Salad">
                <div class="gallery-overlay">
                    <div class="overlay-content">
                        <h3>Freshness in Every Bite</h3>
                        <p>SALAD</p>
                    </div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="images/gallery/SignaturePasta.png" alt="Spices">
                <div class="gallery-overlay">
                    <div class="overlay-content">
                        <h3>Infused with Flavor</h3>
                        <p>SIGNATURE PASTA</p>
                    </div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="images/gallery/Momo.jpg" alt="Hummus">
                <div class="gallery-overlay">
                    <div class="overlay-content">
                        <h3>All-Time Favourite</h3>
                        <p>LUNCH</p>
                    </div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="images/gallery/Noodles.jpg" alt="Fresh Fruits">
                <div class="gallery-overlay">
                    <div class="overlay-content">
                        <h3>Warm your soul </h3>
                        <p>COZY BOWLS</p>
                    </div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="images/gallery/Coffee.jpg" alt="Breakfast">
                <div class="gallery-overlay">
                    <div class="overlay-content">
                        <h3>Start your day with</h3>
                        <p>HOT DRINKS</p>
                    </div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="images/gallery/CheesePizza.jpg" alt="Pizza">
                <div class="gallery-overlay">
                    <div class="overlay-content">
                        <h3>Handcrafted to Perfection</h3>
                        <p>PIZZA</p>
                    </div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="images/gallery/ChocolateLavaCake.jpg" alt="Dessert">
                <div class="gallery-overlay">
                    <div class="overlay-content">
                        <h3>Sweet endings</h3>
                        <p>DESSERT</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="services-section" id="services">
        <div class="services-header">
            <h2 class="services-title">Our<span>Services</span></h2>
            <p class="services-description">
                At DineAmaze, you can customize your meals just the way you like with fresh, tasty ingredients. Enjoy great deals and special offers while savoring your favorites. Whether dining in or taking out, we prepare your order with care to make every meal easy and delicious.
            </p>
        </div>
        
        <div class="services-container">
            <div class="service-item">
                <div class="service-icon">
                    <img src="images/services-icons/Customize.png" alt="Customization">
                </div>
                <h3 class="service-title">Customization</h3>
                <p class="service-description">
                    Customize your meal your way — fresh ingredients, flavors you love, made just for you.
                </p>
            </div>
            
            <div class="service-item">
                <div class="service-icon">
                    <img src="images/services-icons/HotDeals.png" alt="Hot Deals">
                </div>
                <h3 class="service-title">Hot Deals</h3>
                <p class="service-description">
                    Discover sizzling hot deals that make every bite even more delicious!
                </p>
            </div>
            
            <div class="service-item">
                <div class="service-icon">
                    <img src="images/services-icons/Takeout.png" alt="Takeout">
                </div>
                <h3 class="service-title">Takeout</h3>
                <p class="service-description">
                    Hot, fresh, and ready when you are — takeout made simple and satisfying!
                </p>
            </div>
            
           
        </div>
    </section>
    <section class="offer-section" id="offers">
        <div class="offer-header">
            <h2 class="offer-title">Our<span>Offers</span></h2>
            <p class="offer-description">
                Discover our exclusive deals and promotions designed to enhance your dining experience at DineAmaze.
                Don't miss out on these limited-time offers!
            </p>
        </div>
        
        <div class="offer-container">
            <div class="offer-card">
                <div class="offer-badge">20% OFF</div>
                <div class="offer-image">
                    <img src="images/offers/family-meal.jpg" alt="Family Meal Deal">
                </div>
                <div class="offer-content">
                    <h3>Family Meal Deal</h3>
                    <p>Order any 4 main courses and get 20% off your total bill. Perfect for family gatherings!</p>
                    <p class="offer-validity">Valid until: Dec 31, 2024</p>
                
                </div>
            </div>
            
            <div class="offer-card">
                <div class="offer-badge">FREE</div>
                <div class="offer-image">
                    <img src="images/offers/free-dessert.jpg" alt="Free Dessert">
                </div>
                <div class="offer-content">
                    <h3>Free Dessert</h3>
                    <p>Spend over Rs. 1500 on your meal and receive a complimentary dessert of your choice.</p>
                    <p class="offer-validity">Valid until: Nov 30, 2024</p>
                    
                </div>
            </div>
            
            <div class="offer-card">
                <div class="offer-badge">HAPPY HOUR</div>
                <div class="offer-image">
                    <img src="images/offers/happy-hour.png" alt="Happy Hour Special">
                </div>
                <div class="offer-content">
                    <h3>Happy Hour Special</h3>
                    <p>Enjoy 15% off on all beverages between 4PM and 6PM, Monday and  Thursday.</p>
                    <p class="offer-validity">Ongoing</p>
                </div>
            </div>
        </div>
        
        <div class="view-all-offers">
            <a href="Offers.php" class="view-all-btn">View All Offers</a>
        </div>
    <!-- After the offer section and before the footer include -->
    <section class="reviews-section" id="reviews">
        <div class="reviews-header">
            <h2 class="reviews-title">Our<span>Reviews</span></h2>
            <p class="reviews-description">
                Discover what our valued customers have to say about their dining experiences at DineAmaze.
                We appreciate all feedback that helps us serve you better.
            </p>
        </div>
        
        <div class="reviews-slider-container">
            <div class="reviews-slider">
                <!-- Review 1 -->
                <div class="review-card">
                    <div class="review-profile">
                        <img src="images/reviews/review1.jpg" alt="Client Profile">
                    </div>
                    <div class="review-content">
                        <div class="review-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="reviewer-name">Samir Shrestha</h3>
                        <p class="review-text">I have been looking for authentic Nepali cuisine for a long time. The flavors at DineAmaze are incredible! The momos and thali sets are absolutely delicious. I am so glad to have found this place!</p>
                    </div>
                </div>
                
                <!-- Review 2 -->
                <div class="review-card">
                    <div class="review-profile">
                        <img src="images/reviews/review2.jpeg" alt="Client Profile">
                    </div>
                    <div class="review-content">
                        <div class="review-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <h3 class="reviewer-name">Sadikshya Munankarmi</h3>
                        <p class="review-text">The customization options are what make DineAmaze special. I was able to adjust the spice level to my preference, and the staff was incredibly accommodating. The food was fresh and flavorful!</p>
                    </div>
                </div>
                
                <!-- Review 3 -->
                <div class="review-card">
                    <div class="review-profile">
                        <img src="images/reviews/review3.jpeg" alt="Client Profile">
                    </div>
                    <div class="review-content">
                        <div class="review-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="reviewer-name">Nijal Shankar</h3>
                        <p class="review-text">The Sel Roti was absolutely perfect! Crispy on the outside, soft on the inside. The service was excellent and the ambiance was warm and inviting. Will definitely be coming back with friends!</p>
                    </div>
                </div>
            </div>
            
            <div class="slider-controls">
                <button class="prev-review"><i class="fas fa-chevron-left"></i></button>
                <button class="next-review"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
        
        <div class="view-all-reviews">
            <a href="ClientReview.php" class="view-all-btn">Review Us</a>
        </div>
    </section>
    
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Image slider functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slider img');
        
        function showSlide(n) {
            slides.forEach(slide => slide.classList.remove('active'));
            currentSlide = (n + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
        }
        
        function nextSlide() {
            showSlide(currentSlide + 1);
        }
        
        // Change slide every 5 seconds
        setInterval(nextSlide, 5000);
        
        // Reviews slider functionality
        document.addEventListener('DOMContentLoaded', function() {
            const reviewCards = document.querySelectorAll('.review-card');
            const prevButton = document.querySelector('.prev-review');
            const nextButton = document.querySelector('.next-review');
            
            // Show only the first review initially
            reviewCards.forEach((card, index) => {
                if (index === 0) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
            
            let currentReview = 0;
            const totalReviews = reviewCards.length;
            
            function showReview(index) {
                if (index < 0) index = totalReviews - 1;
                if (index >= totalReviews) index = 0;
                
                reviewCards.forEach((card, i) => {
                    if (i === index) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                currentReview = index;
            }
            
            prevButton.addEventListener('click', function() {
                showReview(currentReview - 1);
            });
            
            nextButton.addEventListener('click', function() {
                showReview(currentReview + 1);
            });
            
            // Auto change review every 7 seconds
            setInterval(function() {
                showReview(currentReview + 1);
            }, 7000);
        });
    </script>
</body>
</html>