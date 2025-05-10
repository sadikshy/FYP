// Function to update quantity via AJAX
function updateQuantity(index, quantity) {
    fetch('update_cart_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            index: index,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the item price display
            const priceElement = document.querySelector(`.quantity-input[data-index="${index}"]`).closest('.cart-item').querySelector('.item-price span');
            priceElement.textContent = 'Rs. ' + data.itemTotal.toFixed(2);
            
            // Update the cart total
            document.querySelector('.cart-total-amount').textContent = 'Rs. ' + data.cartTotal.toFixed(2);
            
            // Update cart count in header if it exists
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = data.cartCount;
            }
        }
    })
    .catch(error => {
        console.error('Error updating quantity:', error);
    });
}