<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
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
