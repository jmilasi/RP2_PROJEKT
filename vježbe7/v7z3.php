<meta charset = "utf-8">
<?php
class Sok{
	private $ime_soka;
	private $cijena_soka;
	private $dostupnost_drzava;
	function __construct($ime, $cijena, $drzava){
		$this->ime_soka = $ime;
		$this->cijena_soka = $cijena;
		$this->dostupnost_drzava = $drzava;
	}
	function __destruct(){ echo "Destruktor";}
	function get_ime()
	{
		return $this->ime_soka;
	}
	function get_cijena()
	{
		return $this->cijena_soka;
	}
	function arr_get_dostupnost()
	{
		return $this->dostupnost_drzava;
	}
}

$sok = new Sok("vocko", 12, array("Hrv", "BiH", "Slo"));
echo "Ime: ". $sok->get_ime()."</br>";
echo "Cijena: ".$sok->get_cijena()."</br> Dostupnost: ";
$niz = $sok->arr_get_dostupnost();
foreach ($niz as $value) {
	echo " ". $value;
}
echo "</br>";
?>