<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customize Your Dish - DineAmaze</title>
    <link rel="stylesheet" href="Homepage.css">
    <link rel="stylesheet" href="Customization.css"> 
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="customize-dish">
        <h2>Customize Your Dish</h2>
        <div class="pizza-section">
            <img src="pizza-image.jpg" alt="Pizza" class="pizza-image"> 
            <div class="pizza-info">
                <label for="portion">Number of Portion:</label>
                <select id="portion">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>

                <label for="toppings">Extra Toppings:</label>
                <select id="toppings" multiple>
                    <option value="pepperoni">Pepperoni</option>
                    <option value="mushrooms">Mushrooms</option>
                    <option value="onions">Onions</option>
                    <option value="olives">Olives</option>
                </select>

                <label for="remove">Remove Ingredient:</label>
                <select id="remove" multiple>
                    <option value="cheese">Cheese</option>
                    <option value="sauce">Sauce</option>
                </select>
            </div>
        </div>
        <button class="confirm-button">Confirm Customization</button>
    </section>

    <!-- After your customize-dish section -->
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>