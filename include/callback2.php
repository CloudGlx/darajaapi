<?php
//if(!isset($_POST['confirm_payment'])){
//header('Location:../');
//exit;
//}
//CONTINUE
session_start();
require_once 'conn.php';

$_SESSION['CheckoutRequestID']='ws_CO_20102022142453014740906173';

if(isset($_SESSION['CheckoutRequestID'])){
  

$CheckoutRequestID=$_SESSION['CheckoutRequestID'];
$sql="SELECT * FROM requst WHERE CheckoutRequestID='$CheckoutRequestID'";
$result=mysqli_query($conn, $sql);

$row=mysqli_fetch_array($result);
$MpesaCode= $row['MpesaCode'];
echo $MpesaCode;
}




else{
    echo 'SEESION NOT SET';
}

?>