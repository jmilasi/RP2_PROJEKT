<?php
function fact($n)
{
	if($n === 1)
		return 1;
	else
	return $n*fact($n-1);
}
if(!isset($_GET['number']))
	$number = 4;
else
	$number = $_GET["number"];
	$fac = fact($number); 
	echo $fac;
?>