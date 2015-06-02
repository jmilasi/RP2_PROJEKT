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
	$st = DB::get()->prepare( 'INSERT INTO KORISNIK (USERNAME, PASSWORD, PREDAVAC, ADMIN) VALUES ' .
	                                            '(:username, :password, :ime, :admin)' );
		
	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}
                                            	
	$st->execute( array( 'username' => $username, 
	                     'password' => $hashed_password,
	                     'ime' => $ime,
	                     'admin' => 0 ) );                                        

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
	if( password_verify( $_POST['password'], $hashed_password ) ) 
		return true;
	else {
		echo 'Password nije ispravan!<br />';
		return false;
	}
}
function rezerviraj($predavaonica, $datum, $sat, $ime_kolegija){ 
	if( !isset( $predavaonica ) || !isset( $datum ) || !isset( $sat ) )
	{
		echo "Popunite podatke do kraja!";
		return false;	
	}	

	if( !ctype_alnum( $predavaonica ) && !ctype_alnum( $datum )&& !ctype_alnum( $sat ))
	{
		echo 'Greška u podacima predavaonice i datuma!<br />';
		return false;
	}
	//---------------------PROVJERA REZERVIRA LI TKO--------------------------
	$st = DB::get()->prepare( 'SELECT * FROM OBRADA WHERE BROJ_SOBE = :predavaonica AND DATUM = :datum' );

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}

	$st->execute( array( 'predavaonica' => $predavaonica, 'datum' => $datum) );

	$error = $st->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}

	 $st->fetchColumn();
	if( ($st->fetchColumn()) ) {
		return false;
	} 
	//-----------------------------------------------------------------
	// Spremi usera u bazu podataka.
	$st = DB::get()->prepare( 'INSERT INTO REZERVIRA(BROJ_SOBE, DATUM) VALUES ' .
	                                            '(:predavaonica, :datum)' );

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}
                                            	
	$st->execute( array( 'predavaonica' => $predavaonica, 
	                     'datum' => $datum ) );                                        

	$error = $st->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}

	//-- Dobavi ime od username---
	$st = DB::get()->prepare( 'SELECT PREDAVAC FROM KORISNIK WHERE USERNAME = :username' );

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

	$predavac = $st->fetchColumn();
	//----- DODAJ REZERVIRANI TERMIN----
		// Spremi usera u bazu podataka.
	$st = DB::get()->prepare( 'INSERT INTO REZERVACIJE(PREDAVAONICA, DATUM, SAT, PREDAVAC, IME_KOLEGIJA, DOZVOLA) VALUES ' .
	                                            '(:predavaonica, :datum, :sat, :predavac, :ime_kolegija, :dozvola)' );

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}
                                            	
	$st->execute( array( 'predavaonica' => $predavaonica, 
	                     'datum' => $datum,
	                     'sat' => $sat,
	                     'predavac'=> $predavac,
	                     'ime_kolegija' => $ime_kolegija,
	                     'dozvola' => 1 ));                                        

	$error = $st->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}

	return true;                                                                               	


}

function ponisti_rezervaciju($predavaonica, $datum, $sat){
	if( !isset( $predavaonica ) || !isset( $datum ) )
		return false;

	if( !ctype_alnum( $predavaonica ) && !ctype_alnum( $datum) && !ctype_alnum($sat))
	{
		echo 'Greška u poslanim podacima!<br />';
		return false;
	}
	$st = DB::get()->prepare( 'DELETE FROM REZERVACIJE WHERE PREDAVAONICA = :predavaonica AND DATUM = :datum AND SAT = :sat');

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}
                                            	
	$st->execute( array( 'predavaonica' => $predavaonica, 
	                     'datum' => $datum,
	                     'sat' => $sat ));                                        

	$error = $st->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}

	return true;           

}