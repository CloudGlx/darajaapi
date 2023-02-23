<?php
header("Content-Type:application/json");
date_default_timezone_set("Africa/Nairobi");



//1 for Monday, 7 for Sunday
$day=date('N');


echo date("H:i:s  Y-m-d "); //Date and time