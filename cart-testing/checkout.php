<?php
session_start();

// Retrieve the cart from the session, if it exists
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Initialize an array to store selected items
$selectedItems = [];

// Check if selected items are passed via POST
if (isset($_POST['selected-items'])) {
    $selectedItems = explode(',', $_POST['selected-items']);
}

// Initialize subtotal
$subtotal = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="icon" href="https://res.cloudinary.com/dakq2u8n0/image/upload/v1726737021/logocuddlepaws_pcj2re.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/checkout.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="index.php#about-us">About</a></li>
                <li><a href="login.php">Log In/Sign Up</a></li>
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
                            <?php
                            foreach ($selectedItems as $index) {
                                // Ensure $cart[$index] exists
                                if (isset($cart[$index])) {
                                    $item = $cart[$index];
                                    // Ensure 'price' and 'quantity' exist for each item
                                    if (isset($item['price']) && isset($item['quantity'])) {
                                        $totalPrice = floatval(preg_replace('/[^\d.]/', '', $item['price'])) * $item['quantity'];
                                        $subtotal += $totalPrice; // Add item total to subtotal
                            ?>
                                        <div class="purchase-item">
                                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="50">
                                            <div>
                                                <p> (Qty: <?= htmlspecialchars($item['quantity']) ?>)</p>
                                                <p>₱ <?= htmlspecialchars($item['price']) ?> each</p>
                                                <p>Total: ₱ <?= number_format($totalPrice, 2) ?></p>
                                            </div>
                                        </div>
                            <?php
                                    }
                                }
                            }
                            ?>
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

                <!-- Delivery Address Form -->
                <div class="delivery-address">
                    <h2>Delivery Address</h2>
                    <form action="checkout_delivery.php" method="POST">
                        <input type="text" id="full-name" name="full-name" placeholder="Full Name" required>
                        <input type="tel" id="phone" name="phone" placeholder="Phone Number" pattern="\+63 [0-9]{3} [0-9]{3} [0-9]{4}" required>
                        <input type="text" id="address" name="address" placeholder="Street, Phase/Block" required>
                        <input type="text" id="barangay" name="barangay" placeholder="Barangay" required>
                        <input type="text" id="municipality" name="municipality" placeholder="Municipality" required>
                        <button type="submit" class="place-order">Place Order</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
