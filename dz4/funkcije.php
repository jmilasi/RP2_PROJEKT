<?php

include_once 'podaci.php';

class DB
{
	private static $db;

	final private function __construct() { }
 	final private function __clone() { }
	public static function get() {
		if( is_null(self::$db) )
		{
			global $db_base, $db_user, $db_pass;

			try {
				self::$db = new PDO( $db_base, $db_user, $db_pass );
			}
			catch( PDOException $e ) { die( 'Greška pri spajanju na bazu: ' . $e->getMessage() ); }
		}	
		return self::$db;
	}
}
function generateRandomString($length = 64) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function addUserToDatabase()
{
	$email = $_POST["email"];
	// Ukloni sve ilegalne znakove iz email adrese.
	$email = filter_var( $email, FILTER_SANITIZE_EMAIL );

	// Validiraj e-mail
	if( filter_var( $email, FILTER_VALIDATE_EMAIL ) === false )
	{
		echo "$email nije ispravna email adresa.";
		return false;
	}
	if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) )
		return false;
	$hashed_password = password_hash( $_POST['password'], PASSWORD_DEFAULT );
	if( ctype_alnum( $_POST['username'] ) && (strlen($_POST['username'])>=6) && (strlen($_POST['password'])>=6))
		$username = $_POST['username'];
	else {
		echo 'Username moram biti minimalno 6 alfanumeričkih znakova i password mora biti minimalno 6 znakova!<br />';
		return false;
	}
	//----PORVJERA JELI JEDNISTVEN USERNAME....
	$st = DB::get()->prepare( 'SELECT password FROM KORISNICI WHERE username = :username' );

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}

	$st->execute( array( 'username' => $username ) );

	$error = $st->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}

	$hashed_password = $st->fetchColumn();
	if( $hashed_password ) {
		echo 'Taj username postoji!<br />';
		return false;
	}
//	-------------------------------------------------
	$code =  generateRandomString();
	$st = DB::get()->prepare( 'INSERT INTO KORISNICI (username, password, code, dozvola) VALUES ' .
	                                            '(:username, :password, :code, :dozvola)' );
		
	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}
                                            	
	$st->execute( array( 'username' => $username, 
	                     'password' => $hashed_password, 
	                     'code'=>$code,
	                     'dozvola'=>0) );                                        

	$error = $st->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}
	$to = $email;
	$subject = "Registracija!!";
	$body = "Ostao je još jedan korak do registracije: rp2.studenti.math.hr/putanja_do_skripte/register.php?code=".$code;
	$header = "Reply-To: jure.pmf.hr@gmail.com\r\n";
	mail($to, $subject, $body, $header);
	return true;
}


function verifyLogin()
{
	if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) )
		return false;

	// Dozvoli samo alfanumerička imena
	if( ctype_alnum( $_POST['username'] ) )
		$username = $_POST['username'];
	else {
		echo 'Username se smije sastojati samo od slova i znamenki.<br />';
		return false;
	}
		
	// Pripremi upit na bazu podataka: dohvati password of tog usera.
	$st = DB::get()->prepare( 'SELECT password, dozvola FROM KORISNICI WHERE username = :username' );

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}

	// Izvrši upit.
	$st->execute( array( 'username' => $username ) );

	$error = $st->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}

	// Dohvati password.
	$row = $st->fetch();
	if( !$row['password']) {
		echo 'Taj username ne postoji!<br />';
		return false;
	}
	if( $row['dozvola']===0) {
		echo 'Potvrdite rezervaciju!<br />';
		return false;
	}
	return true;

}

