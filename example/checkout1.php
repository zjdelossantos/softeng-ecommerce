<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$selectedItems = isset($_POST['selected-items']) ? explode(',', $_POST['selected-items']) : [];
$subtotal = 0;
$user = isset($_SESSION['user_data']) ? $_SESSION['user_data'] : []; // Fetch user data from session

if (!empty($selectedItems)) {
    foreach ($selectedItems as $index) {
        if (isset($cart[$index])) {
            $item = $cart[$index];
            if (isset($item['Product_price']) && isset($item['Product_Quantity'])) {
                $totalPrice = floatval(preg_replace('/[^\d.]/', '', $item['Product_price'])) * $item['Product_Quantity'];
                $subtotal += $totalPrice;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="icon" href="https://res.cloudinary.com/dakq2u8n0/image/upload/v1726737021/logocuddlepaws_pcj2re.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="index.php#about-us">About</a></li>
                <?php if (isset($_SESSION['Username'])): ?>
                    <li><a href="account.php">Account</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                <?php else: ?>
                    <li><a href="login.php">Log In/Sign Up</a></li>
                <?php endif; ?>
            </ul>
            <div class="logo">
                <img src="https://res.cloudinary.com/dakq2u8n0/image/upload/v1726737021/logocuddlepaws_pcj2re.png" alt="Hero Image">
                <a href="index.php">Cuddle Paws</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="checkout">
            <h1>Check Out</h1>
            <div class="checkout-container">
                <div class="purchase-payment">
                    <div class="purchase-details">
                        <h2>Your Purchase</h2>
                        <?php if (!empty($selectedItems)): ?>
                            <?php foreach ($selectedItems as $index): ?>
                                <?php if (isset($cart[$index])): ?>
                                    <?php $item = $cart[$index]; ?>
                                    <?php if (isset($item['Product_price']) && isset($item['Product_Quantity'])): ?>
                                        <?php
                                        $totalPrice = floatval(preg_replace('/[^\d.]/', '', $item['Product_price'])) * $item['Product_Quantity'];
                                        $subtotal += $totalPrice;
                                        ?>
                                        <div class="purchase-item">
                                            <img src="<?= htmlspecialchars($item['Product_ImageUrl']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="50">
                                            <div>
                                                <p> (Qty: <?= htmlspecialchars($item['Product_Quantity']) ?>)</p>
                                                <p>₱ <?= htmlspecialchars($item['Product_price']) ?> each</p>
                                                <p>Total: ₱ <?= number_format($totalPrice, 2) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No items selected for checkout.</p>
                        <?php endif; ?>
                    </div>

                    <div class="payment-details">
                        <h2>Payment Details</h2>
                        <div class="payment-item">
                            <span>Subtotal</span>
                            <span>₱ <?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="shipping-payment">
                            <span>Shipping</span>
                            <span>₱ 20.00</span>
                        </div>
                        <div class="payment-item total">
                            <span>Total</span>
                            <span>₱ <?= number_format($subtotal + 20, 2) ?></span> <!-- Add shipping to the total -->
                        </div>
                    </div>
                </div>

                <div class="delivery-address">
                    <h2>Delivery Address</h2>
                    <p><strong>First Name:</strong> <?= isset($user['Firstname']) ? htmlspecialchars($user['Firstname']) : 'Not Provided' ?></p>
                    <p><strong>Last Name:</strong> <?= isset($user['Lastname']) ? htmlspecialchars($user['Lastname']) : 'Not Provided' ?></p>
                    <p><strong>Email:</strong> <?= isset($user['Email']) ? htmlspecialchars($user['Email']) : 'Not Provided' ?></p>
                    <p><strong>Phone Number:</strong> <?= isset($user['Phone_num']) ? htmlspecialchars($user['Phone_num']) : 'N/A' ?></p>
                    <p><strong>Municipality:</strong> <?= isset($user['Municipality']) ? htmlspecialchars($user['Municipality']) : 'Not Provided' ?></p>
                    <p><strong>Barangay:</strong> <?= isset($user['Barangay']) ? htmlspecialchars($user['Barangay']) : 'Not Provided' ?></p>
                    <p><strong>Phase/Block:</strong> <?= isset($user['Phase']) ? htmlspecialchars($user['Phase']) : 'N/A' ?></p>
                    
                </div>
            </div>
        </section>
    </main>
</body>
</html>
