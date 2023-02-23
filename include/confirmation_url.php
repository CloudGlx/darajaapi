<?php
    header("Content-Type: application/json");

   // $response = '{ "ResultCode": 0, "ResultDesc": "Accepted" }';

$content = file_get_contents('json2.json'); //Receives the JSON Result from safaricom
$res = json_decode($content, true); //Convert the json to an array
date_default_timezone_set("Africa/Nairobi");

$dataToLog = array(
    date("Y-m-d H:i:s"), //Date and time
    " TransactionType: ".$res['TransactionType'],
    " TransID: ".$res['TransID'],
    " TransAmount: ".$res['TransAmount'],
	" OrgAccountBalance: ".$res['OrgAccountBalance'],
    " FirstName: ".$res['FirstName'],
    //IF USER PAYS
    
);
$data = implode(" - ", $dataToLog);
$data .= PHP_EOL;
file_put_contents('logs/confirmation.log', $data, FILE_APPEND); //Logs the results to our log file
//GENERATE ORDER CODE
//PREPARE JSON RESPONSE USING MPESA CODE



    //auto generate order id
    function orderid($length = 25) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    //usage 
    $orderNo = orderid(8);
    
//INSERT DATA INTO DATABASE



require_once 'conn.php';
$date= date("Y-m-d H:i:s");//date and time
$TransactionType=$res['TransactionType'];
$TransID=$res['TransID'];
$TransTime=$res['TransTime'];
$TransAmount=$res['TransAmount'];
$BusinessShortCode=$res['BusinessShortCode'];
$BillRefNumber=$res['BillRefNumber'];
$InvoiceNumber=$res['InvoiceNumber'];
$OrgAccountBalance=$res['OrgAccountBalance'];
$ThirdPartyTransID=$res['ThirdPartyTransID'];
$MSISDN=$res['MSISDN'];
$FirstName=$res['FirstName'];



$sql = "INSERT INTO `confirmation`(`TransactionType`, `TransID`, `TransAmount`, `BusinessShortCode`, `BillRefNumber`, `InvoiceNumber`, `OrgAccountBalance`, `MSISDN`,`FirstName`,`Orderid`,`date`)
 VALUES ('".$TransactionType."','".$TransID."','".$TransAmount."','".$BusinessShortCode."','".$BillRefNumber."','".$InvoiceNumber."','".$OrgAccountBalance."', '".$MSISDN."','".$FirstName."','".$orderNo."','".$date."');";
if(!$re=mysqli_query($conn,$sql)){
    $error=mysqli_error($conn);
    $data=$date. ' This Error occured For TransID: '.$TransID.'Error: ' .$error;
    file_put_contents('logs/conUrlDatabase.log',PHP_EOL. $data, FILE_APPEND);
    //SENDS EMAIL NOTIFICATION
  
}
else{
    $data=$date. 'Record Inserted for TransID:'.$TransID;
    file_put_contents('logs/conUrlDatabase.log',PHP_EOL.$data, FILE_APPEND);
    //mysqli_close($conn);



/**********************CHECK STATUS START***************** */
$env="live";
$type = 4;
$shortcode = '6076743'; 
$key = "7NeSKhwgkxhaab9ASpCUAwwoTL9ne1J1"; //Put your key here
$secret = "BkBKrXyXARl8PoQP";  //Put your secret here
$initiatorName = "CLOUDT";
$initiatorPassword = "cloudGALAXY?20";
$results_url = "https://fa96-154-159-244-60.in.ngrok.io/mpro/include/transation_results.php"; //Endpoint to receive results Body
$timeout_url = "https://mydomain.com/TransactionStatus/queue/"; //Endpoint to to go to on timeout
/*End  configurations*/


$command = "TransactionStatusQuery";
$remarks = "Transaction succefully made"; 
$occasion = "Transaction Status Query";
$callback = null ;


$access_token = ($env == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
// $access_token = ($env == "live") ? "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
$credentials = base64_encode($key . ':' . $secret); 

$ch = curl_init($access_token);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response); 

//echo $result->{'access_token'};

$token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";

$publicKey = file_get_contents(__DIR__ . "/ProductionCertificate.cer"); 
$isvalid = openssl_public_encrypt($initiatorPassword, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING); 
$password = base64_encode($encrypted);

//echo $token;

$curl_post_data = array( 
    "Initiator" => $initiatorName, 
    "SecurityCredential" => $password, 
    "CommandID" => $command, 
    "TransactionID" => $TransID, 
    "PartyA" => $shortcode, 
    "IdentifierType" => $type, 
    "ResultURL" => $results_url, 
    "QueueTimeOutURL" => $timeout_url, 
    "Remarks" => $remarks, 
    "Occasion" => $occasion,
); 

$data_string = json_encode($curl_post_data);

//echo $data_string;

// $endpoint = ($env == "live") ? "https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query" : "https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query"; 
$endpoint = ($env == "live") ? "https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query" : "https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query"; 

$ch2 = curl_init($endpoint);
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.$token,
    'Content-Type: application/json'
]);
curl_setopt($ch2, CURLOPT_POST, 1);
curl_setopt($ch2, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
$response= curl_exec($ch2);
curl_close($ch2);

//echo "Authorization: ". $response;

$result = json_decode($response); 

/**********************CHECK STATUS ENDS***************** */

}


?>

