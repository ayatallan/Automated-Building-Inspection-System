
<?php

session_start();
require "connect_mysql.php";

$return_data=array();

$loginname = mysqli_real_escape_string($connect, $_POST['loginname']);
$password = sha1($_POST['password']);


$sql="SELECT loginname,password,id FROM users WHERE loginname='$loginname' AND password='$password' ";
$finder = mysqli_query($connect, $sql);

if (mysqli_num_rows($finder) != 0) 
{
  while ($row = mysqli_fetch_object($finder)) 
  {
    $loginname = $row->loginname;
    $password = $row->password;
    $user_id = $row->id;

  }

  session_unset();

  $_SESSION['user_id'] = $user_id;
  $_SESSION['loginname'] = $loginname;
  $_SESSION['password'] = $password;


  $return_data["head"]="ok";
  $return_data["body"]="correct login data";
  echo json_encode($return_data);
  exit;




}
else
{
  $return_data["head"]="error";
  $return_data["body"]="loginname or password error";
  echo json_encode($return_data);
  exit;
}
		    

?>
