<?php
include_once 'funkcije.php';
include_once 'podaci.php';

session_start();

if( verifyLogin() )
	$_SESSION['login'] = $_POST['username'] . ',' . md5( $_POST['username'] . $secret_word );

unset( $username );
if( isset( $_SESSION['login'] ) ) {
	list( $c_username, $cookie_hash ) = explode( ',' , $_SESSION['login'] );
	if( md5( $c_username . $secret_word ) == $cookie_hash )
		$username = $c_username;
	else
		echo "Poslan je pokvareni kolačić!" ;
}

if( isset( $username ) && isset( $_POST['logout'] ) ) {
	session_unset();
	session_destroy();
	unset( $username );
}
?>



<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
		<title>Rezervacija</title>
		<link rel="stylesheet" href="css/style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    	<title>jsDatePick Javascript example</title>
    	<link rel="stylesheet" type="text/css" media="all"
        href="jsDatePick_ltr.min.css" />
    	<script type="text/javascript" src="jsDatePick.min.1.3.js"></script>
    	<script src="jquery-2.1.4.min.js"></script>
    	<link rel="stylesheet" type="text/css" href = "css/stil.css">
</head>
<body>

<?php

	if( isset( $username ) ) {

		echo "Dobro došli, $username.<br />"; ?>
		<form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			<input type="hidden" name="logout">
			<input type="submit" value="Log Out">
		</form>
		<div id="calendar"></div>

    	<div id="predavaonice" align="center">
       
    	</div>
    	<h2 id = "tekst" align = "center"> </h2>
    	<table id="raspored" align="center" style="visibility: hidden">
    	</table>

    	<script type="text/javascript">
        	$(document).ready(function() {
            	var date, room;
             	var pred = "<table id = 'sobe'><tr><td>001</td><td>002</td><td>003</td></tr><tr><td>004</td><td>005</td><td>006</td></tr><tr><td>110</td><td>201</td><td>A101</td></tr></table>";
           	 	globalObject = new JsDatePick({
                	useMode: 1,
                	isStripped: true,
                	target: "calendar"
            	});

            	globalObject.setOnSelectedDelegate(function() {
            		$("#raspored").text(""); //obrise nam raspored prilikom klika na datum
            		$("#tekst").text(""); //obrsi što piše
                	var obj = globalObject.getSelectedDay();
                	date = obj.day + "." + obj.month + "." + obj.year + ".";
                	var predavaonice = document.getElementById("predavaonice");
                	predavaonice.innerHTML = pred;   //stvori nam tablicu predavaonica      
                
               	 	tds = document.getElementsByTagName("td");
                	for (var i = 0; i < tds.length; ++i)
                    	tds[i].onclick = function() {  
                        	room = this.innerHTML;
                        
                        	var fil = {datum: date, predavaonica: room};
           
                        	document.getElementById("sobe").remove();  //obrise nam listu predavaonica kada kliknemo na njega.
                        	$.ajax("raspored.php", {
                            	type: "POST",
                            	contentType: "application/json",
                            	data: JSON.stringify(fil),
                            	success: function(data) {
									if( typeof( data ) === "string" ) {
										alert( data);
										return;
									} 
									$("#tekst").text("Raspored predavaonice "+ room + " za datum: "+ date);
                                	$("#raspored").text("");
                                	$("#raspored").append(
                                    	"<tr><th>SAT</th>" +
                                   	 	"<th>IME KOLEGIJA</th>" +
                                    	"<th>PREDAVAČ</th>" +
                                    	"<th>DOZVOLA MIJENJANJA</th></tr>");
          
                                	var j = 0;
                                	for(var i = 8; i < 20; i++){

                                    	if(j < data.length && data[j].SAT === i.toString()){ // i je tip number, a data[j].sat je string
                                        	if( data[j].DOZVOLA == 1 )
                                        	{ 
                                            	$("#raspored").append(
                                            	"<tr><td>" + data[j].SAT + " - " + (Number(data[j].SAT) + 1)+ 
                                            	"</td><td>" + data[j].IME_KOLEGIJA +
                                            	"</td><td>" + data[j].PREDAVAC +
                                            	"</td><td>" + "<input type = 'button' value = 'poništi rezervaciju'>" +
                                            	"</td></tr>");
                                        	}
                                        	else
                                        	{
                                            	$("#raspored").append(
                                            	"<tr><td>" + data[j].SAT + " - " + (Number(data[j].SAT) + 1)+ 
                                            	"</td><td>" + data[j].IME_KOLEGIJA +
                                            	"</td><td>" + data[j].PREDAVAC +
                                            	"</td><td>" + 
                                            	"" +
                                            	"</td></tr>"); 
                                        	}
                                        	j++

                                    	}
                                    	else
                                    	{
                                        	$("#raspored").append(
                                         	"<tr><td>" + i + " - " + (i+1)+ 
                                         	"</td><td>" + ""+
                                         	"</td><td>" + ""+
                                         	"</td><td>" + "<input type = 'button' value = 'rezerviraj'>"+
                                         	"</td></tr>"); 
                                    	}
                                 
                                	}

                                	document.getElementById("raspored").style.visibility = "visible";
                            	}
                        	});
                    	}; 
            	});
        	});
    	</script>
		<?php
	} 
	else {
		?>
	<div class='info'>
    <h1>Registracija korisnika</h1>
	</div>
		<div class='form aniamted bounceIn'>
 		 <div class='login'>
 		   <h2>Prijavite se</h2>
 		   <form method = 'post' action = "login.php">
 		     	<input placeholder = 'Korisničko ime' type = 'text', name = "username">
		      	<input placeholder = 'Lozinka' type = "password", name = "password">
      			<button type="submit">Login</button>
		    </form>
		    <button id = "pogledaj" >Pogledaj raspored</button>
  	</div>
	</div>
		<?php
	}
?>
<script>
	var pogledaj = document.getElementById("pogledaj");
	pogledaj.onclick = function(){
		window.location.href = 'http://localhost/~jmilasi/projekt/samo_pregledaj.html';
	}
</script>
</body>
</html>


