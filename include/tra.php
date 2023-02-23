<?php
		header("Content-Type: application/json");

	

		// DATA
		$mpesaResponse = file_get_contents('php://input');

		// log the response
		$logFile = "tra.log";

		// write to file
		$log = fopen($logFile, "a");

		fwrite($log, $mpesaResponse);
		fclose($log);

		echo $response;
?>
