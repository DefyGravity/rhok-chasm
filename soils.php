<html>
<head>
	<title>Soils Database File</title>
	<script>
		var visible = false;
		function toggleDebug() {
			alert('Toggling debugging data');
			if (visible) {
				document.getElementById('data').style.display = "none";
			} else {
				document.getElementById('data').style.display = "block";
			}
			visible = !visible;
		}
	</script>
</head>
<body ondblclick="toggleDebug()">
<pre>
<?php
	include_once "chasm.php";
	include_once "parse.php";

	if ( empty($_REQUEST) ) {		
		$_REQUEST = Chasm_Input_Parser::debugData();
		echo "<script>alert(\"No data supplied. Using testcase 06 data\");</script>";
	}  

	echo "<div id=\"data\" style=\"display:none\">";
	print_r($_REQUEST);
	echo "<hr/>";
	echo "</div>";	

	Chasm_Soils::generateSoilsDatabase( $_REQUEST, fopen( "php://output", "w"), "<br/>" );
?>
</pre>
</body>
</html>