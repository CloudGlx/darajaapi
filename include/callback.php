<?php


//echo '<a href="index.php">Home<br /></a>';

$content = file_get_contents('php://input'); //Receives the JSON Result from safaricom
$res = json_decode($content, true); //Convert the json to an array

$dataToLog = array(
    date("Y-m-d H:i:s"), //Date and time
    " MerchantRequestID: ".$res['Body']['stkCallback']['MerchantRequestID'],
    " CheckoutRequestID: ".$res['Body']['stkCallback']['CheckoutRequestID'],
    " ResultCode: ".$res['Body']['stkCallback']['ResultCode'],
    " ResultDesc: ".$res['Body']['stkCallback']['ResultDesc'],
    //IF USER PAYS
    "Amount: ".$res['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'],
    "MpesaCode: ".$res['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'],
    "Balance: ".$res['Body']['stkCallback']['CallbackMetadata']['Item'][2]['Value'],
    "Date: ".$res['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'],
    "Phone: ".$res['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'],
    
);

$data = implode(" - ", $dataToLog);
$data .= PHP_EOL;
file_put_contents('stkpush.log', $data, FILE_APPEND); //Logs the results to our log file


//UPDATE DATA TO THE DATABASE
require_once 'conn.php';
$MerchantRequestID=$res['Body']['stkCallback']['MerchantRequestID'];
$CheckoutRequestID=$res['Body']['stkCallback']['CheckoutRequestID'];
$ResultCode=$res['Body']['stkCallback']['ResultCode'];
$ResultDesc=$res['Body']['stkCallback']['ResultDesc'];
$Amount=$res['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
$MpesaCode=$res['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
//$Balance=$res['Body']['stkCallback']['CallbackMetadata']['Item'][2]['Value'];
$TransactionDate=$res['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'];
//$PhoneNumber=$res['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];

if($ResultCode==0){
    $date= date("Y-m-d H:i:s");
    $sql="SELECT * FROM requst WHERE CheckoutRequestID='$CheckoutRequestID'";
    $result=mysqli_query($conn, $sql);

    if(mysqli_num_rows($result)==1){

        $sql= "UPDATE requst SET ResultDesc='$ResultDesc', MpesaCode='$MpesaCode', TransactionDate='$TransactionDate', status = 'SUCCESS' WHERE CheckoutRequestID='$CheckoutRequestID'";
        if(!$re=mysqli_query($conn,$sql)){
            $error=mysqli_error($conn);
            $data=$date. ' This Error occured For CheckId: '.$MerchantRequestID.'Error: ' .$error;
            file_put_contents('error_log',PHP_EOL. $data, FILE_APPEND);
        }
        else{
            $data=$date. ' Record Inserted for CheckoutID:'.$MerchantRequestID;
            file_put_contents('error_log',PHP_EOL.$data, FILE_APPEND);
        }
     }

}else{
    $sql="SELECT * FROM requst WHERE CheckoutRequestID='$CheckoutRequestID'";
    $result=mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result)==1){

        $sql= "UPDATE requst SET ResultDesc='$ResultDesc', MpesaCode='$MpesaCode', TransactionDate='$TransactionDate', status = 'error' WHERE CheckoutRequestID='$CheckoutRequestID'";
        if(!$re=mysqli_query($conn,$sql)){
            $error=mysqli_error($conn);
            $data=$date. ' This Error occured For CheckId: '.$MerchantRequestID.'Error: ' .$error;
            file_put_contents('error_log',PHP_EOL. $data, FILE_APPEND);
        }
        else{
            $data=$date. ' Record Inserted for CheckoutID:'.$MerchantRequestID;
            file_put_contents('error_log',PHP_EOL.$data, FILE_APPEND);
        }

     }
}

/*
$conn=new PDO("mysql:host=localhost;dbname=mpesa","root","");
$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$stmt = $conn->query("SELECT * FROM orders ORDER BY ID DESC LIMIT 1");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $row){
    $ID = $row['ID'];

    if($res['Body']['stkCallback']['ResultCode'] == '1032'){
        $sql = $conn->query("UPDATE `orders` SET `Status` = 'CANCELLED' WHERE `orders`.`ID` = $ID");
        $rs = $sql->execute();
     }else{
        $sql = $conn->query("UPDATE `orders` SET `Status` = 'SUCCESS' WHERE `orders`.`ID` = $ID");
        $rs = $sql->execute();
     }

    if($rs){
        file_put_contents('error_log', "Records Inserted", FILE_APPEND);;
    }else{
        file_put_contents('error_log', "Failed to insert Records", FILE_APPEND);
    }
}
*/
?>