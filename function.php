<?php
// functions.php

// Function to generate an access token from Safaricom API
function generateAccessToken(){
    $consumerKey ='aHptIiAs3uRyhlMLJs5bnBXx8m3NX8mlW1Tjfpf367Qrv7hK';
    $consumerSecret ='PM2ejiQULqrohKR5PlJQlFLnkRRy0t5PcNAlhwu4oqphG61dOAecxfGve26cJHMN';
    $credentials = base64_encode($consumerKey.":".$consumerSecret);
    $url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic ".$credentials));
    curl_setopt($curl, CURLOPT_HEADER,false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curlResponse = curl_exec($curl);
    $formattedResponse = json_decode($curlResponse);
    $accessToken = $formattedResponse;
    return $accessToken;
}

// Function to initiate Lipa Na M-Pesa STK Push
function lipaNaMpesa($phoneNumber, $amount){
    $accessToken = generateAccessToken();
    
    // STK Push API endpoint (use production URL when live)
    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    
    $businessShortCode = 'YOUR_TILL_NUMBER'; // Your Buy Goods Till Number
    $passkey = 'ec44daa9545a4f54cc2a9599501c7acdac0c9eee728ab62d52787578035878f4';
    $timestamp = date('YmdHis');
    $password = base64_encode($businessShortCode . $passkey . $timestamp);
    
    // Set your callback URL (must be publicly accessible)
    $callbackUrl = 'https://4cfe-102-135-168-187.ngrok-free.app/callback.php';
    
    $curl_post_data = array(
        'BusinessShortCode' => $businessShortCode,
        'Password'          => $password,
        'Timestamp'         => $timestamp,
        'TransactionType'   => 'CustomerBuyGoodsOnline', // CustomerBuyGoodsOnline
        'Amount'            => $amount,
        'PartyA'            => $phoneNumber, // The phone number sending the money
        'PartyB'            => $businessShortCode,
        'PhoneNumber'       => $phoneNumber,
        'CallBackURL'       => $callbackUrl,
        'AccountReference'  => 'CompanyXYZ', // Reference for the transaction
        'TransactionDesc'   => 'Payment for goods'
    );
    
    $data_string = json_encode($curl_post_data);
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    
    $response = curl_exec($curl);
    curl_close($curl);
    
    return $response;
}
?>