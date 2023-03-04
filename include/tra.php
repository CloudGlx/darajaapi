<?php
date_default_timezone_set("Africa/Nairobi");
$Time = date("H:i:s  Y-m-d ");
$start_time=$Time;
$end_time = date("Y-m-d H:i:s", strtotime('+6 hours'));
$Fname="stteve";
$phone="0745214555";
$amount=20;
	require_once 'conn.php';
	$day=1;
  $sql = "SELECT * FROM code WHERE day='$day' AND CodeStatus=0 AND Device=1 AND Period=6 LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($result);
        if ($num == 0) {
             echo "no code";
          }

        $row = mysqli_fetch_row($result);
          $AccessCode = $row[1];

        $sql = "UPDATE code SET StartTime='$start_time', EndTime='$end_time', CodeStatus='1', AssignedTO='$phone', Amount='$amount', Fname='$Fname',Time='$Time' WHERE Acode='$AccessCode'";
        $result = mysqli_query($conn, $sql);
?>
