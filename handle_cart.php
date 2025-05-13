<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Save the current page to redirect back after login
    $_SESSION['redirect_to'] = 'cart.php';

    // Redirect to the login page
    header("Location: new_login.php");
    exit();
}

// Ensure a product ID is provided
if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    // Initialize the cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add product to cart or increment quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }

    // Redirect to the cart page
    header("Location: cart.php");
    exit();
}

// If no product ID is provided, redirect to the shop page
header("Location: shop.php");
exit();
?>
