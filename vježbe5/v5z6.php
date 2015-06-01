<html>
<head>
	<meta charset = "utf-8">
	<title> Niz u string</title>
</head>
<body>
	<?php
		$i = 1;
		$niz = ["Kako", "je", "Stipa", "volio", "Anu."];
		echo "array{";
		foreach ($niz as $value) {
			if($i === 1)
			{
				echo $value;
				$i=2;
			}
			else
				echo ", ".$value;
		}
		echo "}";	
		$string = implode(" ",$niz);
		echo "</br>String: " . $string;

	?>
</body>
</html>
