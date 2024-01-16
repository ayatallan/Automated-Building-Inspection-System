<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
		Automated Building Inspection System
	</title>

	<link rel="icon" href="">


	<link rel='stylesheet' type='text/css' href="assets/css/style_home.css" rel="stylesheet">
	<link rel='stylesheet' type='text/css' href="assets/css/fontAwesome.css" rel="stylesheet">


	<script type="text/javascript" src="assets/js/jquery-2.2.4.min.js"></script>


</head>


<body >

	<?php

	if (empty($_GET['page_is']) || (isset($_GET['page_is']) && $_GET['page_is'] == 'login')) {

		include 'login.php';
	?>
		<div style="height:1px;"></div>
	<?php

	} else if (isset($_GET['page_is']) && $_GET['page_is'] == 'signup') {

		include 'signup.php';
	?>
		<div style="height:1px;"></div>
	<?php

	}

	?>

</body>

</html>