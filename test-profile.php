<?php
include_once "trig.php";
include_once "profile.php";

function testSlope06()
{
    $profileGeometry = array ( array(Chasm_Profile_Parser::H => 0, 
                              Chasm_Profile_Parser::L => 8,
                              Chasm_Profile_Parser::THETA => 0),
                        array(Chasm_Profile_Parser::H => 9, 
                              Chasm_Profile_Parser::L => 24.0252044629861,
                              Chasm_Profile_Parser::THETA => 22),
                        array(Chasm_Profile_Parser::H => 17, 
                              Chasm_Profile_Parser::L => 34,
                              Chasm_Profile_Parser::THETA => 30),
                        array(Chasm_Profile_Parser::H => 11, 
                              Chasm_Profile_Parser::L => 21.3576442905139,
                              Chasm_Profile_Parser::THETA => 31),
                        array(Chasm_Profile_Parser::H => 10, 
                              Chasm_Profile_Parser::L => 24.5859333557424,
                              Chasm_Profile_Parser::THETA => 24),
                        array(Chasm_Profile_Parser::H => 1, 
                              Chasm_Profile_Parser::L => 1.55572382686041,
                              Chasm_Profile_Parser::THETA => 40),
                        array(Chasm_Profile_Parser::H => 0, 
                              Chasm_Profile_Parser::L => 8,
                              Chasm_Profile_Parser::THETA => 0),
                       array(Chasm_Profile_Parser::H => 0, 
                              Chasm_Profile_Parser::L => 16.4519761570364,
                              Chasm_Profile_Parser::THETA => 0),
                       array(Chasm_Profile_Parser::H => 12, 
                              Chasm_Profile_Parser::L => 0,
                              Chasm_Profile_Parser::THETA => 90),       
                    );
                    
	$expectedProfileXY = 	array( 
								array( 0, 60 ),
								array( 8, 60 ),
								array( 30.28, 51 ),
								array( 59.72, 34 ),
								array( 78.03, 23 ),
								array( 100.49, 13 ),
								array( 101.68, 12 ),
								array( 109.68, 12 ),
								array( 126.13, 12 ),
								array( 126.13, 0 ),
							);    
							
    $soil1Depths = array(3, 5, 6, 7, 8, 10, 9.5, 11, 14);
    $expectedSoil1XY = 	array(
    						array( 0, 57 ),
    						array( 8, 55 ),
    						array( 30.28, 45 ),
    						array( 59.72, 27 ),
    						array( 78.03, 15 ),
    						array( 100.49, 3 ),
    						array( 101.68, 2.5 ),
    						array( 109.68, 1 ),
    						array( 126.13, -2 ),
    					);
    									
    $soil2Depths = array(4, 6, 8, 10, 12, 14, 13.5, 16, 20);
    $expectedSoil2XY = 	array(
    						array( 0, 56 ),
    						array( 8, 54 ),
    						array( 30.28, 43 ),
    						array( 59.72, 24 ),
    						array( 78.03, 11 ),
    						array( 100.49, -1 ),
    						array( 101.68, -1.5 ),
    						array( 109.68, -4 ),
    						array( 126.13, -8 ),    						
    					);
    
    $bottomDepths = array(105, 105, 105, 105, 105, 105, 105, 105, 105);
    
	$waterDepths = array(20, 20, 15, 8, 4, 4, 3, 3, 3);
	$expectedWaterXY = 	array(
    						array( 0, 40 ),
    						array( 8, 40 ),
    						array( 30.28, 36 ),
    						array( 59.72, 26 ),
    						array( 78.03, 19 ),
    						array( 100.49, 9 ),
    						array( 101.68, 9 ),
    						array( 109.68, 9 ),
    						array( 126.13, 9 ),    						
    					);
	
    $segment = Chasm_Profile_Parser::generateXYPoints( $profileGeometry );
//    comparePoints( $segment, $expectedProfileXY, "Profile" );
    
    $soil1 = Chasm_Profile_Parser::generateLayerXYPoints( $segment, $soil1Depths );
    $soil1["soiltype"] = 0;
    
    comparePoints( $soil1, $expectedSoil1XY, "Soil 1");
    
    $soil2 = Chasm_Profile_Parser::generateLayerXYPoints( $segment, $soil2Depths );
    $soil2["soiltype"] = 1;
    
    comparePoints( $soil2, $expectedSoil2XY, "Soil 2" );
    
    $bottom = Chasm_Profile_Parser::generateLayerXYPoints( $segment, $bottomDepths );
    $bottom["soiltype"] = 2;
    
    $water = Chasm_Profile_Parser::generateLayerXYPoints( $segment, $waterDepths );
    
    comparePoints( $water, $expectedWaterXY, "Water" );
    
    
    $cells = Chasm_Profile_Parser::generateCells($segment, $soil1, $soil2, $bottom, $bottom );

    $width = $segment[count($segment)-1][Chasm_Profile_Parser::X];
    
	$water_columns = Chasm_Profile_Parser::generateWaterColumns( $width, $water );
	Chasm_Profile_Parser::generateFile( $cells, $water_columns );    
}

