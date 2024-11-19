const productRows = document.getElementById('product-rows');
const sidebar = document.querySelector('.sidebar');
const toggleButton = document.querySelector('.sidebar-toggle');
const addProductBtn = document.querySelector('.add-product-btn');
const tableBody = document.querySelector('.inventory-table tbody');
let isEditing = false;

toggleButton.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');

    if (sidebar.classList.contains('collapsed')) {
        toggleButton.textContent = '↦';
    } else {
        toggleButton.textContent = '↤'; 
    }
});

const inputRow = document.createElement('tr');
inputRow.innerHTML = `
    <td><input type="text" name="product_name" placeholder="Product Name" required></td>
    <td><input type="text" name="product_category" placeholder="Category" required></td>
    <td><input type="number" name="product_stocks" placeholder="Stocks" required></td>
    <td><input type="number" name="product_price" placeholder="Price" required></td>
    <td>
        <button class="save-btn">Add</button>
        <button class="cancel-btn">Cancel</button>
    </td>
`;
tableBody.appendChild(inputRow);
inputRow.style.display = 'none'; 

addProductBtn.addEventListener('click', () => {
    if (!isEditing) {
        inputRow.style.display = 'table-row';
        inputRow.querySelector('input[name="product_name"]').focus();
        isEditing = true;
    } else {
        alert("Please save or cancel the current product before adding a new one.");
    }
});

const saveBtn = inputRow.querySelector('.save-btn');
saveBtn.addEventListener('click', function (event) {
    event.preventDefault();

    const inputs = inputRow.querySelectorAll('input');
    const data = {
        product_name: inputs[0].value,
        product_category: inputs[1].value,
        product_stocks: inputs[2].value,
        product_price: inputs[3].value,
        add_product: true
    };

    fetch('inventory.php', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${data.product_name}</td>
                <td>${data.product_category}</td>
                <td>${data.product_stocks}</td>
                <td>${data.product_price}</td>
                <td><button class="edit-btn" data-id="${result.product_id}">Edit</button></td>
            `;
            tableBody.appendChild(newRow);
            inputRow.style.display = 'none';
            isEditing = false; 
        } else {
            alert('Error adding product: ' + result.message);
        }
    })
    .catch(error => console.error('Error:', error));
});

const cancelBtn = inputRow.querySelector('.cancel-btn');
cancelBtn.addEventListener('click', function () {
    inputRow.style.display = 'none';
    isEditing = false;
});

document.addEventListener('click', function(event) {
    if (event.target.classList.contains('edit-btn')) {
        const row = event.target.closest('tr'); 
        const cells = row.querySelectorAll('td');

        const currentName = cells[0].innerText;
        const currentCategory = cells[1].innerText;
        const currentStocks = cells[2].innerText;
        const currentPrice = cells[3].innerText;

        const productId = event.target.getAttribute('data-id');

        row.innerHTML = `
            <td><input type="text" name="edit_product_name" value="${currentName}" required></td>
            <td><input type="text" name="edit_product_category" value="${currentCategory}" required></td>
            <td><input type="number" name="edit_product_stocks" value="${currentStocks}" required></td>
            <td><input type="number" name="edit_product_price" value="${currentPrice}" required></td>
            <td>
                <button class="save-btn">Save</button>
                <button class="cancel-btn">Cancel</button>
            </td>
        `;

        row.querySelector('.save-btn').addEventListener('click', function() {
            const updatedName = row.querySelector('input[name="edit_product_name"]').value;
            const updatedCategory = row.querySelector('input[name="edit_product_category"]').value;
            const updatedStocks = row.querySelector('input[name="edit_product_stocks"]').value;
            const updatedPrice = row.querySelector('input[name="edit_product_price"]').value;

            const data = {
                product_id: productId,
                product_name: updatedName,
                product_category: updatedCategory,
                product_stocks: updatedStocks,
                product_price: updatedPrice,
                update_product: true 
            };

            fetch('inventory.php', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    row.innerHTML = `
                        <td>${updatedName}</td>
                        <td>${updatedCategory}</td>
                        <td>${updatedStocks}</td>
                        <td>${updatedPrice}</td>
                        <td><button class="edit-btn" data-id="${productId}">Edit</button></td>
                    `;
                } else {
                    alert('Error updating product: ' + result.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        row.querySelector('.cancel-btn').addEventListener('click', function() {
            row.innerHTML = `
                <td>${currentName}</td>
                <td>${currentCategory}</td>
                <td>${currentStocks}</td>
                <td>${currentPrice}</td>
                <td><button class="edit-btn" data-id="${productId}">Edit</button></td>
            `;
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();
});

function fetchProducts() {
    fetch('fetch_products.php') 
        .then(response => response.json())
        .then(products => {
            productRows.innerHTML = ''; 
            products.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${product.Product_Name}</td>
                    <td>${product.Product_Category}</td>
                    <td>${product.Product_Stocks}</td>
                    <td>${product.Product_Price}</td>
                    <td>
                        <button type="button" class="edit-btn" data-id="${product.Product_Id}">Edit</button>
                    </td>
                `;
                productRows.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching products:', error));
}
