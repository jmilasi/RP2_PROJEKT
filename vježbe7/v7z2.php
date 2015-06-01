<meta charset = "utf-8">				
<?php
include "v7z1.php";


class Ucenik extends Razred{
	private $ime_ucenika;
	private $godina;
	function __construct($ime, $ime_u, $god)
	{
		parent::__construct($ime);
		$this->ime_ucenika = $ime_u;
		$this->godina = $god;
	}
	function __destruct(){
		parent::__destruct();
		echo "Dekstrutor od ucenka</br>";
	}
	function ispis(){
		echo "Ime razreda: ". parent::ispis(). " Ime ucenika: ".$this->ime_ucenika.", godina: ".$this->godina."</br>";
	}

}
$ucenik = new Ucenik("4h","Hrvoje",1992);
$ucenik->ispis();
echo Ucenik::$ime_razreda;
?>