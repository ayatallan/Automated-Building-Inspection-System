<?php
include "header.php";
?>



<style>
    /* .welcome-container {
        text-align: center;
        padding: 20px;
        background-color: #5292CCcc;
        border-radius: 10px;
        width: 70%;
        margin: auto;
        border: 2px solid #5292CC44;
        background-image: url('../assets/images/bg-right.jpg') !important;
        background-size: cover;
        min-height: 60vh;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
    } */

    .hand-icon {
        font-size: 2.3em;
        color: #FAD02C;
        text-shadow: 0px 1px 3px #000;
        margin: 3px;
    }

    .person-name {
        font-size: 1.7em;
        font-weight: bold;
        color: #5292CC;
        margin: 9px;
        margin-left: 22px;
    }

    .wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .welcome-container {
        margin-top: 5rem;
        display: flex;
        justify-content: space-evenly;
        align-items: center;
        height: 40vh;
        width: 70%;
        background-color: #5593CE;
        padding: 2rem;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
        border-radius: 20px;
        /* Set your background color */
    }

    .content-container {
        display: flex;
        align-items: center;

    }

    .message-container {
        text-align: left;
        padding: 20px;
        width: 100%;
    }

    .photo-container {
        margin-left: 20px;
        width: 40%;

    }

    .message {
        font-size: 1.4em;
        color: #fff;
        margin-top: 15px;
        text-shadow: 1px 1px 3px #444;
    }

    .btn.btn-o {
        display: inline-block;
        padding: 10px 20px;
        font-size: 1.2em;
        color: #fff;
        text-decoration: none;
        background-color: #FAD02C;
        border-radius: 5px;
        margin-top: 20px;
        transition: background-color 0.3s ease-in-out;
    }

    .btn.btn-o:hover {
        background-color: #FEDB6D;
    }
</style>



<span class="person-name">Hello <font style="text-transform: uppercase;
color:#FAD02C;text-shadow:1px 1px 3px #000;">
        <?php echo $row['fname'] . ' ' . $row['lname']; ?>
    </font></span>
<i class="fa fa-hand-peace-o" style="font-size:2.3em;color:#FAD02C;text-shadow:0px 1px 3px #000;margin:3px;"></i>

<br>
<br>
<div class="wrapper">

    <div class="welcome-container">
        <div class="content-container">
            <div class="message-container">
                <p class="message">Welcome in to <font
                        style="color:#FAD02C;text-shadow:1px 1px 3px #444;font-size:1.2em;">
                        Inspection System App.</font> You can continue to get features</p>
                <a href="main.php" class="btn btn-o" style="text-decoration:none;font-size:1.2em;color:#fff"> Click To
                    Start</a>
            </div>
            <div class="photo-container">
                <img src="../assets/images/bg-remove.png" alt="">
            </div>
        </div>
    </div>
</div>