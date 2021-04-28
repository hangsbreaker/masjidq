<?php
include "jimat.php";
$jim = new Jimat();
if (isset($_SESSION['username'])) {
	header('location:home.php');
	exit;
}
if (isset($_POST['p'], $_POST['l'])) {
	if ($_POST['l'] === $_SESSION['token']) {
		if ($_POST['p'] === 'r') {
			$usr = $jim->_sqli($c, $_POST['u'], 70);
			$email = $jim->_sqli($c, $_POST['eml'], 100);
			$pas1 = $jim->_sqli($c, $_POST['pas1'], 40);
			$pas2 = $jim->_sqli($c, $_POST['pas2'], 40);
			if ($pas1 == $pas2 && $jim->ismail($email)) {
				$pas = $jim->genpas($pas1);
				$q = mysqli_query($c, "insert into user (username, email, password) values ('$usr', '$email', '$pas')");
				if ($q) {
					echo TRUE;
				} else {
					echo FALSE;
				}
			} else {
				echo FALSE;
			}
		} else if ($_POST['p'] === 'l') {
			$usr = $jim->_sqli($c, $_POST['u'], 70);
			$pas = $jim->_sqli($c, $_POST['pas'], 40);
			$pas = $jim->genpas($pas);
			$q = mysqli_query($c, "select * from user where username='$usr' and password='$pas'");
			if (mysqli_num_rows($q) == 1) {
				$_SESSION['username'] = $usr;
				echo TRUE;
			} else {
				echo FALSE;
			}
		}
	}

	exit;
}


$_SESSION['token'] = $jim->token();
?>
<html lang="id">

<head>
	<title>Login</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="initial-scale=1.0, user-scalable=yes, width=device-width" />
	<meta name="Description" content="MasjidQ">
	<link rel="shortcut icon" type="image/png" href="markmosq.png" />
	<style>
		.warpform {
			position: relative;
			margin: 100px auto;
			width: 250px;
		}

		.warpform input {
			width: 100%;
			padding: 5px 7px;
		}

		.warpform button {
			margin-top: 10px;
			float: right;
			padding: 5px 10px;
		}

		#regform {
			display: none;
		}

		#warning,
		#warningr {
			color: red;
			text-align: center;
			margin-bottom: 10px;
		}
	</style>
</head>

<body>

	<div id="lgnform" class="warpform">
		<div id="warning"></div>
		<form method="post" action="">
			Username<br>
			<input type="text" name="username" id="username" /><br>
			Password<br>
			<input type="password" name="password" id="password" /><br>
			<button type="button" onclick="gologin()">Login</button>
		</form><br>
		Daftar <a href="javascript:register();">Disini</a>
	</div>

	<div id="regform" class="warpform">
		<div id="warningr"></div>
		<form method="post" action="">
			Username<br>
			<input type="text" name="username1" id="username1" /><br>
			Email<br>
			<input type="email" name="email" id="email" /><br>
			Password<br>
			<input type="password" name="password1" id="password1" /><br>
			Ulangi Password<br>
			<input type="password" name="password2" id="password2" /><br>
			<button type="button" onclick="godaftar()">Daftar</button>
		</form><br>
		Login <a href="javascript:login();">Disini</a>
	</div>

	<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>
	<script>
		function register() {
			$('#lgnform').css('display', 'none');
			$('#regform').css('display', 'block');
		}

		function login() {
			$('#lgnform').css('display', 'block');
			$('#regform').css('display', 'none');
		}

		function gologin() {
			var usr = $('#username').val();
			var pas = $('#password').val();
			$.post(window.location.pathname, {
				u: usr,
				pas: pas,
				l: '<?php echo $_SESSION["token"]; ?>',
				p: 'l'
			}, function(dt) {
				if (dt) {
					window.location = window.location.pathname;
				} else {
					$('#warning').html('Username / Password Salah');
				}
			});
		}

		function godaftar() {
			var usr = $('#username1').val();
			var eml = $('#email').val();
			var pas1 = $('#password1').val();
			var pas2 = $('#password2').val();
			if (pas1 === pas2 && vmail(eml)) {
				$.post(window.location.pathname, {
					u: usr,
					eml: eml,
					pas1: pas1,
					pas2: pas2,
					l: '<?php echo $_SESSION["token"]; ?>',
					p: 'r'
				}, function(dt) {
					console.log(dt);
					if (dt) {
						window.location = window.location.pathname;
					} else {
						$('#warningr').html('Username sudah ada');
					}
				});
			} else {
				$('#warningr').html('Password / Email<br>Tidak valid');
			}
		}

		function vmail(mail) {
			if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
				return (true)
			}
			return (false)
		}
	</script>
</body>

</html>