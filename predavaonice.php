<?php
include_once "funkcije.php";

$filter = json_decode(file_get_contents("php://input"), true);

$st = DB::get()->prepare("SELECT BROJ FROM PREDAVAONICE");

$error = DB::get()->errorInfo();
if (isset($error[2])) {
	echo "DB::get()->prepare error: " . $error[2];
	return false;
}

$st->execute(array());

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
?>