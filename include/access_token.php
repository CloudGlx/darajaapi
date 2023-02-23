<?php
$config = array(
    "env"              => "live",

    "key"              => "7NeSKhwgkxhaab9ASpCUAwwoTL9ne1J1", //Enter your consumer key here
    "secret"           => "BkBKrXyXARl8PoQP", //Enter your consumer secret here
 
);

$access_token = ($config['env']  == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : " https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
$credentials = base64_encode($config['key'] . ':' . $config['secret']); 

$ch = curl_init($access_token);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$response = curl_exec($ch);
curl_close($ch);
$result = json_decode($response); 
$token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";
echo $token;
?>