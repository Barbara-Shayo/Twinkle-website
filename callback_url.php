<?php
// callback.php

// Set the header to indicate JSON response
header("Content-Type: application/json");

// Read the raw POST data from M-Pesa
$callbackResponse = file_get_contents('php://input');

// Log the response to a file for debugging (ensure this file is secured or remove in production)
file_put_contents('callback_log.txt', date("Y-m-d H:i:s") . " - " . $callbackResponse . "\n", FILE_APPEND);

// Decode the JSON response
$callbackData = json_decode($callbackResponse, true);

// Check if the expected data exists
if (isset($callbackData['Body']['stkCallback'])) {
    $stkCallback = $callbackData['Body']['stkCallback'];
    
    // Retrieve necessary values from the callback
    $resultCode       = $stkCallback['ResultCode'];
    $resultDesc       = $stkCallback['ResultDesc'];
    $merchantRequestID = $stkCallback['MerchantRequestID'];
    $checkoutRequestID = $stkCallback['CheckoutRequestID'];
    
    // Process the callback based on the result code
    if ($resultCode === 0) {
        // Payment was successful
        // You can further extract transaction details from CallbackMetadata if needed
        if (isset($stkCallback['CallbackMetadata']['Item'])) {
            $items = $stkCallback['CallbackMetadata']['Item'];
            // Iterate over $items to extract transaction details (e.g., Amount, MpesaReceiptNumber, TransactionDate)
            // Update your order or payment records in your database accordingly.
        }
    } else {
        // Payment failed or was cancelled
        // Handle failure (e.g., log the error, notify the user, update the database, etc.)
    }
}

// Send an acknowledgment response back to M-Pesa
$response = [
    'ResultCode' => 0,
    'ResultDesc' => 'Accepted'
];
echo json_encode($response);
?>