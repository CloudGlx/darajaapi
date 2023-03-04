<?php
header("Content-Type: application/json");

// $response = '{ "ResultCode": 0, "ResultDesc": "Accepted" }';

$content = file_get_contents('php://input'); //Receives the JSON Result from safaricom
$res = json_decode($content, true); //Convert the json to an array
date_default_timezone_set("Africa/Nairobi");
$day = date('N');
$Time = date("H:i:s  Y-m-d ");
$dataToLog = array(
    date("Y-m-d H:i:s"), //Date and time

    " Name And NUmber: " . $res['Result']['ResultParameters']['ResultParameter'][0]['Value'],
    " TransID: " . $res['Result']['ResultParameters']['ResultParameter'][12]['Value'],
    " TranStatus: " . $res['Result']['ResultParameters']['ResultParameter'][8]['Value'],
    " Amount: " . $res['Result']['ResultParameters']['ResultParameter'][10]['Value'],
);

$data = implode(" - ", $dataToLog);
$data .= PHP_EOL;
file_put_contents('logs/TEST.log', $data, FILE_APPEND); //Logs the results to our log file

//$res['Result']['ResultParameters']['ResultParameter'][1]['Key'];
$string = $res['Result']['ResultParameters']['ResultParameter'][0]['Value'];
$MpesaCode = $res['Result']['ResultParameters']['ResultParameter'][12]['Value'];
$TransactionStatus = $res['Result']['ResultParameters']['ResultParameter'][8]['Value'];
$amount = $res['Result']['ResultParameters']['ResultParameter'][10]['Value'];

$ex = explode(" ", $string, 3);
$phone = $ex[0];
$FullName = $ex[2];


$phone = (substr($phone, 0, 1) == "+") ? str_replace("+", "", $phone) : $phone;
$phone = (substr($phone, 0, 1) == "0") ? preg_replace("/^0/", "254", $phone) : $phone;
$phone = (substr($phone, 0, 1) == "7") ? "254{$phone}" : $phone;


