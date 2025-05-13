<?php
session_start();

// Initialize total amount to 0
$total = 0;

// Check if the cart exists and is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<h2>Error</h2>";
    echo "<p>Your cart is empty. Please add items to proceed to payment.</p>";
    exit;
}

// Validate cart data before calculating the total
foreach ($_SESSION['cart'] as $item) {
    if (!isset($item['quantity'], $item['price']) || $item['quantity'] <= 0 || $item['price'] <= 0) {
        echo "<h2>Error</h2>";
        echo "<p>Invalid cart data. Please go back and try again.</p>";
        exit;
    }
}

// Calculate the total amount from the cart
foreach ($_SESSION['cart'] as $item) {
    $total += $item['quantity'] * $item['price'];
}

// Ensure the total is greater than 0
if ($total <= 0) {
    echo "<h2>Error</h2>";
    echo "<p>Invalid payment amount. Please go back and try again.</p>";
    exit;
}

// Institution code (replace with actual value)
$institutionCode = "614250";

// Retrieve or set an example order ID
$orderId = $_SESSION['id'] ?? '123456'; // Replace with actual logic to retrieve the order ID
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Instructions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .circle-check {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #4CAF50;
            color: white;
            display: inline-block;
            line-height: 35px;
            font-weight: bold;
            margin-right: 10px;
        }
        .btn, .btn-home {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px;
            background-color: #ff5722;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-size: 16px;
        }
        .btn-home {
            background-color: #007bff;
        }
        .btn:hover, .btn-home:hover {
            background-color: #e64a19;
        }
        .btn-home:hover {
            background-color: #0056b3;
        }
        h2, h3 {
            color: #333;
        }
        p {
            color: #444;
        }
        .amount-figures {
            font-size: 20px;
            font-weight: bold;
            color: black;
        }
        .institution-code {
            font-size: 20px;
            font-weight: bold;
            color: black;
        }
        ol {
            text-align: left;
            margin-left: 30px;
            color: #444;
        }
        ol li {
            margin-bottom: 8px;
            font-size: 14px;
        }
        strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="circle-check">✔</div>
        <h2>Awaiting Payment</h2>
        <a href="check_order.php" class="btn">Check My Order</a>
        <a href="index.php" class="btn-home">Home</a>
    </div>

    <div class="container">
        <h3>Payment Details</h3>
        <p><strong>Amount:</strong> <span class="amount-figures">KES <?php echo number_format($total, 2); ?></span></p>
        <p><strong>Institution Code:</strong> <span class="institution-code"><?php echo $institutionCode; ?></span></p>
    </div>

    <div class="container">
        <h3>Payment Guide</h3>
        <p>Follow these steps to complete your payment:</p>
        <ol>
            <li>Go to your SIM toolkit.</li>
            <li>Choose "Lipa na MPESA".</li>
            <li>Enter <strong><?php echo $institutionCode; ?></strong> as the business number.</li>
            <li>Enter your order ID <strong><?php echo $orderId; ?></strong> as the account number.</li>
            <li>Enter <strong>KES <?php echo number_format($total, 2); ?></strong>.</li>
            <li>Enter your MPESA PIN to complete the payment.</li>
        </ol>
    </div>
</body>
</html>
