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


// ODREDI DAN U TJEDNU
function dan_u_tjednu( $datum1 )
{  
	$dan_u_tj = "";
	$max = strlen($datum1);
	$datum1[$max-1] = '';
	$niz = explode('.', $datum1);
	$niz1 = array();
	$dan = intval($niz[0]);
	$godina = intval($niz[2]);
	$mjesec = intval($niz[1]);
	$day_in_week =jddayofweek( cal_to_jd(CAL_GREGORIAN,$mjesec,$dan,$godina),1); 
	$day_to_dan = array('Monday'=> 'ponedjeljak','Tuesday' => 'utorak',	
	'Wednesday' => 'srijeda', 'Thursday' => 'cetvrtak','Friday' => 'petak',
	'Saturday' => 'subota'); 
	foreach ($day_to_dan as $key => $value) 
	{
		if($key == $day_in_week)
		{
			$dan_u_tj = $value;
			break;	
		}
	}
	return $dan_u_tj;
}

$dan_u_tj = dan_u_tjednu($datum);

if(isset($datum) && isset($predavaonica) && ctype_alnum( $predavaonica ) )
{
	if(isset($_SESSION['tko']))		
		$tko = $_SESSION['tko'];
	
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

	if (isset($tko)) {
		for ($i = 0; $i < sizeof($ret); ++$i)
			if ($ret[$i]['DOZVOLA'] == 1)
			{
				if($ret[$i]['PREDAVAC'] != $tko)
				{
					$ret[$i]['DOZVOLA'] = 0;
				}
			}
	}

	header( 'Content-Type: application/json' );
	echo json_encode($ret);
}
?>