<?php
session_start(); // Make sure the session is started

// Check if the 'total' key is set in the session or as a GET parameter
if (isset($_SESSION['total'])) {
    $total = $_SESSION['total'];  // Get the total from the session
} elseif (isset($_GET['total'])) {
    $total = $_GET['total'];  // Get the total from the GET parameters (URL)
} else {
    // If 'total' is not set, redirect to another page (e.g., error or checkout page)
    header('Location: error_page.php');  // Redirect to an error page or checkout page
    exit();  // Stop further execution to ensure the redirect happens
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Payment</title>
</head>
<body>
    <h1>Checkout</h1>

    <p>Total Amount: $<?php echo number_format($total, 2); ?></p>

    <h2>Choose Payment Method:</h2>
    <form action="process_payment.php" method="POST">
        <label>
            <input type="radio" name="payment_method" value="paypal"> PayPal
        </label><br>
        <label>
            <input type="radio" name="payment_method" value="mpesa"> M-Pesa
        </label><br>

        <button type="submit">Proceed with Payment</button>
    </form>
</body>
</html>
