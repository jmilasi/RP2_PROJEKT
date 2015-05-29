<?php
include_once 'funkcije.php';
include_once 'podaci.php';

session_start();

if( verifyLogin() )
	$_SESSION['login'] = $_POST['username'] . ',' . md5( $_POST['username'] . $secret_word );

unset( $username );
if( isset( $_SESSION['login'] ) ) {
	list( $c_username, $cookie_hash ) = explode( ',' , $_SESSION['login'] );
	if( md5( $c_username . $secret_word ) == $cookie_hash )
		$username = $c_username;
	else
		echo "Poslan je pokvareni kolačić!" ;
}

if( isset( $username ) && isset( $_POST['logout'] ) ) {
	session_unset();
	session_destroy();
	unset( $username );
}
?>

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
		<title>Rezervacija</title>
		<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php

	if( isset( $username ) ) {

		echo "Dobro došli, $username.<br />"; ?>
		<form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			<input type="hidden" name="logout">
			<input type="submit" value="Log Out">
		</form>
		 <p> Ovdje će se prikazati tablica:</p>
		<?php
	} 
	else {
		// Ako nije ulogiran, ispiši mu formu za logiranje. ?>
	<div class='info'>
    <h1>Registracija korisnika</h1>
	</div>
		<div class='form aniamted bounceIn'>
 		 <div class='login'>
 		   <h2 style="color:red;">Krivo unesena šifra ili lozinka:</h2>
 		   <form method = 'post' action = "akcija.php">
 		     	<input placeholder = 'Korisničko ime' type = 'text', name = "username">
		      	<input placeholder = 'Lozinka' type = "password", name = "password">
      			<button type="submit">Login</button>
		    </form>
		    <button id = "pogledaj" >Pogledaj raspored</button>
 		 </div>
		</div>
		<?php
	}
?>
<script>
	var pogledaj = document.getElementById("pogledaj");
	pogledaj.onclick = function(){
		window.location.href="http://localhost/~jmilasi/projekt/index2.html";
	}
</script>
</body>
</html>


