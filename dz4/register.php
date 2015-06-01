<?php
include_once 'funkcije.php';
if(isset(_GET["code"]))
{
	$code = _GET["code"];
	if(strlen($code !== 64))
	{
		echo "Pokušavate varati!";
		die();
	}
	$st = DB::get()->prepare( 'SELECT username FROM KORISNICI WHERE code = :code' );
	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}

	// Izvrši upit.
	$st->execute( array( 'code' => $code) );

	$error = $st->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}

	$user = $st->fetchColumn();
	if( !$user) {
		echo 'Pokušavate varati!<br />';
		return false;
	}
	
	$st = DB::get()->prepare( 'UPDATE KORISNICI SET dozvola = 1 WHERE code= :code' );

	$error = DB::get()->errorInfo();
	if( isset( $error[2] ) ) {	
		echo 'DB::get()->prepare error: ' . $error[2];
		return false;
	}
                                            	
	$st->execute( array( 'code'=>$code));                                        

	$error = $st->errorInfo();
	if( isset( $error[2] ) ) {	
		echo '$st->execute error: ' . $error[2];
		return false;
	}
	echo "Uspjesno ste se registrirali!"
	header("Location:login.php");
	
?>