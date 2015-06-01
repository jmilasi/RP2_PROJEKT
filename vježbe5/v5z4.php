<meta charset="utf-8">
<?php

function validate($user, $pass)
{
	$users = array(
		'pero' => 'perinasifra',
		'ana' => 'aninasifra');

	if(isset($users[$user]) && ( $users[$user] === $pass))
		return true;
	else
		return false;
}

$name = $_POST["username"];
$pass = $_POST["password"];

if(validate($name, $pass))
{
	echo "Dobrodošli ". $name;
}
else
{
	echo "Netočna lozinka ili username";
}

?>