<?php
include_once "funkcije.php";
session_start();

$filter = json_decode(file_get_contents("php://input"), true);

$datum = $filter["datum"];
$predavaonica = $filter["predavaonica"];
$sat = $filter["sat"];
$kolegij = $filter["kolegij"];
$predavac = $filter["predavac"];
$dan_u_tj = dan_u_tjednu($datum);

if ($predavac != $_SESSION["tko"]) {
	$ret = "Ne možete rezervirati termin koji nije na Vaše ime!";
	header("Content-Type: application/json");
	echo json_encode($ret);
	return false;
}

if (isset($datum) && isset($predavaonica) && ctype_alnum($predavaonica) &&
	isset($kolegij) && isset($predavac) && isset($dan_u_tj)) {

	$st = DB::get()->prepare("INSERT INTO REZERVACIJE VALUES (:predavaonica, :datum, :sat, :predavac, :kolegij, 1)");

	$error = DB::get()->errorInfo();
	if (isset($error[2])) {
		echo "DB::get()->prepare error: " . $error[2];
		return false;
	}

	$st->execute(array(
		"predavaonica" => $predavaonica, 
		"datum" => $datum, 
		"sat" => $sat,
		"predavac" => $predavac,
		"kolegij" => $kolegij));

	$error = $st->errorInfo();
	if (isset($error[2])) {	
		echo "$st->execute error: " . $error[2];
		return false;
	}
	
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

	if (isset($tko)) {
		for ($i = 0; $i < sizeof($ret); ++$i) {
			if ($ret[$i]['DOZVOLA'] == 1 && $ret[$i]['PREDAVAC'] != $tko)
				$ret[$i]['DOZVOLA'] = 0;
		}
	}

	header("Content-Type: application/json");
	echo json_encode($ret);
}
?>