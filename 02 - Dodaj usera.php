<?php
include_once '02 - Pomocne funkcije.php';
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf8"></head>
<body>
	<?php
	if( addUserToDatabase() ) { 
		// User je uspješno dodan u bazu. Zahvali mu na suradnji :)
		?>
		<p>
			Uspješno ste se registrirali.
		</p>
		<?php
	}
	else {
		// Iscrtaj formu za dodavanje novog usera.
		?>
		<form action="<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>" method="post">
			Username: <input type="text" name="username" id="user" /><br />
			Password: <input type="password" name="password" id="pass1" /><br />
			Password ponovno: <input type="password" id="pass2" /><br />
			<input type="submit" value="Submit!" id="submit" disabled />
		</form>
	
		<script>
			// JavaScript koji enable-a Submit gumb akko su isti passwordi
			function checkPass()
			{
				var user = document.getElementById( 'user' ).value;
				var val1 = document.getElementById( 'pass1' ).value;
				var val2 = document.getElementById( 'pass2' ).value;

				if( user !== '' && val1 !== '' && val1 === val2 )
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

