<style>
       body {
    margin: 0;
    padding: 0;
    box-sizing: border-box; /* Ensure box sizing is applied globally */
}

/* General container styles */
.signup-section {
    background-color: #e8f0fe;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.signup-content {
    max-width: 600px;
    width: 100%;
    padding: 20px;
    box-sizing: border-box;
    background-color: #5593CE;
    border-radius: 15px;
    margin: 20px;
    box-shadow: 1px 5px 18px 0px #3D8AC0;
}

/* Form styles */
.signup-title {
    text-align: center;
    font-size: 1.7em;
    font-weight: bold;
    color: #fff;
    margin-bottom: 20px;
    text-shadow: 1px 0px 5px #fedb6d;
}

.signup-form {
    background-color: #fff;
    border: 1px solid #fffb;
    border-radius: 15px;
    padding: 20px;
    box-sizing: border-box;
}

.signup-form-table {
    width: 100%;
    max-width: 350px;
    margin: 0 auto;
    font-size: 1.1em;
}

.form-input {
    width: 100%;
    height: 33px;
    padding: 5px;
    margin-bottom: 10px;
    border: 1px solid #82b0db;
    border-radius: 5px;
    box-sizing: border-box;
}

.signup-btn {
    background-color: #52A5C9;
    color: #fff;
    border: 0;
    border-radius: 5px;
    font-weight: bold;
    height: 33px;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
}

.signup-btn:hover {
    background-color: #3d8ac0;
}

/* Logo and link styles */
.signup-logo {
    width: 140px;
    margin: 20px 0;
}

.logo-and-link-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.signup-logo {
    width: 140px;
    margin-bottom: 10px;
}

.login-link {
    color: #fad02c;
    font-size: 1.1em;
    text-decoration: none;
    text-shadow: 1px 1px 1px #fedb6d;
}

/* Responsive styles */
@media only screen and (max-width: 600px) {
    .signup-content {
        margin: 10px;
    }

    .signup-form {
        max-width: 100%;
    }

    .signup-form-table {
        max-width: 100%;
    }
}

/* Image and link hover styles */
.img {
    width: 500px;
    height: 500px;
    z-index: 5;
}

a:hover {
    text-shadow: 1px 1px 1px #3d8ac0;
}
.signup-btn {
    background-color: #52A5C9;
    color: #fff;
    border: 0;
    border-radius: 5px;
    font-weight: bold;
    height: 40px; /* Adjusted height */
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
}

.signup-btn:hover {
    background-color: #3d8ac0;
}



    </style>

<script>

	function signup(element) {



		var fname = $("[name=fname]").val();
		var lname = $("[name=lname]").val();
		var loginname = $("[name=loginname]").val();
		var password = $("[name=password]").val();
		var repassword = $("[name=repassword]").val();




		if (fname == "") {
			alert("enter first name"); return;
		}
		if (lname == "") {
			alert("enter last name"); return;
		}
		if (loginname == "") {
			alert("enter correct phone number"); return;
		}
		if (password == "") {
			alert("enter password "); return;
		}
		if (password != repassword) {
			alert("password not equal "); return;
		}

		$.ajax({
			type: "POST",
			url: './signup_backend.php',
			data: { "fname": fname, "lname": lname, "loginname": loginname, "password": password, "repassword": repassword },
			success: function (response) {

				//alert(response.trim());

				response = JSON.parse(response.trim());

				if (response.head == "ok") {


					$("#show_response").css({ "color": "green", });
					$("#show_response").html(response.body);

					$("[name=fname]").val('');
					$("[name=lname]").val('');
					$("[name=loginname]").val('');
					$("[name=password]").val('');
					$("[name=repassword]").val('');

				}
				else {
					$("#show_response").css({ "color": "red", });
					$("#show_response").html(response.body);


				}


				//location.reload();
			}
		});
	}

</script>
<section class="signup-section">
	<div class="signup-content">
        <div class="in-page">
            <div class="content">
                <div class="section-inner" align="middle">
                    <br>
                    <div class="signup-container">
                        <span class="signup-title">Signup</span>
                        <br>
                        <div class="signup-form-container">
                            <div class="signup-form">
                                <table class="signup-account-table">
                                    <tr>
                                        <td colspan="4" align="middle">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="middle">First name * <br><input type="text" class="form-input" name="fname" autocomplete="off" /></td>
                                    </tr>
                                    <tr>
                                        <td align="middle">Last name * <br><input type="text" class="form-input" name="lname" autocomplete="off" /></td>
                                    </tr>
                                    <tr>
                                        <td align="middle">Login name *<br><input dir="ltr" style="text-align:center;" type="text" class="form-input" name="loginname" autocomplete="off" /></td>
                                    </tr>
                                    <tr>
                                        <td align="middle">Password *<br><input type="password" class="form-input" name="password" autocomplete="off" /></td>
                                    </tr>
                                    <tr>
                                        <td align="middle">Re-password *<br><input type="password" class="form-input" name="repassword" autocomplete="off" /></td>
                                    </tr>
                                    <tr>
                                        <td align="middle">
                                            <br><button class=" signup-btn " onclick="signup(this)">Submit</button>
                                            <div id="show_response" class="response-container"></div>
                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td >
                                            <br>
                                            <a href="?page_is=login" class="back-to-login">Back to Login</a>
                                            <br><br>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		</div>
		<img class="img" src="./assets/images/bg-remove.png" alt="">

    </section>