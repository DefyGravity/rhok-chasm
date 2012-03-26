<?php 
	
	include_once "parse.php";
	include_once "profile.php";

	class Chasm_Stability
	{
		const ANALYSIS_METHOD = "Bishop";
		const X_GRID_SPACING = "1";
		const Y_GRID_SPACING = "1";
		const INITIAL_RADIUS_LENGTH = "5";
		const RADIUS_INCREASE = "0.5";
		
		public static function generateStabilityFile( $req, $outputStream, $newLine = "\n" )
		{
			$profileGeometry = $req[Chasm_Input_Parser::PROFILE];
		
			$segment = Chasm_Profile_Parser::generateXYPoints( $profileGeometry );
			
			fwrite( $outputStream, Chasm_Stability::ANALYSIS_METHOD . $newLine );
				
			// sanity check that there are enough coordinates
			if ( count( $segment ) < 4 )
			{
				die( "Insufficent data points");
			} 
			
			$beginPoint = $segment[ 1 ];
			$endPoint = $segment[ count( $segment ) - 3 ];
			
			// calculate slip search coordinate based on non-virtual segments
			$xCoord = round( 0.75 * ( $endPoint[ Chasm_Profile_Parser::X ]
				- $beginPoint[ Chasm_Profile_Parser::X ] ) );
					
			$yCoord = round( 0.75 * ( $beginPoint[ Chasm_Profile_Parser::Y ] 
					- $endPoint[ Chasm_Profile_Parser::Y ] ) );
						
			$dimensions = round ( ( $beginPoint[ Chasm_Profile_Parser::Y ] 
					- $endPoint[ Chasm_Profile_Parser::Y ] ) / 3.0 );
			
			// fwrite( $outputStream, grid search parameters
			fwrite( $outputStream, $xCoord . " " . $yCoord 
				. " " . Chasm_Stability::X_GRID_SPACING 
				. " " . Chasm_Stability::Y_GRID_SPACING 
				. " " . $dimensions . " " . $dimensions 
				. " " . Chasm_Stability::INITIAL_RADIUS_LENGTH
				. " " . Chasm_Stability::RADIUS_INCREASE
				. $newLine );
			
			fwrite( $outputStream, (count( $segment ) - 1) . $newLine );
			for ( $idx = 0; $idx < count( $segment ) - 1 ; $idx++ )
			{
				fwrite( $outputStream, round( $segment[ $idx ][ Chasm_Profile_Parser::X ], 2 ) 
					. " " 
					. round( $segment[ $idx ][ Chasm_Profile_Parser::Y ], 2)
					. $newLine );
			}
		}
	}
	
	class Chasm_Boundary_Conditions
	{
		const DETENTION_CAPACITY = "0.01";
		const MAX_SOIL_EVAPORATION = "5e-07";
		
		const START_TIME = "0";
		const STOP_TIME = "359";
		
		public static function generateBoundaryConditions( $req, $rainIdx = 0, 
				$outputStream, $newLine = "\n" )
		{
			$upslopeRecharge = $req[ Chasm_Input_Parser::WATER_UPSLOPE_RECHARGE ];
	
			// upslope recharge
			fwrite( $outputStream, $upslopeRecharge . $newLine );
			
			// detention capacity / max soil evaporation
			fwrite( $outputStream, Chasm_Boundary_Conditions::DETENTION_CAPACITY 
				. " " . Chasm_Boundary_Conditions::MAX_SOIL_EVAPORATION 
				. $newLine );
			
			fwrite( $outputStream, $newLine );
			
			// storm start time / stop time
			fwrite( $outputStream, Chasm_Boundary_Conditions::START_TIME . " " 
				. Chasm_Boundary_Conditions::STOP_TIME . $newLine );
			
			fwrite( $outputStream, $newLine );
			
			// loop through 7 days pre-storm
			for ( $day = 0; $day < 7; $day++ )
			{
				$counter = 0;
				for ( $hour = 0; $hour < 24; $hour++ )
				{
					fwrite( $outputStream, "0.0 " );
					
					if ( $counter++ > 4 )
					{
						fwrite( $outputStream, $newLine );
						$counter = 0;
					}
				}
				fwrite( $outputStream, $newLine );
			}
			
			// loop through hours
			$rain = $req[ Chasm_Input_Parser::RAIN ][ $rainIdx ];
			$hours = $rain[ Chasm_Input_Parser::RAIN_DURATION ];
			$volume = $rain[ Chasm_Input_Parser::RAIN_VOLUME ];
			
			// convert volume from mm to m
			$volume = $volume / 1000;
			
			$rainFallPerHour = $volume / $hours;
			
			$counter = 0;
			for ( $hour = 0; $hour < 24; $hour++ )
			{
				if ( $hour < $hours )
				{
					fwrite( $outputStream, $rainFallPerHour . " " );
				}
				else 
				{
					fwrite( $outputStream, "0.0 " );
				}
				
				if ( $counter++ > 4 )
				{
					fwrite( $outputStream, $newLine );
					$counter = 0;
				}
			}
			fwrite( $outputStream, $newLine );
			
			// loop through 7 days post-storm
			for ( $day = 0; $day < 7; $day++ )
			{
				$counter = 0;
				for ( $hour = 0; $hour < 24; $hour++ )
				{
					fwrite( $outputStream, "0.0 " );
					
					if ( $counter++ > 4 )
					{
						fwrite( $outputStream, $newLine );
						$counter = 0;
					}
				}
				fwrite( $outputStream, $newLine );
			}
		}
	}
	
	class Chasm_Soils
	{
		// soil parameter constants
		const MOISTURE_CONTENT = 0.43;
		const SAT_BULK_DENSITY = 20;
		const UNSAT_BULK_DENSITY = 18;
		
		// suction curve constants
		const SUCTION_MOISTURE_CURVE_MODEL = 0;
		const NUM_CURVE_COORDS = 12;
		const MOISTURE_CONTENT_POINTS = "0.209 0.218 0.227 0.251 0.267 0.295 0.308 0.324 0.348 0.376 0.4 0.42";
		const SUCTION_POINTS = "-10 -8 -6 -4 -3 -2 -1.6 -1.2 -0.8 -0.4 -0.2 -0.1";
		
		public static function generateSoilsDatabase( $req, $outputStream, 
				$newLine = "\n" )
		{
			$soils = $req[ Chasm_Input_Parser::SOIL ];
			
			// number of soils
			fwrite( $outputStream, count( $soils ) . $newLine );
			
			fwrite( $outputStream, $newLine );
			
			// loop through soils
			for ( $idx = 0; $idx < count( $soils ); $idx++ )
			{
				// ksat
				fwrite( $outputStream, $soils[ $idx ][ Chasm_Input_Parser::SOIL_KS ] );
				fwrite( $outputStream, " " );
				
				// sat moisture content
				fwrite( $outputStream, Chasm_Soils::MOISTURE_CONTENT );
				fwrite( $outputStream, " " );
				 
				// sat bulk density
				fwrite( $outputStream, Chasm_Soils::SAT_BULK_DENSITY );
				fwrite( $outputStream, " " );
				
				// unsat bulk density
				fwrite( $outputStream, Chasm_Soils::UNSAT_BULK_DENSITY );
				fwrite( $outputStream, " " );
				
				// effective cohesion
				fwrite( $outputStream, $soils[ $idx ][ Chasm_Input_Parser::SOIL_C ] );
				fwrite( $outputStream, " " );
				
				// effective angle of internal friction
				fwrite( $outputStream, $soils[ $idx ][ Chasm_Input_Parser::SOIL_PHI ] );
				fwrite( $outputStream, $newLine );
				
				// model
				fwrite( $outputStream, Chasm_Soils::SUCTION_MOISTURE_CURVE_MODEL );
				fwrite( $outputStream, $newLine );
				
				// num curve points
				fwrite( $outputStream, Chasm_Soils::NUM_CURVE_COORDS );
				fwrite( $outputStream, $newLine );
				
				// moisture content (x-coord)
				fwrite( $outputStream, Chasm_Soils::MOISTURE_CONTENT_POINTS );
				fwrite( $outputStream, $newLine );
				
				// suction (y-coord)
				fwrite( $outputStream, Chasm_Soils::SUCTION_POINTS );
				fwrite( $outputStream, $newLine );
				
				fwrite( $outputStream, $newLine );
			}
		}
	}
	
	class Chasm_Steering
	{
		const GEOMETRY_HEADER = "geometry file: ";
		const SOILS_HEADER = "soils databbase file: ";
		const STABILITY_HEADER = "stability file: ";
		const BOUNDARY_HEADER = "boundary conditions file: ";
		const REINFORCEMENT_HEADER = "reinforcement file: ";
		const VEGETATION_HEADER = "vegetation file: ";
		const STOCHASTIC_HEADER = "stochastic parameters: ";
		const OUTPUT_VARIABLES = "output variables: ";
		const DURATION = "duration of the simulation (h): ";
		const TIME_STEP = "time step (s): ";
	
		const GEOMETRY_FILE_SUFFIX = "-geometry.txt";
		const SOILS_FILE_SUFFIX = "-soils.txt";
		const STABILITY_FILE_SUFFIX = "-stability.txt";
		const BOUNDARY_FILE_PREFIX = "-1in";
		const BOUNDARY_FILE_SUFFIX = "-rain.txt";
		const REINFORCEMENT_FILE_SUFFIX = "-reinforcement.txt";
		const VEGETATION_FILE_SUFFIX = "-vegetation.txt";
		const STOCHASTIC_FILE_SUFFIX = "-stochastic.txt";
		const STEERING_FILE_SUFFIX = "-rain.steering";
		
		const TEXT_EXTENSION = ".txt";
		const STEERING_EXTENSION = ".steering";
		
		const REPORT_FACTOR_OF_SAFETY = "1";
		const REPORT_PRESSURE_HEAD = "0";
		const REPORT_SOIL_MOSTURE_CONTENT = "0";
		const REPORT_TOTAL_SOIL_MOISTURE_CONTENT = "0";
		const REPORT_VEGETATION_INTERCEPTION = "0";
		
		const DURATION_VALUE = 360;
		const TIME_STEP_VALUE = 60;
		
		public static function isCommentedOut( $name )
		{
			$array = array( self::REINFORCEMENT_HEADER,		
				self::STOCHASTIC_HEADER,
				self::VEGETATION_HEADER
			);
			
			return in_array( $name, $array );
		}
		
		public static function safeFileName( $name ) 
		{
			$temp = str_replace( " ", "_", strtolower( $name ) );
			
			$newFileName = "";
		    for ( $idx = 0; $idx < strlen( $temp ); $idx++ ) {
		        if ( preg_match('([0-9]|[a-z]|_)', $temp[ $idx ] ) ) {
		            $newFileName = $newFileName . $temp[ $idx ];
		        }    
		    }
		 
		    return $newFileName;			
		}		
		
		public static function generateSteering( $req, $rainIdx = 0, 
				$outputStream, $newLine = "\n" )
		{
			$info = $req[ Chasm_Input_Parser::INFO ];
			$rain = $req[ Chasm_Input_Parser::RAIN ][ $rainIdx ]; 
		
			$filePrefix = Chasm_Steering::safeFileName( $info[ Chasm_Input_Parser::NAME ] );	
			
			$geometryFile = $filePrefix . Chasm_Steering::GEOMETRY_FILE_SUFFIX;
			$soilsFile = $filePrefix . Chasm_Steering::SOILS_FILE_SUFFIX;
			$stabilityFile = $filePrefix . Chasm_Steering::STABILITY_FILE_SUFFIX;
			$boundaryFile =  $filePrefix . Chasm_Steering::BOUNDARY_FILE_PREFIX 
				. $rain[ Chasm_Input_Parser::RAIN_FREQUENCY ] 
				. Chasm_Steering::BOUNDARY_FILE_SUFFIX;
			$reinforcementFile = $filePrefix 
				. Chasm_Steering::REINFORCEMENT_FILE_SUFFIX;
			$vegetationFile = $filePrefix 
				. Chasm_Steering::VEGETATION_FILE_SUFFIX;
			$stochasticFile = $filePrefix 
				. Chasm_Steering::STOCHASTIC_FILE_SUFFIX;
			
			if ( Chasm_Steering::isCommentedOut( Chasm_Steering::GEOMETRY_HEADER ) )
			{
				fwrite( $outputStream, "#");
			}
			fwrite( $outputStream, Chasm_Steering::GEOMETRY_HEADER . $geometryFile );
			fwrite( $outputStream, $newLine );
			
			if ( Chasm_Steering::isCommentedOut( Chasm_Steering::SOILS_HEADER ) )
			{
				fwrite( $outputStream, "#" );
			}
			fwrite( $outputStream, Chasm_Steering::SOILS_HEADER . $soilsFile );
			fwrite( $outputStream, $newLine );
			
			if ( Chasm_Steering::isCommentedOut( Chasm_Steering::STABILITY_HEADER ) )
			{
				fwrite( $outputStream, "#" );
			}
			fwrite( $outputStream, Chasm_Steering::STABILITY_HEADER . $stabilityFile );
			fwrite( $outputStream, $newLine );
			
			if ( Chasm_Steering::isCommentedOut( Chasm_Steering::BOUNDARY_HEADER ) )
			{
				fwrite( $outputStream, "#" );
			}
			fwrite( $outputStream, Chasm_Steering::BOUNDARY_HEADER . $boundaryFile );
			fwrite( $outputStream, $newLine );
			
			if ( Chasm_Steering::isCommentedOut( Chasm_Steering::REINFORCEMENT_HEADER ) )
			{
				fwrite( $outputStream, "#" );
			}
			fwrite( $outputStream, Chasm_Steering::REINFORCEMENT_HEADER . $reinforcementFile );
			fwrite( $outputStream, $newLine );
			
			if ( Chasm_Steering::isCommentedOut( Chasm_Steering::VEGETATION_HEADER ) )
			{
				fwrite( $outputStream, "#" );
			}
			fwrite( $outputStream, Chasm_Steering::VEGETATION_HEADER . $vegetationFile );
			fwrite( $outputStream, $newLine );
			
			if ( Chasm_Steering::isCommentedOut( Chasm_Steering::STOCHASTIC_HEADER ) )
			{
				fwrite( $outputStream, "#" );
			}
			fwrite( $outputStream, Chasm_Steering::STOCHASTIC_HEADER . $stochasticFile );
			fwrite( $outputStream, $newLine );
			
			if ( Chasm_Steering::isCommentedOut( Chasm_Steering::OUTPUT_VARIABLES ) )
			{
				fwrite( $outputStream, "#" );
			}
			fwrite( $outputStream, Chasm_Steering::OUTPUT_VARIABLES 
				. Chasm_Steering::REPORT_FACTOR_OF_SAFETY 
				. " " . Chasm_Steering::REPORT_PRESSURE_HEAD 
				. " " . Chasm_Steering::REPORT_SOIL_MOSTURE_CONTENT 
				. " " . Chasm_Steering::REPORT_TOTAL_SOIL_MOISTURE_CONTENT 
				. " " . Chasm_Steering::REPORT_VEGETATION_INTERCEPTION );
			fwrite( $outputStream, $newLine );
		
			if ( Chasm_Steering::isCommentedOut( Chasm_Steering::DURATION ) )
			{
				fwrite( $outputStream, "#" );
			}
			fwrite( $outputStream, Chasm_Steering::DURATION . Chasm_Steering::DURATION_VALUE );
			fwrite( $outputStream, $newLine );
			
			if ( Chasm_Steering::isCommentedOut( Chasm_Steering::TIME_STEP) )
			{
				fwrite( $outputStream, "#" );
			}
			fwrite( $outputStream, Chasm_Steering::TIME_STEP . Chasm_Steering::TIME_STEP_VALUE );
			fwrite( $outputStream, $newLine );			
		}
	}

	class Chasm_Geometry
	{
		public static function generateFile( $req, $outputStream, $newLine = "\n" ) {
	
		    $profileGeometry = $req[Chasm_Input_Parser::PROFILE];
		                       
			$waterDepths = $req[Chasm_Input_Parser::WATER_DEPTH];
			
		    $segment = Chasm_Profile_Parser::generateXYPoints( $profileGeometry );
		
		    $max_height = $segment[0][Chasm_Profile_Parser::Y];
		    $max_width = $segment[ count( $segment ) - 1][Chasm_Profile_Parser::X];
		
		    $max_depth = 0;
		    $soilLayers = array();
		    for ( $depthIdx = 0; $depthIdx < count( $req[Chasm_Input_Parser::SOIL] ) - 1; $depthIdx++ )
		    {
		    	$soilLayer = Chasm_Profile_Parser::generateLayerXYPoints( $segment, 
		    		$req[Chasm_Input_Parser::SOIL][ $depthIdx ][Chasm_Input_Parser::SOIL_DEPTH] );    	
		    	$soilLayer[Chasm_Profile_Parser::SOIL_TYPE] =  $depthIdx;
		    	    	
		    	array_push( $soilLayers,  $soilLayer);
		    	
		    	$max_depth += array_sum($req[Chasm_Input_Parser::SOIL][ $depthIdx ][Chasm_Input_Parser::SOIL_DEPTH]);
		    }
		    
		    $bottomDepths = array_fill(0, count( $segment ), $max_height + $max_depth);
		    $bottom = Chasm_Profile_Parser::generateLayerXYPoints( $segment, $bottomDepths );
		    $bottom[Chasm_Profile_Parser::SOIL_TYPE] = count( $soilLayers );
		    array_push( $soilLayers, $bottom );
		    
		    $water = Chasm_Profile_Parser::generateLayerXYPoints( $segment, $waterDepths );
		    
		    $cells = Chasm_Profile_Parser::generateCellsArr($segment, $soilLayers);
			$water_columns = Chasm_Profile_Parser::generateWaterColumns( $max_width, $water );
		
			if ( !empty( $req[ Chasm_Input_Parser::WATER_INITIAL_SLOPE_SUCTION ] ) )
			{
				$initial_surface_suction = $req[ Chasm_Input_Parser::WATER_INITIAL_SLOPE_SUCTION ];	
			}
			else
			{
				$initial_surface_suction = -0.5;
			}

			fwrite( $outputStream, count($cells) . $newLine );
	
			for ( $row=0; $row<count($cells); $row++ ) {
				// clear invalid data
				while ( $cells[$row][0] == Chasm_Profile_Parser::INVALID_DATA ) {
					array_shift($cells[$row]);
				}
	
				$num_cells = count( $cells[$row] );
				$num_water = round ( $water_columns[$row] ) ;
				$column_width = 1;
				$column_breadth = 1;
						
				// print meta data line
				fwrite( $outputStream, $num_cells 
					. " " . $num_water 
					. " " . $column_width 
					. " " . $column_breadth 
					. " " . $initial_surface_suction . $newLine );
	
				// print column line
				$text = "1 " . $cells[$row][0];
				for ( $col=1; $col<count($cells[$row]); $col++ ) {
					$text = $text . " 1 " . $cells[$row][$col];
				}
				fwrite( $outputStream, $text . $newLine );
			}
	
			// print EOF marker
			fwrite( $outputStream, "0 " . $water_columns[count($cells) - 1] );
		}	
	}
?>
