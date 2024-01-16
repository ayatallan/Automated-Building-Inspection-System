<style>
	body {
		margin: 0;
		padding: 0;

	}

	.login-section {
		background-color: #e8f0fe;
		display: flex;
		justify-content: center;
		align-items: center;
		height: 100vh;


	}

	.login-content {
		/* text-align: center; */
		max-width: 600px;
		width: 100%;
		padding: 20px;
		box-sizing: border-box;
		background-color: #5593CE;
		border-radius: 15px;
		margin: 20px;
		box-shadow: 1px 5px 18px 0px #3D8AC0;
		
	}

	.login-title {
		text-align: center;
		font-size: 1.7em;
		font-weight: bold;
		color: #fff;
		margin-bottom: 20px;
		text-shadow: 1px 0px 5px #fedb6d;
		
	}

	.login-form {
		background-color: #fff;
		border: 1px solid #fffb;
		border-radius: 15px;
		padding: 20px;
		box-sizing: border-box;

	}

	.login-form-table {
		width: 100%;
		max-width: 350px;
		margin: 0 auto;
		font-size: 1.1em;
		
	}

	.form-row {
		padding: 10px 0;
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

	.login-btn {
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

	.login-btn:hover {
		background-color: #3d8ac0;
	}

	.login-logo {
		width: 140px;
		margin: 20px 0;
	}

	.logo-and-link-container {
		display: flex;
		flex-direction: column;
		align-items: center;
	}

	.login-logo {
		width: 140px;
		margin-bottom: 10px;
	}

	.signup-link {
		color: #fad02c;
		font-size: 1.1em;
		text-decoration: none;
		text-shadow: 1px 1px 1px #fedb6d;
	}

	.signup-link {
		color: #fad02c;
		font-size: 1.1em;
		text-decoration: none;
		text-shadow: 1px 1px 1px #fedb6d;
	}

	@media only screen and (max-width: 600px) {
		.login-content {
			margin: 10px;
		}

		.login-form {
			max-width: 100%;
		}

		.login-form-table {
			max-width: 100%;
		}
	}
	.img{
		width: 500px;
		height:500px;
		z-index: 5;
	}
	a:hover{
text-shadow: 1px 1px 1px #3d8ac0;
	}
</style>
<script>
	function login(element) {

		var loginname = $("[id=loginname]").val();
		var password = $("[id=password]").val();

		//alert(4);
		if (loginname != "" && password != "") {
			$.ajax({
				type: "POST",
				url: "login_backend.php",
				data: {
					'loginname': loginname,
					'password': password
				},
				success: function (response) {

					//alert(response);
					response = JSON.parse(response.trim());

					if (response.head == "ok") {

						$("[id=loginname]").empty();
						$("[id=password]").empty();

						location = "my-account/index.php";


					} else if (response.head == "error") {
						alert(response.body);

					}
				}
			});


		}

	}
</script>
<section class="login-section">
	<div class="login-content">
		<div class="login-title">Automated Building Inspection System</div>

		<div class="login-form">
			<table class="login-form-table">
				<tr class="form-row">
					<td>
						<label for="loginname" style="color: #3D8AC0">Username</label><br>
						<input type="text" id="loginname" class="form-input" placeholder="Username"
							autocomplete="off" />
					</td>
				</tr>
				<tr class="form-row">
					<td>
						<label for="password" style="color: #3D8AC0">Password</label><br>
						<input type="password" id="password" class="form-input" placeholder="Password"
							autocomplete="off" />
					</td>
				</tr>
				<tr class="form-row">
					<td>
						<button id="login-btn" type="submit" class="login-btn" onclick="login(this)">Login</button>
					</td>
				</tr>
			</table>

			<div class="logo-and-link-container">
				<img src="assets/images/logo.png" alt="Logo" class="login-logo" />
				<a href="?page_is=signup" class="signup-link">Create new account</a>
			</div>


		</div>
	</div>
	<img class="img" src="./assets/images/bg-remove.png" alt="">

</section>