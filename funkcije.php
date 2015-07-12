<?php

include_once "podatci.php";

class DB {
	private static $db;
	final private function __construct() {}
 	final private function __clone() {}

 	public static function get() {
		if (is_null(self::$db)) {
			global $db_base, $db_user, $db_pass;
			try {
				self::$db = new PDO($db_base, $db_user, $db_pass);
			}
			catch(PDOException $e) {
				die("Greška pri spajanju na bazu: " . $e->getMessage());
			}
		}
		return self::$db;
	}
}

function addUserToDatabase() {
	if (isset($_POST["p_admin"]) && $_POST["p_admin"]=='da')
		$admin = '1';
	else
		$admin = '0';
	
	if (!isset($_POST["username"]) || !isset($_POST["password"]))
		return false;
	
	$hashed_password = password_hash($_POST["password"], PASSWORD_DEFAULT);

	// Dozvoli samo alfanumerička imena!
	if (ctype_alnum($_POST["username"])) {
		$username = $_POST["username"];
	 	$ime = $_POST["name"]. " " . $_POST["lastname"];
	}

	else {
		$message = "Korisničko ime smije imati samo slova i znamenke!";
		echo "<script type='text/javascript'>alert('$message');</script>";
		return false;
	}
		
	// Spremi usera u bazu podataka.
	$st = DB::get()->prepare(
		"INSERT INTO KORISNIK (USERNAME, PASSWORD, PREDAVAC, ADMIN) VALUES (:username, :password, :ime, :admin)");
		
	$error = DB::get()->errorInfo();
	if (isset($error[2])) {	
		echo "DB::get()->prepare error: " . $error[2];
		return false;
	}
                                            	
	$st->execute(array(
		"username" => $username,
		"password" => $hashed_password,
		"ime" => $ime,
		"admin" => $admin));                                        

	$error = $st->errorInfo();
	if (isset($error[2])) {	
		echo "$st->execute error: " . $error[2];
		return false;
	}

	return true;
}

function verifyLogin() {
	if (!isset($_POST["username"]) || !isset($_POST["password"]))
		return false;
	if (ctype_alnum($_POST["username"]))
		$username = $_POST["username"];
	else {
		$message = "Korisničko ime smije imati samo slova i znamenke!";
		echo "<script type='text/javascript'>alert('$message');</script>";
		return false;
	}
		
	$st = DB::get()->prepare("SELECT PASSWORD FROM KORISNIK WHERE USERNAME = :username");

	$error = DB::get()->errorInfo();
	if (isset($error[2])) {
		echo "DB::get()->prepare error: " . $error[2];
		return false;
	}

	$st->execute(array("username" => $username));

	$error = $st->errorInfo();
	if (isset($error[2])) {	
		echo "$st->execute error: " . $error[2];
		return false;
	}

	$hashed_password = $st->fetchColumn();
	if (!$hashed_password) {
		$message = "To korisničko ime ne postoji!";
		echo "<script type='text/javascript'>alert('$message');</script>";
		return false;
	}

	if (password_verify($_POST["password"], $hashed_password)) 
		return true;
	else {
		$message = "Password nije ispravan!";
		echo "<script type='text/javascript'>alert('$message');</script>";
		return false;
	}
}

function dan_u_tjednu($neki_datum) {  
	$dan_u_tj = "";
	$max = strlen($neki_datum);
	$neki_datum[$max-1] = "";
	$niz = explode(".", $neki_datum);
	$niz1 = array();
	$dan = intval($niz[0]);
	$godina = intval($niz[2]);
	$mjesec = intval($niz[1]);
	$day_in_week = jddayofweek(cal_to_jd(CAL_GREGORIAN, $mjesec, $dan, $godina), 1); 
	$day_to_dan = array("Monday" => "ponedjeljak", "Tuesday" => "utorak",	
	"Wednesday" => "srijeda", "Thursday" => "cetvrtak", "Friday" => "petak",
	"Saturday" => "subota", "Sunday" => "nedjelja"); 
	foreach ($day_to_dan as $key => $value) {
		if ($key == $day_in_week) {
			$dan_u_tj = $value;
			break;	
		}
	}
	return $dan_u_tj;
}
?>