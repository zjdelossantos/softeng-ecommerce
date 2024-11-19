<?php
session_start();

if (isset($_POST['product_name']) && isset($_SESSION['cart'])) {
    $product_name = $_POST['product_name'];

    // Filter out the product to be removed
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($item) use ($product_name) {
        return $item['product_name'] !== $product_name;
    });
}

// Redirect back to the cart
header("Location: cart.php");
exit();
?>
