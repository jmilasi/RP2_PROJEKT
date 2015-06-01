<?php

// U datoteci nedostupnoj kroz web su spremljene varijable $db_base, $db_user, $db_pass
// Npr. $db_base = 'mysql:host=db.com;dbname=imeBaze;charset=utf8', $db_user='pero', $db_pass='sifra';
include_once '../../db_settings.php';

// Klasa sa statičkom funkcijom get koja ima konekciju prema bazi
// Svaka funkcija može pristupiti bazi kroz DB::get()
class DB
{
	// Interna statička varijabla koja čuva konekciju na bazu
	private static $db;

	// Zabranimo new DB() i kloniranje;
	final private function __construct() { }
 	final private function __clone() { } //služi za kloniranje varijabli

	// Statička funkcija za pristup bazi.
 	public static function get() {
		// Spoji se ako već nisi.
		if( is_null(self::$db) )
		{
			global $db_base, $db_user, $db_pass;

			try {
				self::$db = new PDO( $db_base, $db_user, $db_pass );
			}
			catch( PDOException $e ) { die( 'Greška pri spajanju na bazu: ' . $e->getMessage() ); }
		}	

		// Vrati konekciju na bazu
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
		$username = $_POST['username'];
	else {
		echo 'Korisničko ime smije imati samo slova i znamenke!<br />';
		return false;
	}
		
	// Spremi usera u bazu podataka.
	$st = DB::get()->prepare( 'INSERT INTO users (username, password) VALUES ' .
	                                            '(:username, :password)' );
		
	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}
                                            	
	$st->execute( array( 'username' => $username, 
	                     'password' => $hashed_password ) );                                        

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

	// Dozvoli samo alfanumerička imena
	if( ctype_alnum( $_POST['username'] ) )
		$username = $_POST['username'];
	else {
		echo 'Username se smije sastojati samo od slova i znamenki.<br />';
		return false;
	}
		
	// Pripremi upit na bazu podataka: dohvati password of tog usera.
	$st = DB::get()->prepare( 'SELECT password FROM users WHERE username = :username' ); //ovdje pripremimo upit, a s execute() zadajemo taj username eksplicitno

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}

	// Izvrši upit za zadani username
	$st->execute( array( 'username' => $username ) ); //

	$error = $st->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}

	// Dohvati password.
	$hashed_password = $st->fetchColumn();
	if( !$hashed_password ) {
		echo 'Taj username ne postoji!<br />';
		return false;
	}

	if( password_verify( $_POST['password'], $hashed_password ) ) //password_verofy() koristimo ako je $hashed_password nastao pomocu  password_hash()
		return true;
	else {
		echo 'Password nije ispravan!<br />';
		return false;
	}
}

