<?php
    header("Content-Type: application/json");

   // $response = '{ "ResultCode": 0, "ResultDesc": "Confirmation Received Successfully" }';

   $mpesaResponse = file_get_contents('php://input');
   $logFile = "logs/StatusTimeout.log";
   $jsonMpesaResponse = json_decode($mpesaResponse, true); 

    $log = fopen($logFile, "a");
    fwrite($log, $mpesaResponse);
    fclose($log);

    echo $response;

?>
