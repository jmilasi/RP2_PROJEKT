<meta charset="utf-8">
<?php

$string = $_POST['string'];
$br =  substr_count($string,"re");
$br +=  substr_count($string,"ru");
$br +=  substr_count($string,"pu");
$br +=  substr_count($string,"pe");

echo "Upisali ste: " . $string . "</br>"."Ispis: ". $br;
?>