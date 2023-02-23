<?php
    header("Content-Type: application/json");
    require_once 'conn.php';
   // $response = '{ "ResultCode": 0, "ResultDesc": "Accepted" }';

$content = file_get_contents('json.json'); //Receives the JSON Result from safaricom
$res = json_decode($content, true); //Convert the json to an array


date_default_timezone_set("Africa/Nairobi");
//$day=date('N');
$day=7;
//GET CODE FROM DATABASE AS PER DAY
$FirstNum=$day;


$a = 1;
/*
	while($a<=9){
        $SecNum=$a;
        $ThirdNum=$FirstNum+$a;
        $third=$FirstNum.''.$SecNum.''.$ThirdNum;
        intval($third);
        //ADD ZERO
        $num=$third;
        $numlength = strlen((string)$num);
        if($numlength<=3){
            $finalnum=$num.'0';
        }
        else{
            $finalnum=$third;
        }
        intval($finalnum);
    	echo $finalnum."<br>";
        $status=0;
//INSERT TO DATABASE
//$sql = "INSERT INTO `code`(`Acode`, `day`, `CodeStatus`) VALUES ('".$finalnum."','".$day."','".$status."');";
//$result=mysqli_query($conn, $sql);  
      $a++;
    }
*/

	while($a<=5){
        $num3 = mt_rand(100,999);
        $final=$FirstNum.''.$num3;
        intval($final);
        //ADD ZERO
       // echo $final."<br>";
        $status=0;
        $device=3;
        $period=720;
//INSERT TO DATABASE
$sql = "INSERT INTO `code`(`Acode`, `day`, `Device`, `CodeStatus`, `Period`) VALUES ('".$final."','".$day."','".$device."','".$status."','".$period."');";
$result=mysqli_query($conn, $sql);  
      $a++;
    }


