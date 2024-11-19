
function updateCounter(row) {
    const quantityElement = row.querySelector('.number');
    const decreaseButton = row.querySelector('.btn#decrease');
    const count = parseInt(quantityElement.textContent);

    quantityElement.textContent = count;

    if (count <= 1) {
        decreaseButton.disabled = true;
        decreaseButton.classList.add('disabled');
    } else {
        decreaseButton.disabled = false;
        decreaseButton.classList.remove('disabled');
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const totalAmountElement = document.querySelector('.total-amount');
    const rows = document.querySelectorAll('.cart-table tbody tr');

    function calculateRowTotal(row) {
        const priceCell = row.querySelector('td:nth-child(2)'); 
        const quantityCell = row.querySelector('.number'); 
        const totalPriceCell = row.querySelector('td:nth-child(4)'); 

        let priceText = priceCell.textContent.replace('P', '').trim().replace(/,/g, '');
        const price = parseFloat(priceText);

        const quantity = parseInt(quantityCell.textContent);

        const totalRowPrice = price * quantity;

        totalPriceCell.textContent = `P ${totalRowPrice.toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    function calculateTotal() {
        let total = 0;

        rows.forEach(row => {
            calculateRowTotal(row); 

            const totalPriceCell = row.querySelector('td:nth-child(4)');
            let totalPriceText = totalPriceCell.textContent.replace('P', '').trim().replace(/,/g, '');
            const rowTotal = parseFloat(totalPriceText);

            total += rowTotal;
        });

        totalAmountElement.textContent = `P ${total.toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    rows.forEach(row => {
        const decreaseBtn = row.querySelector('.btn#decrease');
        const increaseBtn = row.querySelector('.btn#increase');
        const quantityElement = row.querySelector('.number');

        updateCounter(row);

        decreaseBtn.addEventListener('click', function() {
            let currentQuantity = parseInt(quantityElement.textContent);
            if (currentQuantity > 1) {
                quantityElement.textContent = currentQuantity - 1;
                updateCounter(row);
                calculateTotal(); 
            }
        });

        increaseBtn.addEventListener('click', function() {
            let currentQuantity = parseInt(quantityElement.textContent);
            quantityElement.textContent = currentQuantity + 1;
            updateCounter(row);
            calculateTotal(); 
        });
    });

    calculateTotal();
});



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
                const row = e.target.closest('tr');
                row.remove();
                updateTotalPrice(); // Recalculate total after removing the row
            } else {
                alert('Failed to remove product from the cart.');
            }
        })
        .catch(error => console.error('Error:', error));
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

// Event listener for cart item checkboxes
document.querySelector('.cart-table').addEventListener('change', (event) => {
    if (event.target.name === 'cart-item-checkbox') {
        updateTotalPrice();
    }
});

// Initialize total price calculation after page load
document.addEventListener('DOMContentLoaded', () => {
    updateTotalPrice(); // Ensure the initial total is accurate
});

// Function to handle the increase and decrease of quantity
document.querySelector('.cart-table').addEventListener('click', (event) => {
    if (event.target.classList.contains('increase-btn')) {
        const index = event.target.dataset.index;
        updateQuantity(index, 1);
    } else if (event.target.classList.contains('decrease-btn')) {
        const index = event.target.dataset.index;
        updateQuantity(index, -1);
    }
});

// Function to update the total price when cart item checkbox is changed
document.querySelector('.cart-table').addEventListener('change', (event) => {
    if (event.target.name === 'cart-item-checkbox') {
        updateTotalPrice();
    }
});
