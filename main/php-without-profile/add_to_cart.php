<?php
session_start();

// Ensure the cart exists in the session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get the incoming product data
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // Add the product to the session cart
    $_SESSION['cart'][] = [
        'name' => $data['name'],
        'price' => $data['price'],
        'image' => $data['image'],
        'quantity' => $data['quantity']
    ];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
