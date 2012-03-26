<?php
include_once "columns.php";
include_once "trig.php";
include_once "profile.php";

function testSlope06()
{
    $profile = array ( array(Chasm_Profile_Parser::H => 0, 
                              Chasm_Profile_Parser::L => 8,
                              Chasm_Profile_Parser::THETA => 0),
                        array(Chasm_Profile_Parser::H => 9, 
                              Chasm_Profile_Parser::L => 24.03,
                              Chasm_Profile_Parser::THETA => 22),
                        array(Chasm_Profile_Parser::H => 17, 
                              Chasm_Profile_Parser::L => 34,
                              Chasm_Profile_Parser::THETA => 30),
                        array(Chasm_Profile_Parser::H => 11, 
                              Chasm_Profile_Parser::L => 21.36,
                              Chasm_Profile_Parser::THETA => 31),
                        array(Chasm_Profile_Parser::H => 10, 
                              Chasm_Profile_Parser::L => 24.59,
                              Chasm_Profile_Parser::THETA => 24),
                        array(Chasm_Profile_Parser::H => 1, 
                              Chasm_Profile_Parser::L => 1.56,
                              Chasm_Profile_Parser::THETA => 40),
                        array(Chasm_Profile_Parser::H => 0, 
                              Chasm_Profile_Parser::L => 8,
                              Chasm_Profile_Parser::THETA => 0),
                    );
    $segment = generateXYPoints( $profile );
}

function testXYPoints() {
	$testData = array(	array(Chasm_Profile_Parser::H => 3, 
                              Chasm_Profile_Parser::L => 5,
                              Chasm_Profile_Parser::THETA => 36.869897645844021296855612559093),
						array(Chasm_Profile_Parser::H => 10, 	
                              Chasm_Profile_Parser::L => 26,
                              Chasm_Profile_Parser::THETA => 22.61986494804042617294901087668),
						array(Chasm_Profile_Parser::H => 7,
                              Chasm_Profile_Parser::L => 25,
                              Chasm_Profile_Parser::THETA => 16.260204708311957406288774881813),
						array(Chasm_Profile_Parser::H => 5,
                              Chasm_Profile_Parser::L => 13,
                              Chasm_Profile_Parser::THETA => 22.61986494804042617294901087668),	);
						
	$expectedValues = array( 	array(0, 	25),
									array(4,	22),
									array(28,	12),
									array(52,	5),
									array(64,	0)	);
						
	$points = generateXYPoints( $testData );	
	
	echo "<h1>Plot Points</h1>";
	echo "<table border=1>";
	echo "<tr><th>Expected x</th><th>Expected y</th><th>Computed x</th><th>Computed y</th></tr>";
	for ($i=0; $i<count($points); $i++) {	
			echo "<tr>";
			echo "<td>" . $expectedValues[$i][Chasm_Profile_Parser::X] . "</td><td>" . $expectedValues[$i][Chasm_Profile_Parser::Y] . "</td>";
			echo "<td>" . $points[$i][Chasm_Profile_Parser::X] . "</td><td>" . $points[$i][Chasm_Profile_Parser::Y] . "</td>";
			echo "</tr>";
	}
	echo "</table>";
		
}

function testWater() {
	$water =	array(
					array(0, 25),
					array(4, 22),
					array(28, 12),
					array(52, 5),
					array(64, 0)				
				);
	$sat = generateWaterColumns( 64, $water );	
}

function testGenerateColumn() {

	
	$segment = 	array(
					array(0, 75),
					array(4, 72),
					array(28, 62),
					array(52, 55),
					array(64, 50)
				);
				
	$soil1 = 	array(
					array(0, 25),
					array(4, 22),
					array(28, 12),
					array(52, 5),
					array(64, 0),
					"soiltype"=>"1"
				);
	
	$soil2 = 	array(
					array(0, 0),
					array(4, 0),
					array(28, 0),
					array(52, 0),
					array(64, 0),
					"soiltype"=>"2"
				);
				

	$bottom = 	array(
					array(0, -1),
					array(4, -1),
					array(28, -1),
					array(52, -1),
					array(64, -1),
					"soiltype"=>"10"
				);
	
	$column = array();
	
	for ($i = 0; $i<64; $i++) {
		array_push($column, generateColumn($i+0.5, 75, $segment, $soil1, $soil2, $bottom, $bottom));
	}
	
	echo "<h1>generateColumn</h1>";
	echo "<hr/>";
	
	echo "<table border=1>";
	for ($i=0; $i<count($column); $i++) {
		echo "<tr>";
		echo "<td>" . (count($column) - $i) . "</td>";
		for ($j=0; $j<count($column[$i]); $j++) {
			echo "<td>" . $column[$i][$j] . "</td>";
		}
		echo "</tr>";
	}
	
	echo "</table>";
}

