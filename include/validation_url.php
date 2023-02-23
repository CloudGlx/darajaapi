<?php
    header("Content-Type: application/json");

    $response = '{ "ResultCode": 0, "ResultDesc": "Accepted" }';

$content = file_get_contents('php://input'); //Receives the JSON Result from safaricom
$res = json_decode($content, true); //Convert the json to an array


$dataToLog = array(
    date("Y-m-d H:i:s"), //Date and time
    " TransactionType: ".$res['TransactionType'],
    " TransID: ".$res['TransID'],
    " TransAmount: ".$res['TransAmount'],
    " FirstName: ".$res['FirstName'],
    //IF USER PAYS
    "MiddleName: ".$res['MiddleName'],
    "LastName: ".$res['LastName'],
    
    
);
$data = implode(" - ", $dataToLog);
$data .= PHP_EOL;
file_put_contents('validation.log', $data, FILE_APPEND); //Logs the results to our log file
echo $response;

?>

