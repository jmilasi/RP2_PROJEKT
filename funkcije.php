<meta charset = "utf-8">
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

function addUserToDatabase()
{
	if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) )
		return false;

	// Hashiraj password. Za verzije prije PHP 5.0 može $skrivenaRijec i md5($password.$skrivenaRijec)
	$hashed_password = password_hash( $_POST['password'], PASSWORD_DEFAULT );

	// Dozvoli samo alfanumerička imena
	if( ctype_alnum( $_POST['username'] ) )
	{
		$username = $_POST['username'];
	 	$ime = $_POST['name']. " " . $_POST['lastname'];
	}
	else {
		echo 'Korisničko ime smije imati samo slova i znamenke!<br />';
		return false;
	}
		
	// Spremi usera u bazu podataka.
	$st = DB::get()->prepare( 'INSERT INTO KORISTNIK (username, password,ime, dozvola) VALUES ' .
	                                            '(:username, :password, :ime, :dozvola)' );
		
	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}
                                            	
	$st->execute( array( 'username' => $username, 
	                     'password' => $hashed_password,
	                     'ime' => $ime,
	                     'dozvola' => 0 ) );                                        

	$error = $st->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}

	return true;
}


function verifyLogin()
{
	if( !isset( $_POST['username'] ) || !isset( $_POST['password'] ) )
		return false;
	if( ctype_alnum( $_POST['username'] ) )
		$username = $_POST['username'];
	else {
		echo 'Username se smije sastojati samo od slova i znamenki.<br />';
		return false;
	}
		
	
	$st = DB::get()->prepare( 'SELECT PASSWORD FROM KORISNIK WHERE USERNAME = :username' );

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
	if( !$hashed_password ) {
		echo 'Taj username ne postoji!<br />';
		return false;
	}
	echo $hashed_password;
	if( password_verify( $_POST['password'], $hashed_password ) ) 
		return true;
	else {
		echo 'Password nije ispravan!<br />';
		return false;
	}
}
function dozvola($predavaonica, $datum){
	if( !isset( $predavaonica ) || !isset( $datum ) )
		return false;

	if( ctype_alnum( $predavaonica ) && ctype_alnum( $predavaonica ))
	{

	}
	else {
		echo 'Greška u podacima predavaonice i datuma!<br />';
		return false;
	}
	//-----------------------------------------------
	$st1 = DB::get()->prepare( 'SELECT * FROM REZERVIRA WHERE BROJ_SOBE = :predavaonica AND DATUM = :datum' );

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}

	$st1->execute( array( 'predavaonica' => $predavaonica, 'datum' => $datum) );

	$error = $st1->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}

	 $st1->fetchColumn();
	if( ($st1->fetchColumn()) ) {
		return false;
	}
	//-----------------------------------------------------------------
	// Spremi usera u bazu podataka.
	$st2 = DB::get()->prepare( 'INSERT INTO REZERVIRA(BROJ_SOBE, DATUM) VALUES ' .
	                                            '(:predavaonica, :datum)' );

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}
                                            	
	$st2->execute( array( 'predavaonica' => $predavaonica, 
	                     'datum' => $datum ) );                                        

	$error = $st2->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}

	// TREBA DODATI DODAVAJE U BAZU TOGA PREDMETA, PRVO UPIT NA IME USERNAME!!-->Jure će sredit!

	return true;                                                                               	


}
