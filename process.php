<?php
	include_once "chasm.php";
	include_once "parse.php";
	include_once "fileio.php";
	
	if ( empty($_REQUEST) ) {		
		$_REQUEST = Chasm_Input_Parser::debugData();
	}  

	$req = $_REQUEST;
	
	// create a temporary directory by getting a temp file, and then 
	//  using that name to create a directory
	$tmpName = tempnam( sys_get_temp_dir(), "" );
	unlink( $tmpName );
	mkdir( $tmpName, "600", TRUE ); 
	
	$tmpDir = $tmpName . "/";
	
	$filePrefix =  Chasm_Steering::safeFileName( 
			$req[ Chasm_Input_Parser::INFO ][ Chasm_Input_Parser::NAME ] );
	
	$files = array();
	
	// generate geometry file
	$geometryFile = $filePrefix . Chasm_Steering::GEOMETRY_FILE_SUFFIX;
	Chasm_Geometry::generateFile( $req, fopen( $tmpDir . $geometryFile, "w" ) );
	array_push( $files, $geometryFile );
	
	// generate soils file
	$soilsFile = $filePrefix . Chasm_Steering::SOILS_FILE_SUFFIX;
	Chasm_Soils::generateSoilsDatabase( $req, fopen( $tmpDir . $soilsFile, "w" ) );
	array_push( $files, $soilsFile );
	
	// generate stability file
	$stabilityFile = $filePrefix . Chasm_Steering::STABILITY_FILE_SUFFIX;
	Chasm_Stability::generateStabilityFile( $req, fopen( $tmpDir . $stabilityFile, "w" ) );
	array_push( $files, $stabilityFile );
	
	// Not generating reinforcement, vegetation, or stochastic file
//	$reinforcementFile = $filePrefix 
//		. Chasm_Steering::REINFORCEMENT_FILE_SUFFIX;
//	$vegetationFile = $filePrefix 
//		. Chasm_Steering::VEGETATION_FILE_SUFFIX;
//	$stochasticFile = $filePrefix 
//		. Chasm_Steering::STOCHASTIC_FILE_SUFFIX; 

	
	// for each rain fall
	$rains = $req[ Chasm_Input_Parser::RAIN ];
	for ( $rainIdx = 0; $rainIdx < count( $rains ); $rainIdx++ )
	{
		$rain = $rains[ $rainIdx ];
		
		// generate boundary file
		$boundaryFile =  $filePrefix . Chasm_Steering::BOUNDARY_FILE_PREFIX 
			. $rain[ Chasm_Input_Parser::RAIN_FREQUENCY ] 
			. Chasm_Steering::BOUNDARY_FILE_SUFFIX;		
		Chasm_Boundary_Conditions::generateBoundaryConditions( $req, $rainIdx,
			 fopen( $tmpDir . $boundaryFile, "w" ) );
		array_push( $files, $boundaryFile );
		
		// generate steering file
		$steeringFile = $filePrefix . Chasm_Steering::BOUNDARY_FILE_PREFIX 
			. $rain[ Chasm_Input_Parser::RAIN_FREQUENCY ] 
			. Chasm_Steering::STEERING_FILE_SUFFIX;
		Chasm_Steering::generateSteering( $req, $rainIdx, fopen( $tmpDir . $steeringFile, "w" ) );
		array_push( $files, $steeringFile );
	}
	
	// need to zip files
	$zipFile = $filePrefix . ".zip";
	
	Chasm_File_IO::zipFiles( $tmpDir, $files, $zipFile );
	Chasm_File_IO::streamFile( $zipFile, $filePrefix . ".zip");
?>