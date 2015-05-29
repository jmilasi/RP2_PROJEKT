<meta charset = "utf-8">
<?php
//VRATIT CE JSON POPUNJEN SA PODACIMA IZ BAZE, KOJE CE SE ISPISATI U OBLIKU TABLICE
if($_SERVER[CONTENT_TYPE] !== "application/json")
{
	echo "Krivi podaci!";
	die();
}
$filter = json_decode( file_get_contents( 'php://input' ), true );

$datum = $filter['datum'];
$predavaonica = $filter["predavaonica"];

if(isset($datum) && isset($predavaonica) && ctype_alnum( $datum) && ctype_alnum( $predavaonica ) )
{
	$st = DB::get()->prepare( 'SELECT SAT, IME_KOLEGIJA, PREDAVAC FROM RASPORED WHERE DATUM = :datum AND BROJ_PREDAVAONICE = :predavaonica' );

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}

	$st->execute()

	$error = $st->errorInfo( array( 'DATUM' => $datum, 'BROJ_PREDAVAONICE' => $predavaonica ));
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}
	$ret = array();

	while( $row = $st->fetch() )
			$ret[] = $row;

	header( 'Content-Type: application/json' );

	echo json_encode( $ret );

?>