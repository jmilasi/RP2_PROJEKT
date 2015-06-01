<?php
include_once '02 - Pomocne funkcije.php';
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf8"></head>
<body>
	<?php
	if( verifyLogin() ) { 
		// User se uspješno ulogirao. Čestitaj mu :)
		?>
		<p>
			Uspješno ste se ulogirali.
		</p>
		<?php
	}
	else {
		// Iscrtaj formu za ulogiravanje.
		?>
		<form action="<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>" method="post">
			Username: <input type="text" name="username" /><br />
			Password: <input type="password" name="password" /><br />
			<input type="submit" value="Submit!" />
		</form>
		<?php
	} ?>
</body>
</html>		

