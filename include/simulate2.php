<?php 

session_start();
require_once('conn.php');

$errors  = array();
$errmsg  = '';

$config = array(
    "env"              => "live",
    "BusinessShortCode"=> "6076743",
    "PartyB"           =>"9955753",
    "key"              => "7NeSKhwgkxhaab9ASpCUAwwoTL9ne1J1", //Enter your consumer key here
    "secret"           => "BkBKrXyXARl8PoQP", //Enter your consumer secret here
    "username"         => "CLOUDG",
    "TransactionType"  => "CustomerBuyGoodsOnline",
    "passkey"          => "acde58b0685421b2380ccfc9803352e918a39a9e301fc2c49cc1b8ab38f5a53b", //Enter your passkey here
   //"CallBackURL"      => "https://cloudgalaxypaymets.premier-writers.com/pay/include/confirmation_url.php", //When using localhost, Use Ngrok to forward the response to your Localhost
    "CallBackURL"      => "https://1002-154-159-244-60.in.ngrok.io/mpesa/include/callback.php", 
    "AccountReference" => "9955753",
    "TransactionDesc"  => "Payment of WIFI" ,
    
);



if (isset($_POST['phone_number']) && isset($_POST['pay'])) {
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
    
    

    $phone = $_POST['phone_number'];
    
    $amount =$_POST['amount'];

    $phone = (substr($phone, 0, 1) == "+") ? str_replace("+", "", $phone) : $phone;
    $phone = (substr($phone, 0, 1) == "0") ? preg_replace("/^0/", "254", $phone) : $phone;
    $phone = (substr($phone, 0, 1) == "7") ? "254{$phone}" : $phone;



    $access_token = ($config['env']  == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
    $credentials = base64_encode($config['key'] . ':' . $config['secret']); 
    
    $ch = curl_init($access_token);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response); 
    $token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";

    $timestamp = date("YmdHis");
    $password  = base64_encode($config['BusinessShortCode'] . "" . $config['passkey'] ."". $timestamp);

    $curl_post_data = array( 
        "BusinessShortCode" => $config['BusinessShortCode'],
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => $config['TransactionType'],
        "Amount" => $amount,
        "PartyA" => $phone,
        "PartyB" => $config['PartyB'],
        "PhoneNumber" => $phone,
        "CallBackURL" => $config['CallBackURL'],
        "AccountReference" => $config['AccountReference'],
        "TransactionDesc" => $config['TransactionDesc'],
    ); 

    $data_string = json_encode($curl_post_data);

    $endpoint = ($config['env'] == "live") ? "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest" : "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest"; 

    $ch = curl_init($endpoint );
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer '.$token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response     = curl_exec($ch);
    curl_close($ch);

    $result = json_decode(json_encode(json_decode($response)), true);

    if(!preg_match('/^[0-9]{10}+$/', $phone) && array_key_exists('errorMessage', $result)){
        $errors['phone'] = $result["errorMessage"];
    }

    if($result['ResponseCode'] === "0"){         //STK Push request successful

        $MerchantRequestID = $result['MerchantRequestID'];
        $CheckoutRequestID = $result['CheckoutRequestID'];

        //Saves your request to a database
    
       
        $sql = "INSERT INTO `requst`(`orderid`, `amount`, `phone`, `CheckoutRequestID`, `MerchantRequestID`) VALUES ('".$orderNo."','".$amount."','".$phone."','".$CheckoutRequestID."','".$MerchantRequestID."');";
        
        if ($conn->query($sql) === TRUE){
            $_SESSION["MerchantRequestID"] = $MerchantRequestID;
            $_SESSION["CheckoutRequestID"] = $CheckoutRequestID;
            $_SESSION["phone"] = $phone;
            $_SESSION["orderNo"] = $orderNo;

           // header('location: ../confirm-payment.php');
        }else{
            $errors['database'] = "Unable to initiate your order: ".$conn->error;;  
            foreach($errors as $error) {
                $errmsg .= $error . '<br />';
            } 
        }
        
    }else{
        $errors['mpesastk'] = $result['errorMessage'];
        foreach($errors as $error) {
            $errmsg .= $error . '<br />';
        }
    }
    
}

?>
