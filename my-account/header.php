<?php

session_start();

include '../connect_mysql.php';

if (!isset($_SESSION['user_id'])) {
    die("you do not have permission");
}

if ($_GET['action'] == "signout") {
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit;
}

?>
<html>

<head>
    <meta />
    <title>Automated Building Inspection System</title>
    <link rel='stylesheet' type='text/css' href='../assets/css/css.css' />
    <link rel='stylesheet' type='text/css' href="../assets/css/fontAwesome.css" rel="stylesheet">
    <script src="../assets/js/jquery-2.2.4.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #fff !important;
            font-family: 'Ubuntu', sans-serif;
            font-weight: bold;

        }
    </style>
</head>


<body>

    <?php

    $user_id = $_SESSION['user_id'];
    $query = mysqli_query($connect, "SELECT * FROM users WHERE id='$user_id'");
    $row = mysqli_fetch_array($query);

    ?>
    <div
        style="display: flex; align-items: center; justify-content: space-between; background:#fff2cd !important; padding:22px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">

        <!-- First Div: Logo and Project Name -->
        <div style="display: flex; align-items: center;">

            <div style="">
                <img src="../assets/images/logo.png" width="55px" />
            </div>
            <div style="margin-left: 10px;">
                <a href="index.php" style="text-decoration:none;color:#5292CC;font-size:24px;text-shadow:1px 0px 1px #7d9bcc; ">
                    Automated Building Inspection System
                </a>
            </div>
        </div>

        <!-- Second Div: User Information, Change Password, and Logout -->
        <div style="display: flex; align-items: center;">

            <div style="">
                <a style="text-decoration:none;">
                    <i class="fa fa-user-circle" style="font-size:2.0em;color:#5292CC; "></i>
                </a>
            </div>
            <div style="margin-left: 10px;">
                <a style="text-decoration:none;color:#5292CC;">
                    <?php echo $row['fname'] . ' ' . $row['lname']; ?>
                </a>
            </div>
            <div style="margin-left: 10px;">
                <a href="change_password.php" style="text-decoration:none;color:#555;">
                    Change Password
                </a>
            </div>
            <div style="margin-left: 10px;">
                <a href='?action=signout' style="text-decoration:none;color:#855;">
                    Logout
                </a>
            </div>
        </div>
    </div>



    <br><br>