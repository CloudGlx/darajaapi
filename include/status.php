<?php
/*
if(!isset($_POST['confirm_payment'])){
   echo 'Wrong path';
   exit;
*/
session_start();
header("Content-Type:application/json");
require_once 'conn.php';
/*Call function with these configurations*/
    $env="live";
    $type = 2;
    $shortcode = '9955753'; 
    $key = "sZUIM7g5yJhsV33roxstCM1StxYhvKLz"; //Put your key here
    $secret = "zqv9M7XBGpQ9CSJ5";  //Put your secret here
    $initiatorName = "testapi";
    $initiatorPassword = "Safaricom999!*!";
    $results_url = "https://2975-154-159-244-60.in.ngrok.io/mpro/include/transation_results.php"; //Endpoint to receive results Body
    $timeout_url = "https://mydomain.com/TransactionStatus/queue/"; //Endpoint to to go to on timeout
/*End  configurations*/

/*GET TARNSCTION IF FROM DATABASE*/

/*

if(!isset($_SESSION['CheckoutRequestID'])){
      
    echo "Ivalid Transactions ID!";
    exit;
}

$CheckoutRequestID=$_SESSION['CheckoutRequestID'];
$sql="SELECT * FROM requst WHERE CheckoutRequestID='$CheckoutRequestID'";
$result=mysqli_query($conn, $sql);
$row=mysqli_fetch_array($result);
$transactionID = $row['MpesaCode'];    

  */ 
/*End transaction code validation*/

    //$transactionID = $_GET["transactionID"]; 
    $transactionID = "OEI2AK4Q16";

    $command = "TransactionStatusQuery";
    $remarks = "Transaction succefully made"; 
    $occasion = "Transaction Status Query";
    $callback = null ;

    
  //  $access_token = ($env == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
  $access_token = ($env == "live") ? "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
    $credentials = base64_encode($key . ':' . $secret); 
    
    $ch = curl_init($access_token);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response); 

    //echo $result->{'access_token'};
    
    $token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";

    $publicKey = file_get_contents(__DIR__ . "/SandboxCertificate.cer"); 
    $isvalid = openssl_public_encrypt($initiatorPassword, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING); 
    $password = base64_encode($encrypted);

    //echo $token;

    $curl_post_data = array( 
        "Initiator" => $initiatorName, 
        "SecurityCredential" => $password, 
        "CommandID" => $command, 
        "TransactionID" => $transactionID, 
        "PartyA" => $shortcode, 
        "IdentifierType" => $type, 
        "ResultURL" => $results_url, 
        "QueueTimeOutURL" => $timeout_url, 
        "Remarks" => $remarks, 
        "Occasion" => $occasion,
    ); 

    $data_string = json_encode($curl_post_data);

    //echo $data_string;

    $endpoint = ($env == "live") ? "https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query" : "https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query"; 
   // $endpoint = ($env == "live") ? "https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query" : "https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query"; 

    $ch2 = curl_init($endpoint);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer '.$token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch2, CURLOPT_POST, 1);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
    $response     = curl_exec($ch2);
    curl_close($ch2);

    //echo "Authorization: ". $response;

    $result = json_decode($response); 
/*
	$mpesaResponse = file_get_contents('php://input');
		// log the response
		$logFile = "TransactionStatus.json";
		// write to file
		$log = fopen($logFile, "a");
		fwrite($log, $mpesaResponse);
		fclose($log);
		//echo $response;

*/


    $verified = $result->{'ResponseCode'};
    if($verified === "0"){
        echo "Transaction verified as TRUE";
    }else{
        echo "Transaction doesnt exist";
    }