//UPDATE THIS ITO DATABASE
require_once 'conn.php';
//$sql= "UPDATE confirmation SET ResultDesc='$ResultDesc', MpesaCode='$MpesaCode', TransactionDate='$TransactionDate', status = 'SUCCESS' WHERE CheckoutRequestID='$CheckoutRequestID'";
if ($TransactionStatus == 'Completed') {
    $sql = "UPDATE confirmation SET MSISDN='$phone', FirstName='$FullName' WHERE TransID='$MpesaCode'";
    $result = mysqli_query($conn, $sql);
    //MSG BODY
    //MSG BODY
    $link = "http://wifi.login/login";
    $support = "0716815356";
    $support2 = "0715878800";
    $renewlink="http://wifi.login/pass/";
    /***SEND SMS CODE */

 


    //SEND SMS FUNCTION
    function sendsms($phone, $body)
    {

        $data = [

            "apikey"       => "b4368bb35b50907e07337ebff26be508",
            "partnerID"    => "6356",  //6356
            "message"      => $body,
            "shortcode"    => "CloudGalaxy",
            "mobile"       => $phone
        ];
        //endpoint
        $url = 'https://sms.textsms.co.ke/api/services/sendsms/';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

   /*SCHEDULE SMS FUNCTION  */
  
    //SMS BODY FUNCTION for code
    function MsgBody($Fname, $AccessCode, $UpTime, $link, $support, $Device)
    {
        $MsgBody = "Hi " . $Fname . ",Connect to CloudGalaxy Wifi to get started" . "\n" .
            "Access Code:" . $AccessCode . "" . "\n" .
            "Devices:" . $Device . "" . "\n" .
            "Uptime:" . $UpTime . "" . "\n" .
            "LoginLink: " . $link . "" . "\n" .
            "For help call " . $support . "";

        return $MsgBody;
    }


    //RENEW SMS BODY


    //cut name

    $ex = explode(" ", $FullName, 3);
    $Fname = $ex[0];


    $AdmBody = "Hi Admin, You Dont have active Accesse codes for day " . $day . "";
    $AdmPhone = "254716815356";

//6 HORS ONE DEVICE
    if ($amount == '25') {

        /********GET CODE FROM DATABASE AS PER DAY******/

        $sql = "SELECT * FROM code WHERE day='$day' AND CodeStatus=0 AND Device=1 AND Period=6 LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($result);
        if ($num == 0) {

            sendsms($AdmPhone, $AdmBody);
            exit;
        }
        $row = mysqli_fetch_row($result);
        $AccessCode = $row[1];

        $Device=1;
        $UpTime = "6Hrs";

        $MsgBody = MsgBody($Fname, $AccessCode, $UpTime, $link, $support, $Device);
        $data = sendsms($phone, $MsgBody);
        $res = json_decode($data, true);

        $ReCode = $res['responses'][0]['response-code'];
        $ReDec = $res['responses'][0]['response-description'];
        $MsgID = $res['responses'][0]['messageid'];

        //UPDATE CODE TO DATABASE
        if ($ReCode == 200) {
            //SMS WELL SEND
            //update code AS TAKEN
            $MsgStatus = 1; //0 for not sent
            $start_time=$Time;
            $end_time = date("Y-m-d H:i:s", strtotime('+6 hours'));
            $sendtime=strtotime($end_time);//Unix timestamp.
            // $sql= "UPDATE code SET CodeStatus='1', AssignedTo=$phone, Amount=$amount WHERE Acode='$AccessCode'"; 
            $sql = "UPDATE code SET StartTime='$start_time', EndTime='$end_time', CodeStatus='1', AssignedTO='$phone', Amount='$amount', Fname='$Fname',Time='$Time' WHERE Acode='$AccessCode'";
            $result = mysqli_query($conn, $sql);

            //SCHEDULING SMS


            //SAVE SMS INFO TO SMS DATABASE
            //$sql = "INSERT INTO `sms`(`SmsId`, `SmsStatus`, `SmsBody`) VALUES ('".$MsgID."','".$MsgStatus."','".$MsgBody."');";
            //$result=mysqli_query($conn, $sql);  
            exit;
        } else {
            //ERROR OCURRED
            $ErrorCode = $res['response-code'];
            $ErrorDec = $res['response-description'];
            //SEND EMAIL TO ADMIN WITH ERROR CODE
        }
    } elseif ($amount == '45') {

        /********GET CODE FROM DATABASE AS PER DAY******/

        $sql = "SELECT * FROM code WHERE day='$day' AND CodeStatus=0 AND Device=2 AND Period=6 LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($result);
        if ($num == 0) {

            sendsms($AdmPhone, $AdmBody);
            exit;
        }
        $row = mysqli_fetch_row($result);
        $AccessCode = $row[1];

        $Device=2;
        $UpTime = "6Hrs";

        $MsgBody = MsgBody($Fname, $AccessCode, $UpTime, $link, $support, $Device);
        $data = sendsms($phone, $MsgBody);
        $res = json_decode($data, true);

        $ReCode = $res['responses'][0]['response-code'];
        $ReDec = $res['responses'][0]['response-description'];
        $MsgID = $res['responses'][0]['messageid'];

        //UPDATE CODE TO DATABASE
        if ($ReCode == 200) {
            //SMS WELL SEND
            //update code AS TAKEN
            $MsgStatus = 1; //0 for not sent
            $start_time=$Time;
            $end_time = date("Y-m-d H:i:s", strtotime('+6 hours'));
            $sendtime=strtotime($end_time);//Unix timestamp.
            // $sql= "UPDATE code SET CodeStatus='1', AssignedTo=$phone, Amount=$amount WHERE Acode='$AccessCode'"; 
            $sql = "UPDATE code SET StartTime='$start_time', EndTime='$end_time', CodeStatus='1', AssignedTO='$phone', Amount='$amount', Fname='$Fname',Time='$Time' WHERE Acode='$AccessCode'";
            $result = mysqli_query($conn, $sql);

            //SCHEDULING SMS


            //SAVE SMS INFO TO SMS DATABASE
            //$sql = "INSERT INTO `sms`(`SmsId`, `SmsStatus`, `SmsBody`) VALUES ('".$MsgID."','".$MsgStatus."','".$MsgBody."');";
            //$result=mysqli_query($conn, $sql);  
            exit;
        } else {
            //ERROR OCURRED
            $ErrorCode = $res['response-code'];
            $ErrorDec = $res['response-description'];
            //SEND EMAIL TO ADMIN WITH ERROR CODE
        }
    

    } elseif ($amount == '35') {

     
        /********GET CODE FROM DATABASE AS PER DAY******/

        $sql = "SELECT * FROM code WHERE day='$day' AND CodeStatus=0 AND Device=1 AND Period=12 LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($result);
        if ($num == 0) {

            sendsms($AdmPhone, $AdmBody);
            exit;
        }
        $row = mysqli_fetch_row($result);
        $AccessCode = $row[1];

        $Device=1;
        $UpTime = "12Hrs";

        $MsgBody = MsgBody($Fname, $AccessCode, $UpTime, $link, $support, $Device);
        $data = sendsms($phone, $MsgBody);
        $res = json_decode($data, true);

        $ReCode = $res['responses'][0]['response-code'];
        $ReDec = $res['responses'][0]['response-description'];
        $MsgID = $res['responses'][0]['messageid'];

        //UPDATE CODE TO DATABASE
        if ($ReCode == 200) {
            //SMS WELL SEND
            //update code AS TAKEN
            $MsgStatus = 1; //0 for not sent
            $start_time=$Time;
            $end_time = date("Y-m-d H:i:s", strtotime('+12 hours'));
            $sendtime=strtotime($end_time);//Unix timestamp.
            // $sql= "UPDATE code SET CodeStatus='1', AssignedTo=$phone, Amount=$amount WHERE Acode='$AccessCode'"; 
            $sql = "UPDATE code SET StartTime='$start_time', EndTime='$end_time', CodeStatus='1', AssignedTO='$phone', Amount='$amount', Fname='$Fname',Time='$Time' WHERE Acode='$AccessCode'";
            $result = mysqli_query($conn, $sql);

            //SCHEDULING SMS


            //SAVE SMS INFO TO SMS DATABASE
            //$sql = "INSERT INTO `sms`(`SmsId`, `SmsStatus`, `SmsBody`) VALUES ('".$MsgID."','".$MsgStatus."','".$MsgBody."');";
            //$result=mysqli_query($conn, $sql);  
            exit;
        } else {
            //ERROR OCURRED
            $ErrorCode = $res['response-code'];
            $ErrorDec = $res['response-description'];
            //SEND EMAIL TO ADMIN WITH ERROR CODE
        }


    } elseif ($amount == '65') {

      
        /********GET CODE FROM DATABASE AS PER DAY******/

        $sql = "SELECT * FROM code WHERE day='$day' AND CodeStatus=0 AND Device=2 AND Period=12 LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($result);
        if ($num == 0) {

            sendsms($AdmPhone, $AdmBody);
            exit;
        }
        $row = mysqli_fetch_row($result);
        $AccessCode = $row[1];

        $Device=2;
        $UpTime = "12Hrs";

        $MsgBody = MsgBody($Fname, $AccessCode, $UpTime, $link, $support, $Device);
        $data = sendsms($phone, $MsgBody);
        $res = json_decode($data, true);

        $ReCode = $res['responses'][0]['response-code'];
        $ReDec = $res['responses'][0]['response-description'];
        $MsgID = $res['responses'][0]['messageid'];

        //UPDATE CODE TO DATABASE
        if ($ReCode == 200) {
            //SMS WELL SEND
            //update code AS TAKEN
            $MsgStatus = 1; //0 for not sent
            $start_time=$Time;
            $end_time = date("Y-m-d H:i:s", strtotime('+12 hours'));
            $sendtime=strtotime($end_time);//Unix timestamp.
            // $sql= "UPDATE code SET CodeStatus='1', AssignedTo=$phone, Amount=$amount WHERE Acode='$AccessCode'"; 
            $sql = "UPDATE code SET StartTime='$start_time', EndTime='$end_time', CodeStatus='1', AssignedTO='$phone', Amount='$amount', Fname='$Fname',Time='$Time' WHERE Acode='$AccessCode'";
            $result = mysqli_query($conn, $sql);

            //SCHEDULING SMS


            //SAVE SMS INFO TO SMS DATABASE
            //$sql = "INSERT INTO `sms`(`SmsId`, `SmsStatus`, `SmsBody`) VALUES ('".$MsgID."','".$MsgStatus."','".$MsgBody."');";
            //$result=mysqli_query($conn, $sql);  
            exit;
        } else {
            //ERROR OCURRED
            $ErrorCode = $res['response-code'];
            $ErrorDec = $res['response-description'];
            //SEND EMAIL TO ADMIN WITH ERROR CODE
        }


    } elseif ($amount == '40') {

        
        /********GET CODE FROM DATABASE AS PER DAY******/

        $sql = "SELECT * FROM code WHERE day='$day' AND CodeStatus=0 AND Device=1 AND Period=24 LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($result);
        if ($num == 0) {

            sendsms($AdmPhone, $AdmBody);
            exit;
        }
        $row = mysqli_fetch_row($result);
        $AccessCode = $row[1];

        $Device=1;
        $UpTime = "24Hrs";

        $MsgBody = MsgBody($Fname, $AccessCode, $UpTime, $link, $support, $Device);
        $data = sendsms($phone, $MsgBody);
        $res = json_decode($data, true);

        $ReCode = $res['responses'][0]['response-code'];
        $ReDec = $res['responses'][0]['response-description'];
        $MsgID = $res['responses'][0]['messageid'];

        //UPDATE CODE TO DATABASE
        if ($ReCode == 200) {
            //SMS WELL SEND
            //update code AS TAKEN
            $MsgStatus = 1; //0 for not sent
            $start_time=$Time;
            $end_time = date("Y-m-d H:i:s", strtotime('+24 hours'));
            $sendtime=strtotime($end_time);//Unix timestamp.
            // $sql= "UPDATE code SET CodeStatus='1', AssignedTo=$phone, Amount=$amount WHERE Acode='$AccessCode'"; 
            $sql = "UPDATE code SET StartTime='$start_time', EndTime='$end_time', CodeStatus='1', AssignedTO='$phone', Amount='$amount', Fname='$Fname',Time='$Time' WHERE Acode='$AccessCode'";
            $result = mysqli_query($conn, $sql);

            //SCHEDULING SMS


            //SAVE SMS INFO TO SMS DATABASE
            //$sql = "INSERT INTO `sms`(`SmsId`, `SmsStatus`, `SmsBody`) VALUES ('".$MsgID."','".$MsgStatus."','".$MsgBody."');";
            //$result=mysqli_query($conn, $sql);  
            exit;
        } else {
            //ERROR OCURRED
            $ErrorCode = $res['response-code'];
            $ErrorDec = $res['response-description'];
            //SEND EMAIL TO ADMIN WITH ERROR CODE
        }
        //24 2 DEVICES
    } elseif ($amount == '75') {
        /********GET CODE FROM DATABASE AS PER DAY******/

        $sql = "SELECT * FROM code WHERE day='$day' AND CodeStatus=0 AND Device=2 AND Period=24 LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($result);
        if ($num == 0) {

            sendsms($AdmPhone, $AdmBody);
            exit;
        }
        $row = mysqli_fetch_row($result);
        $AccessCode = $row[1];

        $Device=2;
        $UpTime = "24Hrs";

        $MsgBody = MsgBody($Fname, $AccessCode, $UpTime, $link, $support, $Device);
        $data = sendsms($phone, $MsgBody);
        $res = json_decode($data, true);

        $ReCode = $res['responses'][0]['response-code'];
        $ReDec = $res['responses'][0]['response-description'];
        $MsgID = $res['responses'][0]['messageid'];

        //UPDATE CODE TO DATABASE
        if ($ReCode == 200) {
            //SMS WELL SEND
            //update code AS TAKEN
            $MsgStatus = 1; //0 for not sent
            $start_time=$Time;
            $end_time = date("Y-m-d H:i:s", strtotime('+24 hours'));
            $sendtime=strtotime($end_time);//Unix timestamp.
            // $sql= "UPDATE code SET CodeStatus='1', AssignedTo=$phone, Amount=$amount WHERE Acode='$AccessCode'"; 
            $sql = "UPDATE code SET StartTime='$start_time', EndTime='$end_time', CodeStatus='1', AssignedTO='$phone', Amount='$amount', Fname='$Fname',Time='$Time' WHERE Acode='$AccessCode'";
            $result = mysqli_query($conn, $sql);

            //SCHEDULING SMS


            //SAVE SMS INFO TO SMS DATABASE
            //$sql = "INSERT INTO `sms`(`SmsId`, `SmsStatus`, `SmsBody`) VALUES ('".$MsgID."','".$MsgStatus."','".$MsgBody."');";
            //$result=mysqli_query($conn, $sql);  
            exit;
        } else {
            //ERROR OCURRED
            $ErrorCode = $res['response-code'];
            $ErrorDec = $res['response-description'];
            //SEND EMAIL TO ADMIN WITH ERROR CODE
        }


    }elseif ($amount == '150') {
     /********GET CODE FROM DATABASE AS PER DAY******/

     $sql = "SELECT * FROM code WHERE day='$day' AND CodeStatus=0 AND Device=1 AND Period=168 LIMIT 1";
     $result = mysqli_query($conn, $sql);
     $num = mysqli_num_rows($result);
     if ($num == 0) {

         sendsms($AdmPhone, $AdmBody);
         exit;
     }
     $row = mysqli_fetch_row($result);
     $AccessCode = $row[1];

     $Device=1;
     $UpTime = "7days";

     $MsgBody = MsgBody($Fname, $AccessCode, $UpTime, $link, $support, $Device);
     $data = sendsms($phone, $MsgBody);
     $res = json_decode($data, true);

     $ReCode = $res['responses'][0]['response-code'];
     $ReDec = $res['responses'][0]['response-description'];
     $MsgID = $res['responses'][0]['messageid'];

     //UPDATE CODE TO DATABASE
     if ($ReCode == 200) {
         //SMS WELL SEND
         //update code AS TAKEN
         $MsgStatus = 1; //0 for not sent
         $start_time=$Time;
         $end_time = date("Y-m-d H:i:s", strtotime('+168 hours'));
         $sendtime=strtotime($end_time);//Unix timestamp.
         // $sql= "UPDATE code SET CodeStatus='1', AssignedTo=$phone, Amount=$amount WHERE Acode='$AccessCode'"; 
         $sql = "UPDATE code SET StartTime='$start_time', EndTime='$end_time', CodeStatus='1', AssignedTO='$phone', Amount='$amount', Fname='$Fname',Time='$Time' WHERE Acode='$AccessCode'";
         $result = mysqli_query($conn, $sql);

         //SCHEDULING SMS


         //SAVE SMS INFO TO SMS DATABASE
         //$sql = "INSERT INTO `sms`(`SmsId`, `SmsStatus`, `SmsBody`) VALUES ('".$MsgID."','".$MsgStatus."','".$MsgBody."');";
         //$result=mysqli_query($conn, $sql);  
         exit;
     } else {
         //ERROR OCURRED
         $ErrorCode = $res['response-code'];
         $ErrorDec = $res['response-description'];
         //SEND EMAIL TO ADMIN WITH ERROR CODE
     }

    } elseif($amount == '290'){
/********GET CODE FROM DATABASE AS PER DAY******/

$sql = "SELECT * FROM code WHERE day='$day' AND CodeStatus=0 AND Device=2 AND Period=168 LIMIT 1";
$result = mysqli_query($conn, $sql);
$num = mysqli_num_rows($result);
if ($num == 0) {

    sendsms($AdmPhone, $AdmBody);
    exit;
}
$row = mysqli_fetch_row($result);
$AccessCode = $row[1];

$Device=2;
$UpTime = "7days";

$MsgBody = MsgBody($Fname, $AccessCode, $UpTime, $link, $support, $Device);
$data = sendsms($phone, $MsgBody);
$res = json_decode($data, true);

$ReCode = $res['responses'][0]['response-code'];
$ReDec = $res['responses'][0]['response-description'];
$MsgID = $res['responses'][0]['messageid'];

//UPDATE CODE TO DATABASE
if ($ReCode == 200) {
    //SMS WELL SEND
    //update code AS TAKEN
    $MsgStatus = 1; //0 for not sent
    $start_time=$Time;
    $end_time = date("Y-m-d H:i:s", strtotime('+168 hours'));
    $sendtime=strtotime($end_time);//Unix timestamp.
    // $sql= "UPDATE code SET CodeStatus='1', AssignedTo=$phone, Amount=$amount WHERE Acode='$AccessCode'"; 
    $sql = "UPDATE code SET StartTime='$start_time', EndTime='$end_time', CodeStatus='1', AssignedTO='$phone', Amount='$amount', Fname='$Fname',Time='$Time' WHERE Acode='$AccessCode'";
    $result = mysqli_query($conn, $sql);

    //SCHEDULING SMS


    //SAVE SMS INFO TO SMS DATABASE
    //$sql = "INSERT INTO `sms`(`SmsId`, `SmsStatus`, `SmsBody`) VALUES ('".$MsgID."','".$MsgStatus."','".$MsgBody."');";
    //$result=mysqli_query($conn, $sql);  
    exit;
} else {
    //ERROR OCURRED
    $ErrorCode = $res['response-code'];
    $ErrorDec = $res['response-description'];
    //SEND EMAIL TO ADMIN WITH ERROR CODE
}
    }
    //ANY OTHER AMOUNT CODE
    else {
        //ANY OTHER AMOUNT
        $MsgBody = "Hi " . $Fname . ", It's Cloud Galaxy Tech, This is a confirmation message that we've received your payments" . "\n" .
          //  "Amount KSH :" . $amount . "" . "\n" .
          //  "MpesaID: " . $MpesaCode . "" . "\n" .
            "For Queries call/sms " . $support2 . "";
        $data = sendsms($phone, $MsgBody);
        $res = json_decode($data, true);

        $ReCode = $res['responses'][0]['response-code'];
        $ReDec = $res['responses'][0]['response-description'];
        $MsgID = $res['responses'][0]['messageid'];
        if (!$ReCode == 200) {
            //SEND MAIL TO ADMIN
        }
    }
} else {
    //IF TARNSACTION STATUS NOT WORKING
    $AdmBody = "Hi Admin, You have a failed transaction status. " . $day . "";
    sendsms($AdmPhone, $AdmBody);
    exit;
}
