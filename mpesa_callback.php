<?php
// Capture the incoming JSON payload
$rawData = file_get_contents('php://input');

// Decode the JSON data into a PHP array
$data = json_decode($rawData, true);

// Log the raw data (for debugging purposes only)
file_put_contents('callback_log.txt', "Received data: " . $rawData . "\n", FILE_APPEND);

// Check if JSON decoding was successful
if ($data === null) {
    // Log the error if JSON decoding fails
    file_put_contents('callback_log.txt', "Error decoding JSON data: " . json_last_error_msg() . "\n", FILE_APPEND);
    echo "Error decoding JSON data.";
    exit; // Stop execution
}

// Validate the data structure
if (is_array($data) && isset($data['Body']['stkCallback'])) {
    $stkCallback = $data['Body']['stkCallback'];

    // Extract data from the callback response
    $MerchantRequestID = isset($stkCallback['MerchantRequestID']) ? $stkCallback['MerchantRequestID'] : 'Unknown';
    $CheckoutRequestID = isset($stkCallback['CheckoutRequestID']) ? $stkCallback['CheckoutRequestID'] : 'Unknown';
    $ResultCode = isset($stkCallback['ResultCode']) ? $stkCallback['ResultCode'] : null;
    $ResultDesc = isset($stkCallback['ResultDesc']) ? $stkCallback['ResultDesc'] : 'No Description';

    // Log extracted details
    file_put_contents('callback_log.txt', "MerchantRequestID: $MerchantRequestID, CheckoutRequestID: $CheckoutRequestID, ResultCode: $ResultCode, ResultDesc: $ResultDesc\n", FILE_APPEND);

    if ($ResultCode === 0) { // Successful transaction
        $Amount = isset($stkCallback['CallbackMetadata']['Item'][0]['Value']) ? $stkCallback['CallbackMetadata']['Item'][0]['Value'] : 0;
        $MpesaReceiptNumber = isset($stkCallback['CallbackMetadata']['Item'][1]['Value']) ? $stkCallback['CallbackMetadata']['Item'][1]['Value'] : 'Unknown';
        $TransactionDate = isset($stkCallback['CallbackMetadata']['Item'][3]['Value']) ? $stkCallback['CallbackMetadata']['Item'][3]['Value'] : 'Unknown';
        $PhoneNumber = isset($stkCallback['CallbackMetadata']['Item'][4]['Value']) ? $stkCallback['CallbackMetadata']['Item'][4]['Value'] : 'Unknown';

        // Log success message
        file_put_contents('callback_log.txt', "Payment of KES $Amount from $PhoneNumber was successful.\n", FILE_APPEND);

        echo "Payment of KES $Amount from $PhoneNumber was successful.";
    } else { // Failed transaction
        file_put_contents('callback_log.txt', "Transaction failed: $ResultDesc\n", FILE_APPEND);
        echo "Transaction failed: $ResultDesc";
    }
} else {
    // Log error if data is not valid
    file_put_contents('callback_log.txt', "Invalid callback data received.\n", FILE_APPEND);
    echo "Invalid callback data received.";
}
?>
