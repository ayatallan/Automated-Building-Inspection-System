
<?php

require "connect_mysql.php";

$return_data=array();

$fname = mysqli_real_escape_string($connect, $_POST['fname']);
$lname = mysqli_real_escape_string($connect, $_POST['lname']);
$loginname = mysqli_real_escape_string($connect, $_POST['loginname']);

$password = sha1($_POST['password']);

$sql = mysqli_query($connect, "SELECT loginname FROM users WHERE loginname='$loginname'");
$num = mysqli_num_rows($sql);


if ($num > 0) 
{
  $return_data["head"]="error";
	$return_data["body"]="this username used";
	echo json_encode($return_data);
	exit();
} 


$sql="INSERT INTO users(fname,lname,loginname,password) VALUES ('$fname','$lname','$loginname','$password')";	
$query = mysqli_query($connect, $sql);


if(!$query)
{
  $return_data["head"]="error";
	$return_data["body"]="error when insert";
	echo json_encode($return_data);
	exit();
}

$return_data["head"]="ok";
$return_data["body"]="created";
echo json_encode($return_data);
exit();








