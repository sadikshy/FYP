<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - DineAmaze</title>
    <link rel="stylesheet" href="css/Homepage.css">
    <link rel="stylesheet" href="css/Menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="menu-section">
        <h2>Our Menu</h2>

        <div class="menu-category">
            <h3>1. Traditional Nepali Meals and Platters</h3>
            <div class="dish-grid">
                <div class="dish-item">
                    <img src="Menu Photos/Veg Khana set.jpg" alt="Veg Khana Set">
                    <h4>Dal Bhat Tarkari - Rs. 250</h4>
                    <p>Ingredients: Rice, Lentils (Dal), Mixed Vegetables (Carrot, Potato, Cabbage), Ghee, Spices (Turmeric, Cumin, Coriander), Papad, Salad, Pickle</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/MOMO.jpg" alt="Momo">
                    <h4>Momo - Rs. 150</h4>
                    <p>Ingredients: Flour, Chicken (or Vegetable), Cabbage, Garlic, Ginger, Soy Sauce, Spices</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Sel Roti.jpg" alt="Sel Roti">
                    <h4>Sel Roti - Rs. 100</h4>
                    <p>Ingredients: Rice Flour, Sugar, Yogurt, Ghee, Cardamom</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Samaye Baji.jpg" alt="Newari Khaja Set">
                    <h4>Newari Khaja Set - Rs. 350</h4>
                    <p>Ingredients: Beaten Rice (Chiura), Buff Sukuti, Egg, Aalu Tama, Bhatmas Sadeko, Chhwela, Spinach, Pickles, Dried fish fry, Bara</p>
                </div>

                <div class="dish-item">
                    <img src="Menu Photos/Non-Veg Khana Set.jpg" alt="Non-Veg Khana Set">
                    <h4>Nepali Thali - Rs. 300</h4>
                    <p>Ingredients: Rice, Dal, Vegetable Curry, Chicken (or Mutton), Pickle, Salad, Raita, Papad</p>
                </div>
            </div>
        </div>

        <div class="menu-category">
            <h3>2. Street Food and Quick Bites</h3>
            <div class="dish-grid">
                <div class="dish-item">
                    <img src="Menu Photos/Pakoda.jpg" alt="Pakoda">
                    <h4>Pakodi - Rs. 80</h4>
                    <p>Ingredients: Chickpea Flour, Potatoes, Onion, Spinach, Cumin, Coriander, Turmeric</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Papadi Chaat.jpg" alt="Papadi Chaat">
                    <h4>Chaat - Rs. 100</h4>
                    <p>Ingredients: Fried Bread (Puri), Potatoes, Yogurt, Tamarind, Spices (Cumin, Chaat Masala, Salt)</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Pani Puri.jpg" alt="Pani Puri">
                    <h4>Pani Puri - Rs. 85</h4>
                    <p>Ingredients: Puffed Wheat (Puri), Tamarind Water, Potato, Chickpeas, Spices</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Samosa.jpg" alt="Samosa">
                    <h4>Samosa - Rs. 60</h4>
                    <p>Ingredients: Flour, Potatoes, Peas, Cumin, Turmeric, Coriander</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Pizza Roll.jpg" alt="Pizza Roll">
                    <h4>Pizza Roll - Rs. 150</h4>
                    <p>Ingredients: Pizza Dough, Cheese, Tomato Sauce, Chicken (or Veggies), Herbs</p>
                </div>
            </div>
        </div>

        <div class="menu-category">
            <h3>3. Pizza, Burgers, and Snacks</h3>
            <div class="dish-grid">
                <div class="dish-item">
                    <img src="Menu Photos/Margherita Pizza.png" alt="Margherita Pizza">
                    <h4>Margherita Pizza - Rs. 350</h4>
                    <p>Ingredients: Pizza Dough, Tomato Sauce, Mozzarella Cheese, Basil</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Chicken Burger.jpg" alt="Chicken Burger">
                    <h4>Chicken Burger - Rs. 200</h4>
                    <p>Ingredients: Burger Bun, Chicken Patty, Lettuce, Tomato, Mayonnaise, Ketchup</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Veg Burger.jpg" alt="Vegetarian Burger">
                    <h4>Vegetarian Burger - Rs. 180</h4>
                    <p>Ingredients: Burger Bun, Veggie Patty, Lettuce, Tomato, Cucumber, Ketchup</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Chicken nuggets.jpg" alt="Chicken Nuggets">
                    <h4>Chicken Nuggets - Rs. 150</h4>
                    <p>Ingredients: Chicken, Breadcrumbs, Flour, Eggs, Spices</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/French Fries.jpg" alt="French Fries">
                    <h4>French Fries - Rs. 75</h4>
                    <p>Ingredients: Potatoes, Salt, Oil</p>
                </div>
            </div>
        </div>

        <div class="menu-category">
            <h3>4. Cozy Bowls and Noodles Delights</h3>
            <div class="dish-grid">
                <div class="dish-item">
                    <img src="Menu Photos/Chicken Noodles.jpg" alt="Chicken Noodles">
                    <h4>Chicken Noodles - Rs. 220</h4>
                    <p>Ingredients: Noodles, Chicken, Vegetables (Carrot, Bell Pepper, Cabbage), Soy Sauce, Garlic, Ginger</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Veg Noodles.jpg" alt="Vegetable Noodles">
                    <h4>Vegetable Noodles - Rs. 200</h4>
                    <p>Ingredients: Noodles, Vegetables (Carrot, Bell Pepper, Cabbage), Soy Sauce, Garlic, Ginger</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Soy Sauce Noodles.jpg" alt="Soy Sauce Noodles">
                    <h4>Soy Sauce Noodles - Rs. 210</h4>
                    <p>Ingredients: Noodles, Soy Sauce, Garlic, Ginger, Green Onion</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Noodles Bowl.jpg" alt="Noodles Bowl">
                    <h4>Noodles Bowl - Rs. 150</h4>
                    <p>Ingredients: Noodles, Vegetables, Soy Sauce, Spices</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Pasta Bowl.jpg" alt="Pasta Bowl">
                    <h4>Pasta Bowl - Rs. 250</h4>
                    <p>Ingredients: Pasta, Tomato Sauce, Garlic, Basil, Parmesan Cheese</p>
                </div>
            </div>
        </div>

        <div class="menu-category">
            <h3>5. Desserts</h3>
            <div class="dish-grid">
                <div class="dish-item">
                    <img src="Menu Photos/Gulab Jamun.jpg" alt="Gulab Jamun">
                    <h4>Gulab Jamun - Rs. 80</h4>
                    <p>Ingredients: Milk Powder, Flour, Ghee, Sugar, Rose Water</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Cashew Ladoo.jpg" alt="Cashew Ladoo">
                    <h4>Cashew Ladoo - Rs. 120</h4>
                    <p>Ingredients: Cashews, Sugar, Ghee, Cardamom</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Tiramisu.jpg" alt="Tiramisu">
                    <h4>Tiramisu - Rs. 150</h4>
                    <p>Ingredients: Mascarpone Cheese, Coffee, Ladyfingers, Cocoa Powder, Sugar, Eggs</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Chocolate Cake.jpg" alt="Chocolate Cake">
                    <h4>Chocolate Cake - Rs. 180</h4>
                    <p>Ingredients: Flour, Cocoa Powder, Sugar, Eggs, Butter, Baking Powder</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Fruit Salad.jpg" alt="Fruit Salad">
                    <h4>Fruit Salad - Rs. 130</h4>
                    <p>Ingredients: Seasonal Fruits (Apple, Orange, Banana, Grapes), Honey, Mint</p>
                </div>
            </div>
        </div>

        <div class="menu-category">
            <h3>6. Beverages</h3>
            <div class="dish-grid">
                <div class="dish-item">
                    <img src="Menu Photos/Lassi.jpg" alt="Lassi">
                    <h4>Momo Soup - Rs. 70</h4>
                    <p>Ingredients: Yogurt, Milk, Sugar, Nuts</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Milkshake.jpg" alt="Milkshake">
                    <h4>Milkshake - Rs. 150</h4>
                    <p>Ingredients: Milk, Ice Cream, Sugar, Chocolate or Strawberry Syrup</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Fruit Juice.png" alt="Fresh Juice">
                    <h4>Fresh Juice - Rs. 120</h4>
                    <p>Ingredients: Fresh Fruit (Orange, Mango, Apple), Ice, Sugar</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Milk Tea.jpg" alt="Milk Tea">
                    <h4>Tea - Rs. 40</h4>
                    <p>Ingredients: Tea Leaves, Milk, Sugar</p>
                </div>
                <div class="dish-item">
                    <img src="Menu Photos/Coffee.jpg" alt="Coffee">
                    <h4>Coffee - Rs. 60</h4>
                    <p>Ingredients: Coffee Beans, Milk, Sugar</p>
                </div>
            </div>
        </div>
    </section>

    <!-- At the end of your content, before closing body tag -->
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Any scripts should go after the footer -->
</body>
</html>