<?php
$consumerKey = "vJfttLryQCGRtHNm0qeFK5SpvXetnUI824Ycr3Yzs0xDWHeF"; 
$consumerSecret = "eE6jxLFuU3gAQusRGumh11phXXngzejMy9fR2LFiHLt5rCppQ1S1UQPGWm8S44SG"; 

$credentials = base64_encode($consumerKey . ":" . $consumerSecret);
$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic " . $credentials));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($curl);
curl_close($curl);

$response = json_decode($result);
$access_token = $response->access_token;

// Save the token in a file
file_put_contents('access_token.txt', $access_token);

echo "Access Token: " . $access_token;
?>
