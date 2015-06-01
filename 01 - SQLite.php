<!DOCTYPE html>
<html>
<head><meta charset="utf8"></head>
<body>
<?php
// Ovaj primjer koristi SQLite bazu.
// Posve jednako bi se koristila MySQL baza, jedina promjena je u stvaranju PDO objekta!
// (i u provjeri postoji li tablica studenti...)
// SQLite bazu (na lokalnom računalu) možemo pregledavati preko npr. Firefox pluigina "SQLite Manager":
// https://addons.mozilla.org/en-US/firefox/addon/sqlite-manager/

// Stvori novu bazu ili se spoji na postojeću
$db = new PDO( 'sqlite:/tmp/studenti.sqlite' );

// Provjeri da li postoji tablica studenti
$q = $db->query( "SELECT name FROM sqlite_master WHERE type = 'table' " .
                 "AND name = 'studenti'" );

// Ako upit ne vrati niti jedan redak, onda treba stvoriti tu tablicu i dodati podatke.
if( $q->fetch() === false ) 
{
	 $db->exec( <<<_SQL_
CREATE TABLE studenti (
    JMBAG CHAR(10) NOT NULL,
    ime CHAR(50),
	prezime CHAR(50),
	ocjena TINYINT,
	PRIMARY KEY(JMBAG)
)
_SQL_
    );

	// Nakon stvaranja tablice, ubacit ćemo podatke za nekoliko studenata.
	$sql=<<<_SQL_
INSERT INTO studenti VALUES ('1234567890', 'Pero', 'Perić', 5);
INSERT INTO studenti VALUES ('1111111111', 'Ana', 'Anić', 3);
INSERT INTO studenti VALUES ('9999999999', 'Mirko', 'Mirić', 4);
_SQL_;

	// Razdvoji pojedine SQL naredbe iz varijable $sql i izvrši jednu po jednu.
	foreach( explode( "\n", trim($sql) ) as $q )
		$db->exec( trim($q) );

	die( 'Stvorio sam SQLite bazu.' );
}
else {
	// Ispiši sve podatke iz SQLite baze, koristeći prepared statement.
	$st = $db->prepare( 'SELECT JMBAG, ime, prezime, ocjena FROM studenti' );
	$st->execute(); //nema uvjeta, znači *

	
	echo '<table><tr><th>JMBAG</th><th>Ime</th><th>Prezime</th><th>Ocjena</th></tr>';
	while( $row = $st->fetch() )
		echo '<tr><td>'  . $row['JMBAG'] . 
		     '</td><td>' . $row['ime'] .
		     '</td><td>' . $row['prezime'] .
		     '</td><td>' . $row['ocjena'] .
		     '</td></tr>';
	echo '</table>';
}
?>
</body>
</html>


