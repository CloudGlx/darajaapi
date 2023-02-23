<?php
header("Content-Type:application/json");
date_default_timezone_set("Africa/Nairobi");


require_once 'conn.php';
//1 for Monday, 7 for Sunday
$day=date('N');

require_once ('SmsFunction.php');

/********GET CODE FROM DATABASE AS PER DAY******/

$sql="SELECT * FROM code WHERE day='$day' AND codeStatus=0 LIMIT 1";
$result=mysqli_query($conn, $sql);
$num=mysqli_num_rows($result);
if($num==0){
   $AdmBody="Hi Admin, You Dont have active Accesse codes";
   $AdmPhone="254716815356";
   sendsms($AdmPhone,$AdmBody);
   exit;
}
$row = mysqli_fetch_row($result);
$AccessCode=$row[1];

/***********GET PHONE NUMBER AS PER MPESA CODE*******/
//MPESA CODE
$TransID='QKO2LQ4Y3E';


$sql="SELECT MSISDN,FirstName FROM confirmation WHERE TransID='$TransID'";
$result=mysqli_query($conn, $sql);
$row = mysqli_fetch_row($result);
$phone=$row[0];
//cut name
$name=$row[1];
$ex = explode(" ", $name, 3);
$Fname=$ex[0];

//MSG BODY
$link="http://wifi.login/login";
$support="0716815356";

function MsgBody($Fname,$AccessCode,$UpTime,$link,$support){
    $MsgBody="Hi " .$Fname. ",Connect to CloudGalaxy Wifi to get started"."\n".
    "Access Code:".$AccessCode.""."\n".
    "Uptime:".$UpTime.""."\n".
    "LoginLink: ".$link.""."\n".
    "For help call ".$support."";

    return $MsgBody;
}

$MsgBody=MsgBody($Fname,$AccessCode,$UpTime,$link,$support);
$data=sendsms($phone,$MsgBody);

$res = json_decode($data, true);

$ReCode=$res['responses'][0]['response-code'];
$ReDec=$res['responses'][0]['response-description'];
$MsgID=$res['responses'][0]['messageid'];


//update code and save data
if($ReCode==200){
    $amount=30;
    //SMS WELL SEND
//update code AS TAKEN
    $MsgStatus=1;//0 for not sent
    $sql= "UPDATE code SET CodeStatus='1', AssignedTo=$phone, Amount=$amount WHERE Acode='$AccessCode'"; 
    $result=mysqli_query($conn, $sql);
//SAVE SMS INFO TO SMS DATABASE
 $sql = "INSERT INTO `sms`(`SmsId`, `SmsStatus`, `SmsBody`) VALUES ('".$MsgID."','".$MsgStatus."','".$MsgBody."');";
 $result=mysqli_query($conn, $sql);  
 //echo "good"; 
  
}
else{
 
    //ERROR OCURRED
    $ErrorCode=$res['response-code'];
    $ErrorDec=$res['response-description'];
}