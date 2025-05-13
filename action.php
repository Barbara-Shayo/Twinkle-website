<?php
// action.php

// Include the functions file
require_once 'function.php';

if(isset($_POST['phone']) && isset($_POST['amount'])){
    $phone = $_POST['phone'];
    $amount = $_POST['amount'];
    
    // Optional: Format phone number to the required format (e.g., 2547XXXXXXXX)
    if(substr($phone, 0, 1) === "0"){
        $phone = "254" . substr($phone, 1);
    }
    
    // Initiate the STK Push request
    $result = lipaNaMpesa($phone, $amount);
    
    // Output the response (or handle it accordingly)
    echo $result;
} else {
    echo json_encode(['error' => 'Phone number and amount are required']);
}
?>