<!DOCTYPE html>
<html>
<head>
	<title>Posjetitelj</title>
	<meta charset = "utf-8">
</head>
<body>
    <p> Dobro došli, Vi ste naš <?php

    $f = fopen('brojac.txt', 'r');


    $count = fscanf($f, "%d\n");
    list($brojac) = $count;
    echo $brojac;
    fclose($f) or die($php_errormsg);
    $brojac +=1; 
    $f = fopen('brojac.txt', 'w');
    fwrite($f, $brojac);
    fclose($f) or die($php_errormsg);
?>

    posjetitelj </p> 
</body>
</html>