testSlope06();

function comparePoints( $points, $expectedValues, $name="Plot Points" ) {
	echo "<h1>" . $name . "</h1>";
	echo "<table border=1>";
	echo "<tr>"
		. "<th>Expected x</th><th>Expected y</th>"
		. "<th>Computed x</th><th>Computed y</th>"
		. "<th>Pass/Fail X</th><th>Pass/Fail Y</th>"
		. "</tr>";
	for ($i=0; $i<count($expectedValues); $i++) {	
			echo "<tr>";
			echo "<td>" 
				. $expectedValues[$i][Chasm_Profile_Parser::X] 
				. "</td><td>" . $expectedValues[$i][Chasm_Profile_Parser::Y] 
				. "</td>";
			echo "<td>" 
				. round( $points[$i][Chasm_Profile_Parser::X], 2) . "</td><td>" 
				. round( $points[$i][Chasm_Profile_Parser::Y], 2) . "</td>";
			echo "<td>" 
				. ($expectedValues[$i][Chasm_Profile_Parser::X] == 
					round( $points[$i][Chasm_Profile_Parser::X], 2 ) ? "" : "FAIL" ) 
				. "</td><td>" 
				. ($expectedValues[$i][Chasm_Profile_Parser::Y] ==
					round( $points[$i][Chasm_Profile_Parser::Y], 2 ) ? "" : "FAIL" )
				. "</td>";				
			echo "</tr>";
	}
	echo "</table>";
}

function testXYPoints( $testData, $expectedValues ) {
	
	$points = Chasm_Profile_Parser::generateXYPoints( $testData );	
	
	comparePoints( $points, $expectedValues );
}

