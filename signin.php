<?php
include_once "funkcije.php";

session_start();

if (isset($_SESSION["admin"]))
	$admin = $_SESSION["admin"];
else
	$admin = "0";
?>

<!DOCTYPE html>
<html lang="hr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Dodavanje korisnika</title>
	<link rel="stylesheet" type="text/css" href="css/stil.css" />
	<link rel="stylesheet" type="text/css" href="css/login_form_style.css" />
	<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
	<link rel="shortcut icon" href="https://www.math.pmf.unizg.hr/misc/favicon.ico" type="image/vnd.microsoft.icon" />
	<script type="text/javascript" src="jsDatePick.min.1.3.js"></script>
	<script type="text/javascript" src="jquery-2.1.4.min.js"></script>
</head>

<body>
	<?php
	if ($admin == "1") {
		?>
		<div class="content">
		<div>
			<span id="natpis"></span>
			<span id="odjava"><input type="button" class="myButton" id="povratak" value="Natrag" /></span>
		</div>
		<h1 align="center">Dodavanje korisnika</h1>
		<?php
		if( addUserToDatabase() ) { 
		?>
			<p>
				Uspješno ste registrirali korisnika.
			</p>
		<?php
			}
		else {
		?>
		
			<form class="login" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
				<h3>Podatci za registraciju</h3>
				<input id="ime" class="login-input" type="text" name="name" placeholder="Ime" autofocus />
				<input id="prezime" class="login-input" type="text" name="lastname" placeholder="Prezime" />
				<input id="user" class="login-input" type="text" name="username" placeholder="Korisničko ime" />
				<input id="pass1" class="login-input" type="password" name="password" placeholder="Lozinka" />
				<input id="pass2" class="login-input" type="password" name="password" placeholder="Ponovljena lozinka" />
				Hoće li biti admin?
				<input type="radio" name="p_admin" value="da" />Da
				<input type="radio" name="p_admin" value="ne" checked />Ne
				<br /><br />
				<input class="login-submit" id="submit" type="submit" value="Dodaj!" disabled />
			</form>
		</div>
		<div class="footer">
			J. Milašinović, M. Pavlović, I. Posavčević<br />
			<a href="http://web.math.pmf.unizg.hr/nastava/rp2d/">RP2</a>, <a href="https://www.math.pmf.unizg.hr/">PMF-MO</a>, 2015.
		</div>
		
		<script>
			function checkPass() {
				var user = document.getElementById("user").value;
				var ime = document.getElementById("ime").value;
				var prezime = document.getElementById("prezime").value;
				var pass1 = document.getElementById("pass1").value;
				var pass2 = document.getElementById("pass2").value;

				if (ime !== "" &&prezime!== "" && user !== "" && pass1 !== "" && pass1 === pass2)
					document.getElementById("submit").disabled = false;
				else
					document.getElementById("submit").disabled = true;
			}

			window.onload = function() {
				document.getElementById("pass1").addEventListener("input", checkPass);
				document.getElementById("pass2").addEventListener("input", checkPass);
			}
		</script>
		<?php } ?>
		<script>
			document.getElementById("natpis").innerHTML = <?php echo json_encode($_SESSION["tko"]); ?>;
			document.getElementById("povratak").onclick = function() {
			window.location.href = "http://192.168.89.245/~iposavc/projekt/login.php";
		} 
		</script>
		<?php
	} ?>
</body>
</html>
