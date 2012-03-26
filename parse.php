<?php
class Chasm_Input_Parser
{

    const INFO = "info";
    const NAME = "name";
    const LATITUDE = "latitude";
    const LONGITUDE = "longitude";

    const PROFILE = "profile";
    const PROFILE_HEIGHT = "height";
    const PROFILE_LENGTH = "length";
    const PROFILE_ANGLE = "angle";
    
    const SOIL = "soilStrata";
    const SOIL_TYPE = "type";
    const SOIL_C ="c";
    const SOIL_PHI = "phi";
    const SOIL_KS = "ks";
    const SOIL_DEPTH = "depth";
    
    const WATER_DEPTH = "waterDepth";
    const WATER_UPSLOPE_RECHARGE = "waterUpslopeRecharge";
    const WATER_INITIAL_SLOPE_SUCTION = "waterInitialSlopeSuction";
    
    const RAIN = "rain";
    const RAIN_FREQUENCY = "frequency";
    const RAIN_DURATION = "duration";
    const RAIN_VOLUME = "volume";
        
    public static function trimSoil( $soilLayers )
    {
    	if ( empty( $soilLayers ) || !is_array( $soilLayers ) )
    	{
    		return false;
    	}
    	for ($idx = 0; $idx < count( $soilLayers ); $idx++ ) 
    	{
    		if ( !is_array( $soilLayers[ $idx ] ) )
    		{
    			return false;
    		}
    		
    		if ( 
    			empty( $soilLayers[ Chasm_Input_Parser::SOIL_C ] )
    				|| empty( $soilLayers[ Chasm_Input_Parser::SOIL_PHI ] )
    				|| empty( $soilLayers[ Chasm_Input_Parser::SOIL_KS ] )
    				|| empty( $soilLayers[ Chasm_Input_Parser::SOIL_DEPTH ] )
    				|| !is_array( $soilLayers[ $idx ] )
    		 ) {
    		 	unset( $soilLayers[ $idx ] );
    		 }
    		 
    	}	
    	
    	print_r( $soilLayers );
    }
    
    public static function validateInfoInput( $info )
    {
    }
    
    public static function validateProfileInput( $profile )
    {
    }
    
    public static function validateWaterInput( $profile, $water, $waterUpslopeRecharge )
    {
    }
    
    public static function validateSoilInput( $profile, $soil )
    {
    }
    
    public static function validateRainInput( $rain )
    {
    }
    
    public static function debugData( $print = FALSE )
    {
		$data = array();
		
		$data[Chasm_Input_Parser::INFO] = array();
		$data[Chasm_Input_Parser::INFO][Chasm_Input_Parser::NAME] = "06";
		$data[Chasm_Input_Parser::INFO][Chasm_Input_Parser::LATITUDE] = "-61.67856";
		$data[Chasm_Input_Parser::INFO][Chasm_Input_Parser::LONGITUDE] = "13.54678";
		
		$data[Chasm_Input_Parser::PROFILE] = array ( array(Chasm_Input_Parser::PROFILE_HEIGHT => 0, 
	                              Chasm_Input_Parser::PROFILE_LENGTH => 8,
	                              Chasm_Input_Parser::PROFILE_ANGLE => 0),
	                        array(Chasm_Input_Parser::PROFILE_HEIGHT => 9, 
	                              Chasm_Input_Parser::PROFILE_LENGTH => 24.0252044629861,
	                              Chasm_Input_Parser::PROFILE_ANGLE => 22),
	                        array(Chasm_Input_Parser::PROFILE_HEIGHT => 17, 
	                              Chasm_Input_Parser::PROFILE_LENGTH => 34,
	                              Chasm_Input_Parser::PROFILE_ANGLE => 30),
	                        array(Chasm_Input_Parser::PROFILE_HEIGHT => 11, 
	                              Chasm_Input_Parser::PROFILE_LENGTH => 21.3576442905139,
	                              Chasm_Input_Parser::PROFILE_ANGLE => 31),
	                        array(Chasm_Input_Parser::PROFILE_HEIGHT => 10, 
	                              Chasm_Input_Parser::PROFILE_LENGTH => 24.5859333557424,
	                              Chasm_Input_Parser::PROFILE_ANGLE => 24),
	                        array(Chasm_Input_Parser::PROFILE_HEIGHT => 1, 
	                              Chasm_Input_Parser::PROFILE_LENGTH => 1.55572382686041,
	                              Chasm_Input_Parser::PROFILE_ANGLE => 40),
	                        array(Chasm_Input_Parser::PROFILE_HEIGHT => 0, 
	                              Chasm_Input_Parser::PROFILE_LENGTH => 8,
	                              Chasm_Input_Parser::PROFILE_ANGLE => 0),
	                       array(Chasm_Input_Parser::PROFILE_HEIGHT => 0, 
	                              Chasm_Input_Parser::PROFILE_LENGTH => 16.4519761570364,
	                              Chasm_Input_Parser::PROFILE_ANGLE => 0),
	                       array(Chasm_Input_Parser::PROFILE_HEIGHT => 12, 
	                              Chasm_Input_Parser::PROFILE_LENGTH => 0,
	                              Chasm_Input_Parser::PROFILE_ANGLE => 90),       
	                    );
		$data[Chasm_Input_Parser::SOIL] = array(
							array( 	Chasm_Input_Parser::SOIL_TYPE=>"6",
									Chasm_Input_Parser::SOIL_C=>"10",
									Chasm_Input_Parser::SOIL_PHI=>"23",
									Chasm_Input_Parser::SOIL_KS=>"5e-05",
									Chasm_Input_Parser::SOIL_DEPTH => array(3, 5, 6, 7, 8, 10, 9.5, 11, 14)),
							array( 	Chasm_Input_Parser::SOIL_TYPE=>"4",
									Chasm_Input_Parser::SOIL_C=>"25",
									Chasm_Input_Parser::SOIL_PHI=>"33",
									Chasm_Input_Parser::SOIL_KS=>"5e-06",
									Chasm_Input_Parser::SOIL_DEPTH => array(4, 6, 8, 10, 12, 14, 13.5, 16, 20)),
							array( 	Chasm_Input_Parser::SOIL_TYPE=>"2",
									Chasm_Input_Parser::SOIL_C=>"40",
									Chasm_Input_Parser::SOIL_PHI=>"50",
									Chasm_Input_Parser::SOIL_KS=>"1e-08",
//									Chasm_Input_Parser::SOIL_DEPTH => array('', '', '', '', '', '', '', '', '')
							),
						);
		$data[Chasm_Input_Parser::WATER_DEPTH] = array(20, 20, 15, 8, 4, 4, 3, 3, 3);
		$data[Chasm_Input_Parser::WATER_INITIAL_SLOPE_SUCTION] = "-0.5";
		$data[Chasm_Input_Parser::WATER_UPSLOPE_RECHARGE] = "0";
		
		$data[Chasm_Input_Parser::RAIN] = 	array(
												array( 	Chasm_Input_Parser::RAIN_FREQUENCY=> "50",
	                    								Chasm_Input_Parser::RAIN_DURATION => "24",
														Chasm_Input_Parser::RAIN_VOLUME=>"288"),
												array( 	Chasm_Input_Parser::RAIN_FREQUENCY=> "5",
	                    								Chasm_Input_Parser::RAIN_DURATION => "10",
														Chasm_Input_Parser::RAIN_VOLUME=>"152.4"),
											);
											
		if ( $print ) {
			echo "<pre>";
			print_r( $data );
			echo "</pre>";
		}
		return $data;
    }
}
?>
