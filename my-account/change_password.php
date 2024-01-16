<?php
include "header.php";
?>




<?php 

$user_id = $_SESSION['user_id'];

$query = mysqli_query($connect, "SELECT * FROM users WHERE id='$user_id'");
$row = mysqli_fetch_array($query);

?>

<div align="middle" style="width:100%;">
<div class="title" style="width:50%;">Change Password For (<?php echo $row['fname'].' '.$row['lname'];?>)</div>
<?php





if (isset($_POST['save_info'])) {

  $old_password = sha1($_POST['old_password']);
  $new_password = sha1($_POST['new_password']);

  if ($new_password==$row['password'])
  die('new password equal same old');
  
  if ($old_password!=$row['password'])
  die('old password not correct');
  
  $query = mysqli_query($connect, "UPDATE users SET
		password='" . $new_password . "' 
		WHERE id=" . $user_id );
		
	if($query)
	{
	  echo "<span style='color:green;'>changed password successfully</span>";
	}
}
?>




<?php
$user_id = $_SESSION['user_id'];
$query = mysqli_query($connect, "SELECT * FROM users WHERE id='$user_id'");
$row = mysqli_fetch_array($query);
?>
    
    
<form method="post">
    <table width="50%" class="tabll" >
    

                    
        <tr>
            <td>Old password</td>
            <td><input type="password" class="cp_input" name="old_password" value="" autocomplete="old-password" required/>
        </tr>
        <tr>
            <td>New Password</td>
            <td><input type="password" class="cp_input" name="new_password" value="" autocomplete="new-password" required/>

        </tr>


        <tr>
            <td colspan="2" align="middle"> 
                <input class="btn btn-b" type="submit" style="" name="save_info" value="Save"/>
            </td>
        </tr>
        
    </table>
</form>






