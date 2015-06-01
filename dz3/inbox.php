<?php
// još srediti samo čitanje poruke! 
session_start();
//.....provjera pasworda
function validate(){
		if( ctype_alnum( $_POST["username"] ) )
		{
			$user = $_POST["username"];
			$f = fopen("password.txt", 'r') or die("Ne mogu otvorizi file");

			while($userinfo = fgets($f)){
				$temp = explode("\t",$userinfo);
				if($temp[0] == $user)
				{	
					$pass= explode("\n",$temp[1]);
					if(  password_verify($_POST["password"] ,$pass[0]) ) 
					{
						fclose($f) or die("Ne mogu zatvoriti file");
						return true;
					}
				}
			}
			fclose($f) or die("Ne mogu zatvoriti file");
		}

		return false;
	}
	//...................DODAVANJE KORISNIKA.................
if(isset( $_POST['signin'] ) ) {

	if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) )
		unset($username);
	else
	{
		if( ctype_alnum( $_POST['username'] ) )
		{
			$postoji = false;
			$f = fopen("password.txt", 'a') or die("Ne mogu otvoriti file");
			while($userinfo = fscanf($f, "%s\t%s")){
				list($tmp_user,$temp_pass) = $userinfo;
				if($tmp_user === $_POST['username'])
					$postoji = true;
					break;
			}
			
			if($postoji === false)
			{
				$username = $_POST['username'];
				$hashed_password = password_hash( $_POST['password'], PASSWORD_DEFAULT );
				fwrite($f, $username ."\t".$hashed_password."\n" );
			}
			fclose($f) or die("Ne mogu zatvoriti file");
		}
		else {
		echo 'Korisničko ime smije imati samo slova i znamenke!<br />';
		unset($username);
		}
	}
}

//................dodavanje kolacica.......

$secret_word = 'primjerZaZadcuIzRp2';
if(isset($_POST["username"]) && isset($_POST['password']) && validate())
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
//,....postoji li korisnik
function postoji($kome){
	$f = fopen("password.txt", 'r') or die("Ne mogu otvoriti file");
			while($userinfo = fscanf($f, "%s\t%s")){
				list($tmp_user,$temp_pass) = $userinfo;
				if($tmp_user === $_POST["name"])
					fclose($f) or die("Ne mogu zatvoriti file");
					return true;
			}	
			fclose($f) or die("Ne mogu zatvoriti file");
			return false;
}
//.....posalji....
if(isset( $_POST['posalji_poruku'] ) ) {
	$kome = $_POST['name'];
	$poruka = $_POST['body'] ."\n";
	if(postoji($kome))
	{
		$f = fopen("$kome.txt", 'a') or die("Ne mogu otvoriti file");
		fwrite($f, $poruka);
		fclose($f) or die("Ne mogu zatvoriti file");
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset = "utf-8">
	<title>INBOX</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>
<body>
<?php 
		if(isset($username)){
			echo "Dobro došli, $username.<br/>"; ?>
		<form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			<input type="hidden" name="logout">
			<input type="submit" value="Log Out">
		</form>
		<input type = "button" value = "Pročitaj poruku" id = "poruka">
		<ol id = "box"></ol>

		<form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			Kome: 
			<br/><input type="Text" name="name" size="30" >
			<br/>
			<br/>Upiši poruku:
			<br/><textarea name="body" cols="30" rows="10"></textarea>
			<input type="hidden" name="posalji_poruku">
			<input type="submit" value="Pošalji">
		</form>


		<script type="text/javascript">
		
		document.getElementById("poruka").onclick = function(e){
			<?php
					$user = $_POST["username"];
					$f = fopen("$user.txt", 'r') or die("Ne mogu otvoriti file");
					$prva = true;
					$string = "";
					$poruka = "";
					while($userinfo = fgetc($f))
					{
						if($prva = true)
						{
							$poruka = $userinfo;
							$prva =false;
						}			
						else{
							$string .=$userinfo;
						}		
					}
					#fwrite($f, $string);
					fclose($f) or die("Ne mogu zatvoriti file");
			?>
				var item1 = "<li><?php echo $poruka ?></li>";
				$("#box").append( item1);
			};
		</script>
		<?php
		}
		else
		{ ?>

			<input type = "button" value="Log In" id = "LIN">
			<input type = "button" value="Sign In" id = "SIN">
			<div id = "form"></div>
			<script type="text/javascript">


			window.onload = function() {
				document.getElementById("LIN").onclick = function(e){
					document.getElementById("form").innerHTML = "<form method=\"POST\" action=\"<?php echo htmlentities($_SERVER['PHP_SELF']); ?>\">Username: <input type=\"text\" name=\"username\"> <br />Password: <input type=\"password\" name=\"password\"> <br /><input type=\"submit\" value=\"Log In\"></form>";
					document.getElementById("LIN").type ="hidden";
					document.getElementById("SIN").type ="hidden";
				};
				document.getElementById("SIN").onclick = function(e){
					document.getElementById("form").innerHTML = "<form action=\"<?php echo htmlentities( $_SERVER['PHP_SELF'] ); ?>\" method=\"post\">Username: <input type=\"text\" name=\"username\" id=\"user\" /><br />Password: <input type=\"password\" name=\"password\" id=\"pass1\" /><br />Password ponovno: <input type=\"password\" id=\"pass2\" /><br /><input type=\"hidden\" name=\"signin\"><br/><input type=\"submit\" value=\"Submit!\" id=\"submit\" disabled /></form>";
					document.getElementById("LIN").type ="hidden";
					document.getElementById("SIN").type ="hidden";
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
					document.getElementById( 'pass1' ).addEventListener( 'input', checkPass );
					document.getElementById( 'pass2' ).addEventListener( 'input', checkPass );
				};
				
				}		
			</script>
			<?php
		} ?>
</body>
</html>