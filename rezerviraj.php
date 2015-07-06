<?php
include_once 'funkcije.php';
session_start();
//VRATIT CE JSON POPUNJEN SA PODACIMA IZ BAZE, KOJE CE SE ISPISATI U OBLIKU TABLICE
if( strpos( $_SERVER["CONTENT_TYPE"], "application/json" ) === false )
{
	echo "Krivi podaci!" . $_SERVER["CONTENT_TYPE"];
	die();
}

$filter = json_decode( file_get_contents( 'php://input' ), true );

$datum = $filter['datum'];
$predavaonica = $filter['predavaonica'];
$sat = $filter['sat'];
$kolegij = $filter['kolegij'];
$predavac = $filter['predavac'];
$dan_u_tj = dan_u_tjednu($datum);
if(isset($datum) && isset($predavaonica) && ctype_alnum( $predavaonica ) && isset($kolegij) && isset($predavac))
{

	$st1 = DB::get()->prepare(
		'INSERT INTO REZERVACIJE(PREDAVAONICA, DATUM, SAT, PREDAVAC, IME_KOLEGIJA, DOZVOLA) VALUES '.' (:predavaonica,:datum, :sat,:predavac, :kolegij,:dozvola');

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}

	$st1->execute( array( 
		'predavaonica' => $predavaonica, 
		'datum' => $datum, 
		'sat' => $sat,
		'predavac' => $predavac,
		'kolegij' => $kolegij, 'dozvola' => $dozvola ) );

/*	$error = $st1->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st1->execute error: ' . $error[2];
		return false;
	}
*/

	$ret = array();
	header( 'Content-Type: application/json' );
	//echo json_encode( $ret );
}


?>
