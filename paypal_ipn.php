<?php
// Include database connection
include('db_connect.php');  // Make sure to adjust the path to your actual db connection file

// Step 1: Get the IPN message from PayPal
$ipnMessage = file_get_contents('php://input');

// Step 2: Log the IPN message (optional)
file_put_contents('ipn_log.txt', $ipnMessage . "\n", FILE_APPEND);

// Step 3: Verify the IPN message by sending it back to PayPal for validation
$verifyUrl = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'; // For sandbox, change to live URL for production

$data = array('cmd' => '_notify-validate') + $_POST;

$options = array(
    'http' => array(
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($data)
    )
);
$context = stream_context_create($options);
$response = file_get_contents($verifyUrl, false, $context);

// Step 4: Check the validation response
if (strcmp($response, "VERIFIED") == 0) {
    // Payment is verified
    $paymentStatus = $_POST['payment_status']; // 'Completed' or other statuses
    $orderId = $_POST['invoice']; // Assuming the order ID is stored in the invoice field
    $amountPaid = $_POST['mc_gross']; // Amount paid in USD
    $txnId = $_POST['txn_id']; // Transaction ID from PayPal

    if ($paymentStatus == 'Completed') {
        // Update the order status to 'paid'
        $sql = "UPDATE orders SET status = 'paid', transaction_id = '$txnId', amount = '$amountPaid' WHERE order_id = '$orderId'";

        if (mysqli_query($conn, $sql)) {
            echo "Payment verified and order status updated.";
        } else {
            echo "Error updating order status: " . mysqli_error($conn);
        }
    } else {
        // Payment failed or was pending
        echo "Payment failed or pending.";
    }
} else {
    // Invalid IPN message
    echo "Invalid IPN message.";
}
?>
