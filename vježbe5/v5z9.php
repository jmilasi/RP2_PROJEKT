<meta charset="utf-8">
<?php

function validate($user, $pass)
{
	$f = fopen("password.txt", "r") or die("ne mogu otvoriti datoteku");
	while($podaci = fscanf($f, "%s\t%s\n")){
		list($ime, $lozinka) = $podaci;
		if(( $ime === $user )&& ( $lozinka === $pass))
			return true;
	}
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