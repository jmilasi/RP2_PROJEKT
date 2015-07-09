<?php
include_once "funkcije.php";

$filter = json_decode(file_get_contents("php://input"), true);

$datum = $filter["datum"];
$predavaonica = $filter["predavaonica"];
$dan_u_tj = dan_u_tjednu($datum);

if (isset($datum) && isset($predavaonica) && ctype_alnum($predavaonica)) {
	// slijedi dio koda za ispis rasporeda
	$st = DB::get()->prepare(
		"SELECT SAT, IME_KOLEGIJA, PREDAVAC FROM BAZNI_RASPORED WHERE DAN = :dan_u_tj AND PREDAVAONICA = :predavaonica
		UNION SELECT SAT, IME_KOLEGIJA, PREDAVAC FROM REZERVACIJE WHERE DATUM = :datum AND PREDAVAONICA = :predavaonica
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

	header("Content-Type: application/json");
	echo json_encode($ret);
}
?>