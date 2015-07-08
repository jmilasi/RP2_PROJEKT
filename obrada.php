<?php
include_once "funkcije.php";
session_start();

$filter = json_decode(file_get_contents("php://input"), true);

$datum = $filter["datum"];
$predavaonica = $filter["predavaonica"];

if (isset($datum) && isset($predavaonica) && ctype_alnum($predavaonica)) {
	$st = DB::get()->prepare("DELETE FROM OBRADA WHERE PREDAVAONICA = :predavaonica AND DATUM = :datum");

	$error = DB::get()->errorInfo();
	if (isset($error[2])) {
		echo "DB::get()->prepare error: " . $error[2];
		return false;
	}

	$st->execute(array(
		"predavaonica" => $predavaonica, 
		"datum" => $datum));

	$error = $st->errorInfo();
	if (isset($error[2])) {	
		echo "$st->execute error: " . $error[2];
		return false;
	}

	$ret = "ok";
	header("Content-Type: application/json");
	echo json_encode($ret);
}
?>