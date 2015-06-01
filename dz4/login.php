<?php
include_once 'funkcije.php'; 
session_start();

$secret_word = 'primjerZaZadcuIzRp2';
if(isset($_POST["username"]) && isset($_POST['password']) &&  verifyLogin())
	$_SESSION['login'] = $_POST['username'].','. md5( $_POST['username'].$secret_word);

unset( $username );
if( isset( $_SESSION['login'] ) ) {
	list( $c_username, $cookie_hash ) = explode( ',' , $_SESSION['login'] );
	if( md5( $c_username . $secret_word ) == $cookie_hash )
		$username = $c_username;
	else
		echo "Poslan je pokvareni kolacic";
}

//,,,,,,,,log out........
if( isset( $username ) && isset( $_POST['logout'] ) ) {
	session_unset();
	session_destroy();
	unset( $username );
}
if( isset($_POST['singin'] )) {
	if(addUserToDatabase())
	{
		echo "Pogledajte mail i dovršite registraciju klikom na link!";
	}
	else
	{
		echo "Molimo vas pokušajte opet";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset = "utf-8">
	<title>registracija</title>
</head>
<body>
<?php 
		if(isset($username)){
			echo "Dobro došli, $username.<br/>"; ?>
		<form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			<input type="hidden" name="logout">
			<input type="submit" value="Log Out">
		</form>
		<?php
		}
		else
		{ ?>
			<form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
				Username: <input type="text" name="username"> <br />
				Password: <input type="password" name="password"> <br />
				<input type="submit" value="Log In">
			</form>
			<form action="<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>" method="post">
				Username: <input type="text" name="username" id="user" /><br />
				E-mail: <input type="email" name="email" id ="mail"> <br/>
				Password: <input type="password" name="password" id="pass1" /><br />
				Password ponovno: <input type="password" id="pass2" /><br />
				<input type="hidden" name="singin">
			<input type="submit" value="Submit!" id="submit" disabled />
		</form>
	
		<script>
			// JavaScript koji enable-a Submit gumb akko su isti passwordi
			function checkPass()
			{
				var user = document.getElementById( 'user' ).value;
				var mail = document.getElementById( 'mail' ).value;
				var val1 = document.getElementById( 'pass1' ).value;
				var val2 = document.getElementById( 'pass2' ).value;

				if( user !== '' && mail != ''&&val1 !== '' && val1 === val2 )
					document.getElementById( 'submit' ).disabled = false;
				else
					document.getElementById( 'submit' ).disabled = true;
			}


			window.onload = function() {
				document.getElementById( 'pass1' ).addEventListener( 'input', checkPass );
				document.getElementById( 'pass2' ).addEventListener( 'input', checkPass );
			}		
		</script>
			
			<?php
		} ?>
</body>
</html>