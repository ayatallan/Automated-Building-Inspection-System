<?php

error_reporting(0);

$hosting = "localhost";
$database_name = "building_inspection_system";
$username = "root";
$password=""; 


$connect = new mysqli($hosting, $username, $password, $database_name);

$connect->set_charset('utf8');

$connect->query("SET collation_connection = utf8_general_ci");
if ($connect->connect_error) {

    die("Connect Error" . $connect->connect_error);
    
}


?>

