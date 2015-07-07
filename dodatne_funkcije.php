<?php
include_once "podatci.php";
include_once "funkcije.php";

function rezerviraj($predavaonica, $datum, $sat, $ime_kolegija) { 
	if (!isset($predavaonica) || !isset($datum) || !isset($sat)) {
		echo "Popunite podatke do kraja!";
		return false;	
	}

	if (!ctype_alnum($predavaonica) || !ctype_alnum($datum) || !ctype_alnum($sat)) {
		echo "Greška u podacima predavaonice, datuma ili sata!<br />";
		return false;
	}

	// provjera rezervira li tko
	$st = DB::get()->prepare("SELECT * FROM OBRADA WHERE PREDAVAONICA = :predavaonica AND DATUM = :datum");

	$error = DB::get()->errorInfo();
	if (isset($error[2])) {
		echo "DB::get()->prepare error: " . $error[2];
		return false;
	}

	$st->execute(array("predavaonica" => $predavaonica, "datum" => $datum));

	$error = $st->errorInfo();
	if (isset($error[2])) {
		echo "$st->execute error: " . $error[2];
		return false;
	}

	$st->fetchColumn();
	if ($st->fetchColumn()) {
		echo "Rezervacija u tijeku...";
		return false;
	}
	// kraj provjere rezervira li tko
	
	// spremi usera u bazu podataka
	$st = DB::get()->prepare(
		"INSERT INTO REZERVIRA(BROJ_SOBE, DATUM) VALUES (:predavaonica, :datum)");

	$error = DB::get()->errorInfo();
	if (isset($error[2)) {	
		echo "DB::get()->prepare error: " . $error[2];
		return false;
	}
                                            	
	$st->execute(array("predavaonica" => $predavaonica, "datum" => $datum));                                        

	$error = $st->errorInfo();
	if (isset($error[2])) {
		echo "$st->execute error: " . $error[2];
		return false;
	}

	// Dohvati puno ime od username-a!
	$st = DB::get()->prepare("SELECT PREDAVAC FROM KORISNIK WHERE USERNAME = :username");

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

	$predavac = $st->fetchColumn();

	// dodaj rezervirani termin
	$st = DB::get()->prepare(
		"INSERT INTO REZERVACIJE(PREDAVAONICA, DATUM, SAT, PREDAVAC, IME_KOLEGIJA, DOZVOLA)
		VALUES (:predavaonica, :datum, :sat, :predavac, :ime_kolegija, :dozvola)");

	$error = DB::get()->errorInfo();
	if (isset($error[2])) {	
		echo "DB::get()->prepare error: " . $error[2];
		return false;
	}
                                            	
	$st->execute(array(
		"predavaonica" => $predavaonica, 
        "datum" => $datum,
		"sat" => $sat,
		"predavac"=> $predavac,
		"ime_kolegija" => $ime_kolegija,
		"dozvola" => 1));                                        

	$error = $st->errorInfo();
	if (isset($error[2])) {	
		echo "$st->execute error: " . $error[2];
		return false;
	}

	return true;
}

function ponisti_rezervaciju($predavaonica, $datum, $sat) {
	if (!isset( $predavaonica) || !isset($datum))
		return false;

	if (!ctype_alnum($predavaonica) || !ctype_alnum($datum) || !ctype_alnum($sat)) {
		echo "Greška u poslanim podacima!<br />";
		return false;
	}

	$st = DB::get()->prepare("DELETE FROM REZERVACIJE WHERE PREDAVAONICA = :predavaonica AND DATUM = :datum AND SAT = :sat");

	$error = DB::get()->errorInfo();
	if (isset($error[2])) {	
		echo "DB::get()->prepare error: " . $error[2];
		return false;
	}
                                            	
	$st->execute(array(
		"predavaonica" => $predavaonica, 
		"datum" => $datum,
		"sat" => $sat));                                        

	$error = $st->errorInfo();
	if (isset($error[2])) {	
		echo "$st->execute error: " . $error[2];
		return false;
	}

	return true;
}
?>