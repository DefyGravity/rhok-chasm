<?php
include_once "trig.php";

class Chasm_Profile_Parser
{
	const H = "height";
	const L = "length";
	const THETA = "angle";
	const X = 0;
	const Y = 1;

	const BR = "\n";
	const INVALID_DATA = "9";
	const SOIL_TYPE = "soiltype";

	/**
	 * Calculates the (x, y) point given the current (x, y) point and a height, hypotenuse the
	 * angle opposite the height.
	 *
	 * @static
	 * @param int $x the x coordinate of the current point
	 * @param int $y the y coordinate of the current point
	 * @param float $height the height of the triangle
	 * @param float $length the length of the hypotenuse of the triangle
	 * @param float $theta the angle opposite the height of the triangle
	 * @return array the (x, y) coordinates structured as:
	 *      <code>array( X => integer, Y => integer)</code>
	 * @see Chasm_Profile_Parser::X
	 * @see Chasm_Profile_Parser::Y
	 */
	public static function getNextPoint( $x, $y, $height, $length, $theta ) {
		// given an [x, y] coordinate, length, and theta angle, computes the next [x, y] coordiate
		$x_coord = $x + $length * cos( Chasm_Trig_Functions::degreesToRadians( $theta ) );
		$y_coord = $y - $height;
		return array( self::X => $x_coord, self::Y => $y_coord );
	}

	/**
	 * Returns the total height of the feature described the data array.
	 *
	 * @static
	 * @param array $data an array of (h, l, theta) data inputs structured as:
	 *      <code>array( array( H => float, L => float, THETA => float ), ... )</code>
	 * @return the maximum height of the specified data array
	 */
	public static function getTotalHeight($data) {
		$totalHeight = 0;

		for ($i=0; $i < count($data); $i++) {
			$totalHeight += $data[$i][self::H];
		}

		return $totalHeight;
	}

	/**
	 * Constructs an array of (x, y) coordinates based on the specified data array describing the
	 * feature.
	 *
	 * @static
	 * @param array $data an array of (h, l, theta) data inputs structured as:
	 *      <code>array( array( H => float, L => float, THETA => float ), ... )</code>
	 * @return array an array of (x, y) coordinates where the array is structured as:
	 *      <code>array( array( X => integer, Y => integer), ... )</code>
	 */
	public static function generateXYPoints( $data ) {

		$height0 = self::getTotalHeight( $data );

		$xyPoints = array();

		// one more point than there are measurements
		array_push($xyPoints, array(0, $height0));

		for ($i=0; $i < count($data); $i++) {
			array_push($xyPoints, 
				self::getNextPoint(
					$xyPoints[$i][self::X],
					$xyPoints[$i][self::Y],
					$data[$i][self::H],
					$data[$i][self::L],
					$data[$i][self::THETA]
				)
			);
		}

		return $xyPoints;
	}

	/**
	 * Constructs an array of (x, y) coordinates for a layer (such as soil or water) based on the
	 * specified array of (x, y) coordinates describing the feature and the specified layer depths.
	 * The size of the layer depths array must be the same as the size of the array of (x, y)
	 * coordinates.
	 *
	 * @static
	 * @param array $xyPoints an array of (x, y) coordinates where the array is structured as:
	 *      <code>array( array( X => integer, Y => integer), ... )</code>
	 * @param array $data an array of depths for the layer structured as:
	 *      <code>array( integer => float )</code>
	 * @return array an array of (x, y) coordinates where the array is structured as:
	 *      <code>array( array( X => integer, Y => integer), ... )</code>
	 */
	public static function generateLayerXYPoints( $xyPoints, $layer )
	{
		if ( count( $xyPoints ) != count( $layer ) )
		{
			// TODO: throw an error
		}

		$layerPoints = array();

		for ( $i = 0; $i < count( $layer ); $i++ )
		{
			// concatenate X coord with layer depth (depth needs to be converted to
			// Y value based on Y points);
			array_push( $layerPoints,
			array(  self::X => $xyPoints[ $i ][ self::X ],
			self::Y => $xyPoints[ $i ][ self::Y ] - $layer[ $i ] ) );
		}

		return $layerPoints;
	}

