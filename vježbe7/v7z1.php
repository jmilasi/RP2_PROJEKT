<meta charset = "utf-8">
<?php

class Razred{
	static  $ime_razreda;
	private $broj_ucenika;
	function __construct( $ime, $broj = 1 ){
		$this->ime_razreda = $ime;
		$this->broj_ucenika = $broj;
	}
	public function ispis()
	{
		echo "Ime:" . $this->ime_razreda . ". Broj_ucenika: ".$this->broj_ucenika."</br>";
	}
	function __destruct(){echo "Destruktor_razred!</br>";}
}

/*$raz = new Razred("4h", 28);
$raz->ispis(); */
?>