function testWater() {
	$water =	array(
					array(0, 25),
					array(4, 22),
					array(28, 12),
					array(52, 5),
					array(64, 0)				
				);
	$sat = Chasm_Profile_Parser::generateWaterColumns( 64, $water );	
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
		array_push($column, 
			Chasm_Profile_Parser::generateColumn(
				$i+0.5, 75, $segment, $soil1, $soil2, $bottom, $bottom));
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

function testGetCellValue( $segment, $soil1, $soil2, $soil3, $soil4, $test ) {
	


	echo "<h1>getCellValue</h1>";
	echo "<table border=1>";
	echo "<tr><th>X</th><th>Y</th><th>Expected Value</th><th>Computed Value</th><th>Pass/Fail</th></tr>";
	for ($i=0; $i<count($test); $i++) {
		$line_seg_idx = Chasm_Profile_Parser::findSegment( $test[$i][Chasm_Profile_Parser::X], $segment );
		$cv = Chasm_Profile_Parser::getCellValue( $test[$i][Chasm_Profile_Parser::X], $test[$i][Chasm_Profile_Parser::Y], 
					$segment, $soil1, 
					$soil2, $soil3, $soil4);
					
		echo "<tr><td>" . $test[$i][Chasm_Profile_Parser::X] 
			. "</td><td>" . $test[$i][Chasm_Profile_Parser::Y] 
			. "</td><td>" . $test[$i][2] . "</td><td>" 
			. $cv . "</td><td>"
			. ( $cv == $test[$i][2] ? "" : "FAIL") . "</td></tr>";
	}
	echo "</table>";
	
}

function testIsAboveLine( $segment, $test ) {
	

	echo "<h1>isAboveLine</h1>";
	echo "<table border=1>";
	echo "<tr><th>X</th><th>Y</th><th>Expected Value</th><th>Computed Value</th><th>Pass/Fail</th></tr>";
	for ($i=0; $i<count($test); $i++) {
		$line_seg_idx = Chasm_Profile_Parser::findSegment( $test[$i][Chasm_Profile_Parser::X], $segment );
		$cv = Chasm_Profile_Parser::isAboveLine( $test[$i][Chasm_Profile_Parser::X], $test[$i][Chasm_Profile_Parser::Y], 
					$segment[$line_seg_idx-1][Chasm_Profile_Parser::X], $segment[$line_seg_idx-1][Chasm_Profile_Parser::Y], 
					$segment[$line_seg_idx][Chasm_Profile_Parser::X], $segment[$line_seg_idx][Chasm_Profile_Parser::Y] );
					
		echo "<tr><td>" . $test[$i][Chasm_Profile_Parser::X] 
			. "</td><td>" . $test[$i][Chasm_Profile_Parser::Y] 
			. "</td><td>" . $test[$i][2] . "</td><td>" 
			. $cv . "</td><td>"
			. ( $cv == $test[$i][2] ? "" : "FAIL") . "</td></tr>";
	}
	echo "</table>";
	
}

function testFindSegment( $segment ) {	

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
		
		$cv = Chasm_Profile_Parser::findSegment( $i, $segment );
		echo "<tr><td>" . $i . "</td><td>" . $ev . "</td><td>" 
			. $cv . "</td><td>". ($ev == $cv ? "" : "FAIL") . "</td></tr>";
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
		

	$cells = Chasm_Profile_Parser::generateCells($segment, $soil1, $soil2, $bottom, $bottom );

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
	$water_columns = Chasm_Profile_Parser::generateWaterColumns( 64, $water );
	Chasm_Profile_Parser::generateFile( $cells, $water_columns );
}

//$xyTestData = array(	array(Chasm_Profile_Parser::H => 3, 
//                              Chasm_Profile_Parser::L => 5,
//                              Chasm_Profile_Parser::THETA => 36.869897645844021296855612559093),
//						array(Chasm_Profile_Parser::H => 10, 	
//                              Chasm_Profile_Parser::L => 26,
//                              Chasm_Profile_Parser::THETA => 22.61986494804042617294901087668),
//						array(Chasm_Profile_Parser::H => 7,
//                              Chasm_Profile_Parser::L => 25,
//                              Chasm_Profile_Parser::THETA => 16.260204708311957406288774881813),
//						array(Chasm_Profile_Parser::H => 5,
//                              Chasm_Profile_Parser::L => 13,
//                              Chasm_Profile_Parser::THETA => 22.61986494804042617294901087668),	);
//						
//$xyExpectedValues = array( 	array(0, 	25),
//									array(4,	22),
//									array(28,	12),
//									array(52,	5),
//									array(64,	0)	);				
//testXYPoints( $xyTestData, $xyExpectedValues );

//$testSegment = 	array(
//					array(0, 25),
//					array(4, 22),
//					array(28, 12),
//					array(52, 5),
//					array(64, 0)
//				);

//testFindSegment( $testSegment );

//$testAboveResult = 	array(
//						array(1, 25, TRUE),
//						array(2, 1, FALSE),
//						array(5, 22, TRUE),
//						array(6, 10, FALSE),
//						array(29, 15, TRUE),
//						array(30, 3, FALSE),
//						array(53, 7, TRUE),
//						array(54, 1, FALSE)
//					);
//testIsAboveLine( $testSegment, $testAboveResult );


//$testLineSegment = 	array(
//				array(0, 75),
//				array(4, 72),
//				array(28, 62),
//				array(52, 55),
//				array(64, 50)
//			);
//			
//$testSoil1 = 	array(
//				array(0, 25),
//				array(4, 22),
//				array(28, 12),
//				array(52, 5),
//				array(64, 0),
//				"soiltype"=>"1"
//			);
//
//$testSoil2 = 	array(
//				array(0, 0),
//				array(4, 0),
//				array(28, 0),
//				array(52, 0),
//				array(64, 0),
//				"soiltype"=>"2"
//			);
//			
//$testBottom = 	array(
//				array(0, -1),
//				array(4, -1),
//				array(28, -1),
//				array(52, -1),
//				array(64, -1),
//				"soiltype"=>"10"
//			);
//
//$testCellValueResult = 	array(
//				array(1, 75, 9),
//				array(2, 51, 1),
//				array(5, 72, 9),
//				array(6, 60, 1),
//				array(29, 65, 9),
//				array(30, 53, 1),
//				array(53, 57, 9),
//				array(54, 51, 1),
//				
//				array(1, 25, 1),
//				array(2, 1, 2),
//				array(5, 22, 1),
//				array(6, 10, 2),
//				array(29, 15, 1),
//				array(30, 3, 2),
//				array(53, 7, 1),
//				array(54, 1, 2)
//			);
//
//testGetCellValue( $testLineSegment, $testSoil1,
//	$testSoil2, $testBottom, $testBottom, $testCellValueResult );

//testGenerateColumn();

//testWater();

//testGenerateCells();

//testSlope06();
?>