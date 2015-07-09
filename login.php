<?php
include_once "funkcije.php";

session_start();

if (verifyLogin())
    $_SESSION["login"] = $_POST["username"] . "," . md5($_POST["username"] . $secret_word);

unset($username);
if (isset($_SESSION["login"])) {
    list($c_username, $cookie_hash) = explode(",", $_SESSION["login"]);
    if (md5($c_username . $secret_word) == $cookie_hash)
        $username = $c_username;
    else
        echo "Poslan je pokvareni kolačić!";
}

if (isset($username) && isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    unset($username);
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rezervacija</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/stil.css" />
    <link rel="stylesheet" type="text/css" media="all" href="jsDatePick_ltr.min.css" />
    <script type="text/javascript" src="jsDatePick.min.1.3.js"></script>
    <script type="text/javascript" src="jquery-2.1.4.min.js"></script>
</head>

<body>
<?php
if (isset($username)) {
	$st = DB::get()->prepare("SELECT PREDAVAC, ADMIN FROM KORISNIK WHERE USERNAME=:username");
	$error = DB::get()->errorInfo();
	if (isset($error[2])) {  
		echo "DB::get()->prepare error: " . $error[2];
		return false;
	}

	$st->execute(array("username" => $username));
	$tko = $st->fetch();
	$_SESSION["tko"] = $tko["PREDAVAC"];
	$_SESSION["admin"] = $tko["ADMIN"];
	echo "Korisnik: " . $_SESSION["tko"]. "<br />"; ?>

	<form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
		<input type="hidden" name="logout" />
		<input type="submit" value="Log Out" />
	</form>

	<input type="button" id="povratak" value="povratak" style="visibility: hidden" />
	<div id="calendar"></div>
	<div id="predavaonice" align="center"></div>
	<h2 id="tekst" align="center"></h2>
	<table id="raspored" align="center" style="visibility: hidden"></table>

    <script type="text/javascript">
        $(document).ready(function() {
            var date, room;
            var pred = "";

			function ispisi_pred(podatci) {
				pred += "<table id=sobe>";
				var koliko = 0;
				
				while (koliko < podatci.length) {
					pred += "<tr>";
					for (var j = 0; j < 3; ++j) {
						if (koliko < podatci.length)
							pred += "<td>" + podatci[koliko].BROJ + "</td>";
						else
							pred += "<td></td>";

						++koliko;
					}
					pred += "<tr/>";
				}
				
				pred += "</table>";
			}

			var fill_pred = "";

			$.ajax("predavaonice.php", {
				type: "POST",
				contentType: "application/json",
				data: JSON.stringify(fill_pred),
				success: function(data) {
					if (typeof(data) === "string") {
						alert(data);
						return;
					}
					else {
						ispisi_pred(data);
					}
				}
			});

			var admin = <?php echo json_encode($_SESSION["admin"]); ?>;
			var ime_predavaca = <?php echo json_encode($_SESSION["tko"]); ?>;

            globalObject = new JsDatePick({
                useMode: 1,
                isStripped: true,
                target: "calendar"
            });

			function ispis(data) {
				$("#tekst").text("Raspored predavaonice " + room + " za " + date);
				$("#raspored").text("");
				$("#raspored").append(
					"<tr><th>SAT</th>" +
					"<th>IME KOLEGIJA</th>" +
					"<th>PREDAVAČ</th>" +
					"<th>Radnja</th></tr>");
				
				var i = 8, j = 0;
				var ponovi = 0;
				
				while (i < 20) {
					// i je number, a data[j].sat je string
					if (j < data.length && data[j].SAT === i.toString()) { 
						var pamti = data[j].IME_KOLEGIJA;
						var tmp_predavac = data[j].PREDAVAC;
						while (j+ponovi < data.length && pamti == data[j+ponovi].IME_KOLEGIJA && tmp_predavac == data[j+ponovi].PREDAVAC) {
							ponovi++;
							i++;
						}

						if (data[j].DOZVOLA == 1 ) {
							$("#raspored").append(
								"<tr><td>" + data[j].SAT + " - " + i.toString() + 
								"</td><td>" + data[j].IME_KOLEGIJA +
								"</td><td>" + data[j].PREDAVAC +
								"</td><td>" + "<input class='izmjene' name='ponisti' type='button' value='Poništi' />" +
								"</td></tr>");
						}

						else {
							$("#raspored").append(
								"<tr><td>" + data[j].SAT + " - " + i.toString() + 
								"</td><td>" + data[j].IME_KOLEGIJA +
								"</td><td>" + data[j].PREDAVAC +
								"</td><td>" + "(ništa)" +
								"</td></tr>");
						}

						j += ponovi;
						ponovi = 0;
					}

					else {
						if (admin == '1') {
							$("#raspored").append(
								"<tr><td>" + i + " - " + (i+1) + 
								"</td><td>" + "<input class='izmjene' name='kolegij' type='text' placeholder='prostor za unos' />"+
								"</td><td>" + "<input class='izmjene' name='predavac' type='text' placeholder='prostor za unos' />"+
								"</td><td>" + "<input class='izmjene' name='rezerviraj' type='button' value='Rezerviraj' />"+
								"</td></tr>");
						}
						else {
							$("#raspored").append(
								"<tr><td>" + i + " - " + (i+1) + 
								"</td><td>" + "<input class='izmjene' name='kolegij' type='text' placeholder='prostor za unos' />"+
								"</td><td>" + "<input class='izmjene' name='predavac' type='text' value='" + ime_predavaca + "' />" +
								"</td><td>" + "<input class='izmjene' name='rezerviraj' type='button' value='Rezerviraj' />"+
								"</td></tr>");							
						}
						
						i++;
					}
				} // kraj while-petlje

				document.getElementById("raspored").style.visibility = "visible";
				
				// rezervacija predavaonice
				var rez = document.getElementsByName("rezerviraj");
				for (var i = 0; i < rez.length; ++i) {
					rez[i].onclick = function(event) {
						var td = event.target.parentNode;
						var tr = td.parentNode;
						var td1 = tr.children[0]; 
						var td2 = tr.children[1]; 
						var td3 = tr.children[2];
						var _sat = td1.innerHTML;
						_sat = _sat.substring(0, _sat.indexOf(" "));
						var _kolegij = td2.children[0].value;
						var _predavac = td3.children[0].value;

						var fil_rez = {
						    datum: date,
						    predavaonica: room,
						    sat: _sat,
						    kolegij: _kolegij,
						    predavac: _predavac,
						};

						$.ajax("rezerviraj.php", {
							type: "POST",
							contentType: "application/json",
							data: JSON.stringify(fil_rez),
							success: function(data) {
								if (typeof(data) === "string") {
									alert(data);
									return;
								}
								else {
									ispis(data);
									return;
								}
							}
						}); // kraj ajax-a za rezervaciju
					} // kraj onclick-funkcije kod rezervacije
				} // kraj for-petlje kod rezervacije
				
				// poništavanje rezervacije predavaonice
				var pon = document.getElementsByName("ponisti");
				for (var i = 0; i < pon.length; ++i) {
					pon[i].onclick = function(event) {
						var td = event.target.parentNode;
						var tr = td.parentNode;
						var td1 = tr.children[0]; 
						var td2 = tr.children[1]; 
						var td3 = tr.children[2];
						var _sat = td1.innerHTML;
						_sat = _sat.substring(0, _sat.indexOf(" "));
						var _kolegij = td2.innerHTML;
						var _predavac = td3.innerHTML;

						var fil_pon = {
							datum: date,
							predavaonica: room,
							sat: _sat,
							kolegij: _kolegij,
							predavac: _predavac,
						};

						$.ajax("ponisti.php", {
							type: "POST",
							contentType: "application/json",
							data: JSON.stringify(fil_pon),
							success: function(data) {
								if (typeof(data) === "string") {
									alert(data);
									return;
								}
								else {
									ispis(data);
									return;
								}
							} // kraj success-funkcije kod ajax-a za poništavanje
						}); // kraj ajax-a za poništavanje
					} // kraj onclick-funkcije kod poništavanja
				} // kraj for-petlje kod poništavanja
			} // kraj funkcije ispis()

			function obrada(date, room) {
				var fil_obrada = {datum: date, predavaonica: room};
				$.ajax("obrada.php", {
					type: "POST",
					contentType: "application/json",
					data: JSON.stringify(fil_obrada),
					success: function(data) {
						if (data !== "ok")
							alert("Neuspješno brisanje iz tablice OBRADA!");
						return;
					}
				});
			} // kraj funkcije obrada()

			function klik() {
				$("#raspored").text(""); // obriši raspored prilikom klika na datum
				$("#tekst").text(""); // obriši što piše

				var obj = globalObject.getSelectedDay();
				date = obj.day + "." + obj.month + "." + obj.year + ".";

				var predavaonice = document.getElementById("predavaonice");
				predavaonice.innerHTML = pred; // tablica predavaonica      

				tds = document.getElementsByTagName("td");
				for (var i = 0; i < tds.length; ++i) {
					if (tds[i].innerHTML === "")
						continue;
					tds[i].onclick = function() {
						room = this.innerHTML;    
						document.getElementById("povratak").style.visibility = "visible";                 
						document.getElementById("povratak").onclick = function() {
							obrada(date, room);
							document.getElementById("povratak").style.visibility = "hidden";
							$("#raspored").text(""); // obriši raspored prilikom klika na datum
							$("#tekst").text(""); // obriši što piše
							document.getElementById("calendar").style.visibility = "visible";
							return;
						}

						var fil = {datum: date, predavaonica: room};
						document.getElementById("sobe").remove(); // obriši listu predavaonica prilikom klika na nju
						document.getElementById("calendar").style.visibility = "hidden";
						
						$.ajax("raspored.php", {
							type: "POST",
							contentType: "application/json",
							data: JSON.stringify(fil),
							success: function(data) {
								if (typeof(data) === "string") {
									alert(data);
									document.getElementById("povratak").style.visibility = "hidden";
									document.getElementById("calendar").style.visibility = "visible";
									return;
								}
								else {
									ispis(data);
									return;
								}
							}
						}); // kraj ajaxa za raspored
					}; // kraj onclick-funkcije za td-ove 
				} // kraj for-petlje po td-ovima
			} // kraj funkcije klik()

			globalObject.setOnSelectedDelegate(function() {
				klik();
				window.addEventListener("beforeunload", function() {
					obrada(date, room);
				}, false);
				window.addEventListener("unload", function() {
					obrada(date, room);
				}, false);
				window.addEventListener("popstate", function() {
  					obrada(data, room);	
				}, false);
				window.addEventListener("hashchange", function(){
					obrada(data, room);
				}, false);
			}); // kraj globalObject-a
		}); // kraj documentReady-funkcije
	</script>
	<?php
} 

else { ?>
	<div class="info">
		<h1>Aplikacija</h1>
	</div>
	<div class="form aniamted bounceIn">
		<div class="login">
			<h2>Podatci za prijavu</h2>
			<form method="post" action="login.php">
				<input placeholder="Username" type="text", name="username" />
				<input placeholder="Password" type="password", name="password" />
				<button type="submit">Prijava</button>
			</form>
			<button id="pogledaj">Pogledaj raspored</button>
		</div>
	</div>
	<?php
}
?>

<script>
	var pogledaj = document.getElementById("pogledaj");
	pogledaj.onclick = function(){
		window.location.href = "http://192.168.89.245/~iposavc/projekt/samo_pregledaj.html";
	}
</script>
</body>
</html>