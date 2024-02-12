<?php

error_reporting(0);

$hosting = "localhost";
$database_name = "building_inspection_system";
$username = "root";
$password=""; 


// $host = 'ls-c518f8b51062a200e07267aba15aa47cef9f0c81.c1ui2eio8yhz.us-west-2.rds.amazonaws.com';
// $database_name = 'Database-1'; 
// $username = 'inspection';
// $password = 'ayatallan123';

$connect = new mysqli($host, $username, $password, $database_name);

$connect = new mysqli($hosting, $username, $password, $database_name);

$connect->set_charset('utf8');

$connect->query("SET collation_connection = utf8_general_ci");
if ($connect->connect_error) {

    die("Connect Error" . $connect->connect_error);
    
}
?>
