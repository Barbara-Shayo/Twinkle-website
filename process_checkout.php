<?php
session_start();

// Check if the cart is not empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    // If the cart is empty, redirect to the shop page or display a message
    header("Location: shop.php");
    exit();
}

// Initialize the total amount
$totalAmount = 0;

// Calculate the total amount based on the items in the cart
foreach ($_SESSION['cart'] as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

// Check if the total is valid (could add more validation as needed)
if ($totalAmount <= 0) {
    // Redirect if the total is invalid
    header("Location: shop.php");
    exit();
}

// Process the payment (example: Payment gateway logic, you can replace this with your actual payment code)
// Assuming user selects payment method (e.g., PayPal, M-Pesa)

// Example: Using a simple payment method (you can replace this with actual payment gateway integration)
$paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

// Ensure payment method is selected
if (empty($paymentMethod)) {
    // Redirect if no payment method was selected
    header("Location: checkout.php?error=payment_method_required");
    exit();
}

// Assuming payment is successful (you should implement actual payment gateway handling here)
$paymentSuccess = true; // This should be based on real payment gateway logic

if ($paymentSuccess) {
    // Process successful payment (e.g., update database, send confirmation email, etc.)
    // Clear the cart after successful payment
    unset($_SESSION['cart']);

    // Redirect to a success page or thank you page
    header("Location: payment_success.php");
    exit();
} else {
    // If payment failed, redirect back to the checkout page with an error message
    header("Location: checkout.php?error=payment_failed");
    exit();
}
?>
