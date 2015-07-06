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
$ime_kolegija = $filter['kolegij'];
$predavac = $filter['predavac'];
$dan_u_tj = dan_u_tjednu($datum);
if(isset($datum) && isset($predavaonica) && ctype_alnum( $predavaonica ) && isset($kolegij) && isset($predavac))
{

	$st1 = DB::get()->prepare(
		'DELETE FROM REZERVACIJA WHERE PREDAVAONICA=:predaonica, DATUM=:datum, SAT=:sat' );

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}

	$st1->execute( array( 'predaonica' => $predaonica, 'datum' => $datum, 'sat' => $sat) );

	$error = $st1->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st1->execute error: ' . $error[2];
		return false;
	}

	//----------------------VRATI PONOVNO SVE PREDMETE--------------
	if(isset($_SESSION['predavac']))		
		$predavac = $_SESSION['predavac'];
	$st1 = DB::get()->prepare(
		'SELECT SAT, IME_KOLEGIJA, PREDAVAC, DOZVOLA FROM BAZNI_RASPORED WHERE DAN = :dan_u_tj AND PREDAVAONICA = :predavaonica
		UNION SELECT SAT, IME_KOLEGIJA, PREDAVAC, DOZVOLA FROM REZERVACIJE WHERE DATUM = :datum AND PREDAVAONICA = :predavaonica
		ORDER BY SAT');

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}

	$st1->execute( array( 'dan_u_tj' => $dan_u_tj, 'predavaonica' => $predavaonica, 'datum' => $datum) );

	$error = $st1->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st1->execute error: ' . $error[2];
		return false;
	}
	

	//$ret =array( array( 'SAT' => '8',  'IME_KOLEGIJA' => 'ANALIZA',  'PREDAVAC' => 'PAZANIN', 'DOZVOLA' => 0 ));
	$ret = array();
	while( $row = $st1->fetch() )
			$ret[] = $row;

	if (isset($predavac)) {
		for ($i = 0; $i < sizeof($ret); ++$i)
			if ($ret[$i]['DOZVOLA'] == 1)
			{
				if($ret[$i]['PREDAVAC'] != $predavac)
				{
					$ret[$i]['DOZVOLA'] = 0;
				}
			}
	}

	header( 'Content-Type: application/json' );
	echo json_encode( $ret );
}
}

?>
