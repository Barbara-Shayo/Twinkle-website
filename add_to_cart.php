
<?php
session_start();

// Initialize the cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Validate the input
$id = $_POST['id'] ?? null;
$detail = $_POST['details'] ?? null;
$price = $_POST['price'] ?? null;
$image = $_POST['image'] ?? null;

if (!$id || !$description || !$price || !$image) {
    die("Error: Missing product data.");
}

// Validate numeric values
if (!is_numeric($price) || $price <= 0) {
    die("Error: Invalid price.");
}

// Create the product array
$product = [
    'id' => $id,
    'details' => $detail,
    'price' => (float)$price,
    'image' => $image
];

// Add the product to the cart
$_SESSION['cart'][] = $product;

// Redirect to cart.php
header('Location: cart.php');
exit;