	public static function getYValue( $x_coord, $lineSegments )
	{
		$line_seg_idx = self::findSegment( $x_coord, $lineSegments );
			
		$x1 = $lineSegments[$line_seg_idx-1][self::X];
		$y1 = $lineSegments[$line_seg_idx-1][self::Y];

		$x2 = $lineSegments[$line_seg_idx][self::X];
		$y2 = $lineSegments[$line_seg_idx][self::Y];
		
		// calculate slope
		$slope = ( $y2 - $y1 ) / ( $x2 - $x1 );

		// calculate y value at the x-coordinate
		$y =  $slope * ( $x_coord - $x1 ) + $y1 ;

		return $y;
	}
	
	/*****/


	/**
	 * Constructs an array where each element in the array is the height of the
	 * water table at that index offset. For example, for a 4-unit wide grid
	 * that is 4 units high, where the water line is represented by:
	 * <code>y = 4 -x</code>, this function returns an array:
	 * <code>[ 4, 3, 2, 1 ]</code>.
	 *
	 * @static
	 * @param float $width the total width of the grid
	 * @param array water an array of (x, y) coordinates representing water 
	 * 			where the array is structured as:
	 *      <code>array( array( X => integer, Y => integer), ... )</code>
	 * @return array an array of integer heights of water
	 */
	public static function generateWaterColumns( $width, $water ) {

		$results = array();

		for ( $x=0.5; $x < $width; $x += 1 ) {
			// calculate y value at the x-coordinate
			$y = self::getYValue( $x, $water );

			array_push($results, $y);
		}

		return $results;
	}

	/*
	 Given the profile/line segements, generates the cell data matrix

	 Assumes that the line segments are in right order (i.e. profile > soil1 > soil2 > soil3 > soil4

	 Expected soil data structure:

	 $soil => 	{
	 {x1, y1},
	 {x2,y2},
	 ...
	 "soiltype" => (0|1|2|3)
	 }
	 */
	// TODO: PHPDoc
	public static function generateCells( $profile, $soil1, $soil2, $soil3, $soil4 ) {
		return generateCellsArr( $profile, array( $soil1, $soil2, $soil3, $soil4 ) );
	}

	public static function generateCellsArr( $profile, $soilLayers )
	{
		$height = $profile[0][self::Y];
		$width = $profile[count($profile)-1][self::X];

		$columns = array();

		$x = 0.5;
		while ( $x < $width ) {
			$column = self::generateColumnArr( $x, $height, $profile, $soilLayers );
			array_push($columns, $column );
			$x += 1.0;
		}

		$data = array();

		for ($col=0; $col<$height; $col++) {
			$temp = array();
			for ($row=0; $row<$width-1; $row++) {
				array_push($temp, $columns[$row][$col]);
			}

			array_push($data, $temp);
		}

		return $columns;
	}

	/*
	 Given the max height, the x coord of the column, and the profile/soil line segements, generates the column data

	 Assumes that the line segments are in right order (i.e. profile > soil1 > soil2 > soil3 > soil4

	 Expected soil data structure:

	 $soil => 	{
	 {x1, y1},
	 {x2,y2},
	 ...
	 "soiltype" => (0|1|2|3)
	 }
	 */
	// TODO: PHPDoc
	public static function generateColumn( $x, $height, $profile, $soil1, $soil2, $soil3, $soil4 ) {

		return self::generateColumnArr( $x, $height, $profile, array( $soil1, $soil2, $soil3, $soil4 ) );
	}
	
	public static function generateColumnArr( $x, $height, $profile, $soilLayers )
	{
		$y=$height - 0.5;

		$column = array();

		while ($y > 0) {
			array_push($column, self::getCellValueArr( $x, $y, $profile, $soilLayers ));
			$y -= 1.0;
		}		
		
		return $column;
	}

	/* Given the (x,y) coordinate, the profile lines, soil lines, calculates the correct value.

	Assumes that the line segments are in right order (i.e. profile > soil1 > soil2 > soil3 > soil4

	Expected soil data structure:

	$soil => 	{
	{x1, y1},
	{x2,y2},
	...
	"soiltype" => (0|1|2|3)
	}
	*/
	// TODO: modify to take an array of soils (more flexbile, allows arbitrary soil depth
	// Use strval( var ) to convert num to string
	public static function getCellValue( $x_coord, $y_coord, $profile, $soil1, $soil2, $soil3, $soil4 ) {
		return self::getCellValueArr( $x_coord, $y_coord, $profile, array( $soil1, $soil2, $soil3, $soil4 ) );
	}

