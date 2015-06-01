<html>
<head>
	<meta charset = "utf-8">
	<title> Ispis datuma</title>
</head>
<body>
	<?php
		$datum = getdate();
		if($datum["hours"] < 10 )
		{
			echo "Dobro jutro! </br>";
		}
		if($datum["hours"] > 10 && $datum["hours"] < 18)
		{
			echo "Dobar dan! </br>";
		}
		if($datum["hours"] > 18 )
		{
			echo "Dobar jutro! </br>";
		}
		echo "Dobro došli, danas je " . $datum["mday"] . "." .$datum["month"] . ".".$datum["year"];
		echo "</br> Vrijeme: " . $datum["hours"] . ":" . $datum["minutes"] . ":" . $datum["seconds"];

	?>
</body>
</html>
