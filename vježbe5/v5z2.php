<html>
<body>
	<?php
		echo "<table> <caption>Tablica</caption> <tbody>";
		for( $i = 1 ; $i <= 10; $i++)
		{
			echo "<tr>";
			for( $j = 1; $j <= 10; $j++)
				echo "<th>". $i*$j . "</th>";
			echo "</tr>";
		}
		echo "</tbody></table>";
	?>
</body>
</html>