	/**
	 * Returns the value that should be placed at the cell corresponding to the specified (x, y)
	 * coordinates given the profile and soil layers. This value is either the soil layer (by
	 * index), or the value used for <code>INVALID_DATA</code>.
	 *
	 * @param float $x_coord the x coordinate of the cell
	 * @param float $y_coord the y coordinate of the cell
	 * @param array $profileCoords the (x, y) coordinates representing the slope profile
	 *  structured as:
	 *      <code>array( array( X => integer, Y => integer), ... )</code>
	 * @param array $soilLayers an array of soil layers consisting of (x, y) coordinates
	 *  representing each layer structured as:
	 *      <code>array( integer => array( array( X => integer, Y => integer), ... ), ... )</code>
	 */
	public static function getCellValueArr( $x_coord, $y_coord, $profileCoords, $soilLayers )
	{
		$line_seg_idx = self::findSegment( $x_coord, $profileCoords );

		if ( self::isAboveLine( $x_coord, $y_coord,
		$profileCoords[$line_seg_idx-1][self::X], $profileCoords[$line_seg_idx-1][self::Y],
		$profileCoords[$line_seg_idx][self::X], $profileCoords[$line_seg_idx][self::Y] ) ) {

			return self::INVALID_DATA;
		}

		for ( $i = 0; $i < count( $soilLayers ); $i++ )
		{
			$line_seg_idx = self::findSegment( $x_coord, $soilLayers[ $i ] );
			if ( self::isAboveLine( $x_coord, $y_coord,
			$soilLayers[ $i ][ $line_seg_idx - 1 ][self::X],
			$soilLayers[ $i ][ $line_seg_idx - 1 ][self::Y],
			$soilLayers[ $i ][ $line_seg_idx ][self::X],
			$soilLayers[ $i ][ $line_seg_idx ][self::Y] ) )
			{
				return $soilLayers[ $i ][ self::SOIL_TYPE ];
			}
		}

		return self::INVALID_DATA;
	}

	/**
	 * Returns whether the specified (x, y) coordinate is above the line segment formed by the
	 * corodinates (x1, y1) and (x2, y2).
	 *
	 * @static
	 * @param float $x_coord the x coordinate to test
	 * @param float $y_coord the y coordinate to test
	 * @param float $x1 the first x coordinate of the line segment
	 * @param float $y1 the first y coordinate of the line segment
	 * @param float $x2 the second x coordinate of the line segment
	 * @param float $y2 the second y coordinate of the line segment
	 * @return boolean true if the specified (x, y) coordinate is above the specified line segment;
	 *      false otherwise
	 */
	public static function isAboveLine( $x_coord, $y_coord, $x1, $y1, $x2, $y2 ) {

		// calculate slope
		$slope = ( $y2 - $y1 ) / ( $x2 - $x1 );

		// calculate y value at the x-coordinate (point slope form)
		$y = $slope * ( $x_coord - $x1 ) + $y1;

		return ( $y_coord > $y );
	}

	/**
	 * Returns the index of the first (x, y) coordinate in the specified points whose x coordinate
	 * is greated than the specified x coordinate lies. This function assumes that the (x, y) points
	 * are in ascending x coordinate order.
	 *
	 * @static
	 * @param float $x_coord the x coordinate to find
	 * @param array $xyPoints an array of (x, y) coordinates that correspond to a continguous
	 *      set of ordered line segments structured as:
	 *      <code>array( array( X => integer, Y => integer), ... )</code>
	 * @return integer the index of the line segment in the <code>$xyPoints</code> array that
	 *      contains the specified x coordinate
	 */
	public static function findSegment( $x_coord, $xyPoints ) {

		for ($i=0; $i < count($xyPoints); $i++) {
			if ( $xyPoints[$i][self::X] > $x_coord ) {
				return $i;
			}
		}
	}
}

?>