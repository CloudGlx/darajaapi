<?php
date_default_timezone_set("Africa/Nairobi");

$Time=date("H:i:s  Y-m-d ");
require_once 'conn.php';

$AccessCode=4150;
$phone='0716815356';
$amount='3000';
$Fname="steve mwas";

$finalnum=444;
$day=1;
$status=0;

$sql = "UPDATE code SET CodeStatus='1', AssignedTO='$phone', Amount='$amount',Fname='$Fname',Time='$Time' WHERE Acode='$AccessCode'";

 

if(!$result=mysqli_query($conn, $sql)){
    echo("Error description: " . mysqli_error($conn));
}
else{
    echo "good";
}