function testGetCellValue() {
	
	$segment = 	array(
					array(0, 75),
					array(4, 72),
					array(28, 62),
					array(52, 55),
					array(64, 50)
				);
				
	$soil1 = 	array(
					array(0, 25),
					array(4, 22),
					array(28, 12),
					array(52, 5),
					array(64, 0),
					"soiltype"=>"1"
				);
	
	$soil2 = 	array(
					array(0, 0),
					array(4, 0),
					array(28, 0),
					array(52, 0),
					array(64, 0),
					"soiltype"=>"2"
				);
				
	$bottom = 	array(
					array(0, -1),
					array(4, -1),
					array(28, -1),
					array(52, -1),
					array(64, -1),
					"soiltype"=>"10"
				);
	
	$test = 	array(
					array(1, 75, 9),
					array(2, 51, 1),
					array(5, 72, 9),
					array(6, 60, 1),
					array(29, 65, 9),
					array(30, 53, 1),
					array(53, 57, 9),
					array(54, 51, 1),
					
					array(1, 25, 1),
					array(2, 1, 2),
					array(5, 22, 1),
					array(6, 10, 2),
					array(29, 15, 1),
					array(30, 3, 2),
					array(53, 7, 1),
					array(54, 1, 2)
				);


	echo "<h1>getCellValue</h1>";
	echo "<table border=1>";
	echo "<tr><th>X</th><th>Y</th><th>Expected Value</th><th>Computed Value</th><th>Pass/Fail</th></tr>";
	for ($i=0; $i<count($test); $i++) {
		$line_seg_idx = findSegment( $test[$i][X], $segment );
		$cv = getCellValue( $test[$i][X], $test[$i][Y], 
					$segment, $soil1, 
					$soil2, $bottom, $bottom );
					
		echo "<tr><td>" . $test[$i][X] . "</td><td>" . $test[$i][Y] . "</td><td>" . $test[$i][2] . "</td><td>" .
						$cv .
						 "</td><td>". ( $cv == $test[$i][2] ? "" : "FAIL") . "</td></tr>";
	}
	echo "</table>";
	
}

function testIsAboveLine() {
	
	$segment = 	array(
					array(0, 25),
					array(4, 22),
					array(28, 12),
					array(52, 5),
					array(64, 0)
				);
	$test = 	array(
					array(1, 25, TRUE),
					array(2, 1, FALSE),
					array(5, 22, TRUE),
					array(6, 10, FALSE),
					array(29, 15, TRUE),
					array(30, 3, FALSE),
					array(53, 7, TRUE),
					array(54, 1, FALSE)
				);

	echo "<h1>isAboveLine</h1>";
	echo "<table border=1>";
	echo "<tr><th>X</th><th>Y</th><th>Expected Value</th><th>Computed Value</th><th>Pass/Fail</th></tr>";
	for ($i=0; $i<count($test); $i++) {
		$line_seg_idx = findSegment( $test[$i][X], $segment );
		$cv = isAboveLine( $test[$i][X], $test[$i][Y], 
					$segment[$line_seg_idx-1][X], $segment[$line_seg_idx-1][Y], 
					$segment[$line_seg_idx][X], $segment[$line_seg_idx][Y] );
					
		echo "<tr><td>" . $test[$i][X] . "</td><td>" . $test[$i][Y] . "</td><td>" . $test[$i][2] . "</td><td>" .
						$cv .
						 "</td><td>". ( $cv == $test[$i][2] ? "" : "FAIL") . "</td></tr>";
	}
	echo "</table>";
	
}

function testFindSegment() {
	
	$segment = 	array(
					array(0, 25),
					array(4, 22),
					array(28, 12),
					array(52, 5),
					array(64, 0)
				);
	

	echo "<h1>findSegment</h1>";
	echo "<table border=1>";
	echo "<tr><th>X</th><th>Expected Value</th><th>Computed Value</th><th>Pass/Fail</th></tr>";
	for ($i=0.5; $i<64; $i+=1.0){
		$ev = 0;
		if ($i <4)
			$ev = 1;
		else if ($i<28)
			$ev = 2;
		else if ($i<52)
			$ev = 3;
		else if ($i<64)
			$ev = 4;
		
		$cv = findSegment( $i, $segment );
		echo "<tr><td>" . $i . "</td><td>" . $ev . "</td><td>" . $cv . "</td><td>". ($ev == $cv ? "" : "FAIL") . "</td></tr>";
	}
	echo "</table>";
	
}


function testGenerateCells() {

	$segment = 	array(
					array(0, 75),
					array(4, 72),
					array(28, 62),
					array(52, 55),
					array(64, 50)
				);
				
	$soil1 = 	array(
					array(0, 25),
					array(4, 22),
					array(28, 12),
					array(52, 5),
					array(64, 0),
					"soiltype"=>"1"
				);
	
	$soil2 = 	array(
					array(0, 0),
					array(4, 0),
					array(28, 0),
					array(52, 0),
					array(64, 0),
					"soiltype"=>"2"
				);
				
	$water =	array(
					array(0, 25),
					array(4, 22),
					array(28, 12),
					array(52, 5),
					array(64, 0)					
				);

	$bottom = 	array(
					array(0, -1),
					array(4, -1),
					array(28, -1),
					array(52, -1),
					array(64, -1),
					"soiltype"=>"10"
				);
		

	$cells = generateCells($segment, $soil1, $soil2, $bottom, $bottom );

/*
	echo "<h1>generateCells</h1>";
	echo "<hr/>";

	echo "<table border=1>";
	for ($i=0; $i<count($cells); $i++) {
		echo "<tr>";
		echo "<td>" . (75 - $i - 0.5) . "</td>";
		for ($j=0; $j<count($cells[$i]); $j++) {
			echo "<td style=\"width: 2em;\">" . $cells[$i][$j] . "</td>";
		}
		echo "</tr>";
	}
	echo "<td>&nbsp;</td>";
	for ($j=1; $j<=count($cells[0]); $j++) {
			echo "<td>" . $j . "</td>";
	}
	
	echo "</table>";
	
	echo "<hr/>";
	*/
	$water_columns = generateWaterColumns( 64, $water );
	generateFile( $cells, $water_columns );
}

//testXYPoints();

//testFindSegment();
//testIsAboveLine();
//testGetCellValue();
//testGenerateColumn();

//testWater();
testGenerateCells();
?>