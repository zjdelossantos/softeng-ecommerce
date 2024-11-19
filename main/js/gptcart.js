
const parsePrice = (priceText) => {
    const parsedPrice = parseFloat(priceText.replace(/[^\d.]/g, ''));
    return isNaN(parsedPrice) ? 0 : parsedPrice;
};

// Function to update quantity
function updateQuantity(index, delta) {
    const quantityInput = document.querySelector(`.quantity-input[data-index="${index}"]`);
    let newQuantity = Math.max(1, parseInt(quantityInput.value, 10) + delta); // Ensure min value of 1
    quantityInput.value = newQuantity;
    updateItemQuantity(index, newQuantity);
}

// Function to update item quantity and recalculate total
function updateItemQuantity(index, quantity) {
    const row = document.querySelector(`.quantity-input[data-index="${index}"]`).closest('tr');
    const priceCell = row.querySelector('td:nth-child(2)'); // Price column
    const totalPriceCell = row.querySelector('td:nth-child(4)'); // Total price column

    const price = parsePrice(priceCell.textContent);
    const newTotalPrice = price * quantity;
    totalPriceCell.textContent = `₱ ${newTotalPrice.toFixed(2)}`; // Update item's total price

    // Recalculate the total price of the cart
    updateTotalPrice();
}

// Function to recalculate the cart's total price
function updateTotalPrice() {
    const checkboxes = document.querySelectorAll('input[name="cart-item-checkbox"]:checked');
    let total = 0;

    checkboxes.forEach(checkbox => {
        const row = checkbox.closest('tr'); // Get the parent row
        const totalPriceCell = row.querySelector('td:nth-child(4)'); // Select the total price cell
        const itemTotal = parsePrice(totalPriceCell.textContent); // Extract numeric value
        total += itemTotal;
    });

    // Update the total price element
    document.querySelector('.total-amount').textContent = `₱ ${total.toFixed(2)}`;
}

// Function to handle remove from cart
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('delete-btn')) {
        const productName = e.target.getAttribute('data-name');

        fetch('remove_from_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: productName }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`[data-name="${productName}"]`).closest('tr');
                row.remove();
                updateTotalPrice(); // Recalculate total after removing the row
            } else {
                alert('Failed to remove product from the cart.');
            }
        })
        .catch(error => console.error('Error:', error));
    }
});

// Event listener for cart table
document.querySelector('.cart-table').addEventListener('click', (event) => {
    if (event.target.classList.contains('increase-btn')) {
        updateQuantity(event.target.dataset.index, 1);
    } else if (event.target.classList.contains('decrease-btn')) {
        updateQuantity(event.target.dataset.index, -1);
    }
});

// Event listener for quantity input change
document.querySelector('.cart-table').addEventListener('input', (event) => {
    if (event.target.classList.contains('quantity-input')) {
        const index = event.target.dataset.index;
        const newQuantity = parseInt(event.target.value, 10);
        if (newQuantity >= 1) {
            updateItemQuantity(index, newQuantity);
        } else {
            event.target.value = 1; // Ensure minimum value of 1
        }
    }
});

// Initialize total price calculation after page load
document.addEventListener('DOMContentLoaded', () => {
    updateTotalPrice(); // Ensure the initial total is accurate
});

// Function to update the total price when cart item checkbox is changed
document.querySelector('.cart-table').addEventListener('change', (event) => {
    if (event.target.name === 'cart-item-checkbox') {
        updateTotalPrice();
    }
});

