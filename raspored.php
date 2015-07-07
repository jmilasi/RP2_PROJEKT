<?php
include_once "funkcije.php";
session_start();

$filter = json_decode(file_get_contents("php://input"), true);

$datum = $filter["datum"];
$predavaonica = $filter["predavaonica"];
$dan_u_tj = dan_u_tjednu($datum);

if (isset($datum) && isset($predavaonica) && ctype_alnum($predavaonica)) {
	if (isset($_SESSION["tko"]))		
		$tko = $_SESSION["tko"];
	if(isset($_SESSION["admin"]))
		$admin = $_SESSION["admin"];
	else
		$admin = 0;
	// prvo treba provjeriti radi li već netko s odabranim datumom i odabranom predavonicom
	$st = DB::get()->prepare(
		"SELECT PREDAVAONICA, DATUM FROM OBRADA WHERE PREDAVAONICA = :predavaonica AND DATUM = :datum");

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
	
	// ako postoji netko tko već radi, onda javi da je zauzeto
	$row = $st->fetch();
	if ($row["PREDAVAONICA"] == $predavaonica && $row["DATUM"] == $datum) {
		$ret = "zauzeto";
		header("Content-Type: application/json");
		echo json_encode($ret);
		return;
	}

	// u protivnom zauzmi
	$st = DB::get()->prepare("INSERT INTO OBRADA VALUES (:predavaonica, :datum)");

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

	// slijedi dio koda za ispis rasporeda
	$st = DB::get()->prepare(
		"SELECT SAT, IME_KOLEGIJA, PREDAVAC, DOZVOLA FROM BAZNI_RASPORED WHERE DAN = :dan_u_tj AND PREDAVAONICA = :predavaonica
		UNION SELECT SAT, IME_KOLEGIJA, PREDAVAC, DOZVOLA FROM REZERVACIJE WHERE DATUM = :datum AND PREDAVAONICA = :predavaonica
		ORDER BY SAT");

	$error = DB::get()->errorInfo();
	if (isset($error[2])) {	
		echo "DB::get()->prepare error: " . $error[2];
		return false;
	}

	$st->execute(array("dan_u_tj" => $dan_u_tj, "predavaonica" => $predavaonica, "datum" => $datum));

	$error = $st->errorInfo();
	if (isset($error[2])) {	
		echo "$st->execute error: " . $error[2];
		return false;
	}

	$ret = array();
	while ($row = $st->fetch())
		$ret[] = $row;

	if (isset($tko) && $admin == 0) {
		for ($i = 0; $i < sizeof($ret); ++$i) {
			if ($ret[$i]["DOZVOLA"] == 1 && $ret[$i]["PREDAVAC"] != $tko)
				$ret[$i]["DOZVOLA"] = 0;
		}	
	}

	header("Content-Type: application/json");
	echo json_encode($ret);
}
?>