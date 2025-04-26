document.addEventListener('DOMContentLoaded', function() {
    // Category filtering
    const filterItems = document.querySelectorAll('.filter-item');
    const menuCategories = document.querySelectorAll('.menu-category');
    
    filterItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all filter items
            filterItems.forEach(filter => filter.classList.remove('active'));
            
            // Add active class to clicked filter
            this.classList.add('active');
            
            const filterValue = this.getAttribute('data-filter');
            
            // Show/hide menu categories based on filter
            if (filterValue === 'all') {
                menuCategories.forEach(category => {
                    category.style.display = 'block';
                });
            } else {
                menuCategories.forEach(category => {
                    if (category.getAttribute('data-category') === filterValue) {
                        category.style.display = 'block';
                    } else {
                        category.style.display = 'none';
                    }
                });
            }
            
            // Apply current dietary filter
            applyDietaryFilter();
            // Apply current search filter
            applySearchFilter();
        });
    });
    
    // Dietary filtering
    const dietButtons = document.querySelectorAll('.diet-btn');
    
    dietButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all diet buttons
            dietButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            applyDietaryFilter();
        });
    });
    
    function applyDietaryFilter() {
        const activeDiet = document.querySelector('.diet-btn.active').getAttribute('data-diet');
        const dishItems = document.querySelectorAll('.dish-item');
        
        dishItems.forEach(item => {
            if (activeDiet === 'all' || item.getAttribute('data-diet') === activeDiet) {
                item.classList.remove('diet-filtered-out');
            } else {
                item.classList.add('diet-filtered-out');
            }
        });
        
        // Check if any category is empty after filtering
        updateEmptyCategories();
    }
    
    // Search functionality
    const searchInput = document.getElementById('dish-search');
    const searchButton = document.getElementById('search-button');
    
    // Search when button is clicked
    searchButton.addEventListener('click', function() {
        applySearchFilter();
    });
    
    // Search when Enter key is pressed
    searchInput.addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            applySearchFilter();
        }
    });
    
    function applySearchFilter() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const dishItems = document.querySelectorAll('.dish-item');
        
        dishItems.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            const ingredients = item.getAttribute('data-ingredients').toLowerCase();
            
            if (searchTerm === '' || name.includes(searchTerm) || ingredients.includes(searchTerm)) {
                item.classList.remove('search-filtered-out');
            } else {
                item.classList.add('search-filtered-out');
            }
        });
        
        // Check if any category is empty after filtering
        updateEmptyCategories();
    }
    
    function updateEmptyCategories() {
        document.querySelectorAll('.menu-category').forEach(category => {
            if (category.style.display !== 'none') {
                const items = category.querySelectorAll('.dish-item');
                let hasVisibleItems = false;
                
                items.forEach(item => {
                    if (!item.classList.contains('diet-filtered-out') && 
                        !item.classList.contains('search-filtered-out')) {
                        hasVisibleItems = true;
                    }
                });
                
                if (hasVisibleItems) {
                    category.classList.remove('empty-after-filter');
                } else {
                    category.classList.add('empty-after-filter');
                }
            }
        });
    }
    
    // Add to cart functionality for direct add buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart-direct');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const itemName = this.getAttribute('data-name');
            const itemPrice = parseFloat(this.getAttribute('data-price'));
            const itemImage = this.getAttribute('data-image');
            
            // Create cart item
            const cartItem = {
                id: itemId,
                name: itemName,
                image: itemImage,
                quantity: 1,
                price: itemPrice,
                toppings: [],
                removed_ingredients: [],
                special_instructions: ''
            };
            
            // Send AJAX request to add item to cart
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(cartItem)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count in header
                    const cartCount = document.querySelector('.cart-count');
                    cartCount.textContent = data.cartCount;
                    
                    // Show success message
                    alert(data.message);
                } else {
                    alert('Error adding item to cart: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the item to cart.');
            });
        });
    });
    
    // Add diet indicators to dish items
    document.querySelectorAll('.dish-item').forEach(item => {
        const dietType = item.getAttribute('data-diet');
        const itemTitle = item.querySelector('h4');
        
        const indicator = document.createElement('span');
        indicator.className = `diet-indicator ${dietType}-indicator`;
        indicator.title = dietType.charAt(0).toUpperCase() + dietType.slice(1);
        
        itemTitle.insertBefore(indicator, itemTitle.firstChild);
    });
    
    // Initialize with all filters applied
    applyDietaryFilter();
    applySearchFilter();
});