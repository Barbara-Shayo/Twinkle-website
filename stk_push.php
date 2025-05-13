<?php
$access_token = "EwbU1aZHO5kvEF3EevbWX6aiDNPO"; // Use the generated token
$url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

$headers = [
    "Content-Type: application/json",
    "Authorization: Bearer " . $access_token
];

$payload = json_encode([
    "BusinessShortCode" => "3224330", // Your Buy Goods till number
    "Password" => base64_encode("3224330" . "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919" . date("YmdHis")),
    "Timestamp" => date("YmdHis"),
    "TransactionType" => "CustomerPayBillOnline",
    "Amount" => 10, // Change this to the actual amount
    "PartyA" => "2547XXXXXXXX", // Customer's phone number
    "PartyB" => "3224330",
    "PhoneNumber" => "2547XXXXXXXX",
    "CallBackURL" => "https://twinkle and soul store.com/mpesa/callback.php",
    "AccountReference" => "Twinkle and Soul Emporium",
    "TransactionDesc" => "Payment for Order #1234"
]);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
$response = curl_exec($curl);
curl_close($curl);

echo $response;
?>
