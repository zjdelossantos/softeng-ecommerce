<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "cuddlepaws"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_product'])) {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_category = $_POST['product_category'];
        $product_stocks = $_POST['product_stocks'];
        $product_price = $_POST['product_price'];

        $stmt = $conn->prepare("UPDATE products SET Product_Name = ?, Product_Category = ?, Product_Stocks = ?, Product_Price = ? WHERE Product_Id = ?");
        $stmt->bind_param("ssdii", $product_name, $product_category, $product_stocks, $product_price, $product_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update product.']);
        }

        $stmt->close();
        $conn->close();
        exit; // Exit after handling the update
    }

    if (isset($_POST['add_product'])) {
        $product_name = $_POST['product_name'];
        $product_category = $_POST['product_category'];
        $product_stocks = $_POST['product_stocks'];
        $product_price = $_POST['product_price'];

        $stmt = $conn->prepare("INSERT INTO products (Product_Name, Product_Category, Product_Stocks, Product_Price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $product_name, $product_category, $product_stocks, $product_price);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add product.']);
        }

        $stmt->close();
        $conn->close();
        exit; // Exit after handling the add
    }
}

// Function to fetch products from the database
function fetchProducts($conn) {
    $products = [];
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

// Fetch initial product list
$products = fetchProducts($conn);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Inventory</title>
    <link rel="icon" href="https://res.cloudinary.com/dakq2u8n0/image/upload/v1726737021/logocuddlepaws_pcj2re.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="inventory.css">
</head>

<body>
    <header>
        <nav>
            <div class="logo">
                <a href="dashboard.php">Admin</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="sidebar">
            <button class="sidebar-toggle">&#x21A4;</button>
            <div class="logo-container">
                <img src="https://res.cloudinary.com/dpxfbom0j/image/upload/v1728791049/Ellipse_9_llomyf.png" alt="Cuddle Paws Logo" class="paw-logo">
                <a href="#" class="logo-text">Cuddle <span>Paws</span></a>
            </div>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="orders.php">Orders</a></li>
            </ul>
        </div>

        <!-- Main content area -->
        <div class="inventory">
            <h1>Inventory</h1>
            <div class="products-header">
                <h2>PRODUCTS</h2>
                <button class="add-product-btn">+ Add Product</button>
            </div>

            <!-- Inventory Table -->
            <form id="inventory-form">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Stocks</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="product-rows">
                        <!-- Existing product rows will be dynamically displayed here -->
                        <?php foreach ($products as $product): ?>
                            <tr data-id="<?php echo $product['Product_Id']; ?>">
                                <td><?php echo htmlspecialchars($product['Product_Name']); ?></td>
                                <td><?php echo htmlspecialchars($product['Product_Category']); ?></td>
                                <td><?php echo htmlspecialchars($product['Product_Stocks']); ?></td>
                                <td><?php echo htmlspecialchars($product['Product_Price']); ?></td>
                                <td>
                                    <button type="button" class="edit-btn" data-id="<?php echo $product['Product_Id']; ?>">Edit</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <script src=inventory.js></script>
</body>

</html>
