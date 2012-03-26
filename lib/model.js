// DEPENDENCIES: trig.js

var CHASM =
{};

CHASM.graph = null;

// Constant for Height
CHASM.H = 0;

// Constant for Length
CHASM.L = 1;

// Constant for Theta
CHASM.THETA = 2;

// Constant for X coordinate
CHASM.X = 0;

// Constant for Y coordinate
CHASM.Y = 1;

// profile
CHASM.profilePoints = new Array();

// soil depths
CHASM.soilDepths = new Array();

// water depths
CHASM.waterDepths = new Array();

// establish line colors
CHASM.color =
{};
CHASM.color.profile = '#000000';
CHASM.color.soils = new Array( '#4D8B4D', '#EAAC69', '#973E83' );
CHASM.color.water = '#322AAA';

CHASM.setXYPoints = function( profileXYPoints, soilLayersXYPoints,
	waterXYPoints )
{
	this.profilePoints = profileXYPoints;

	var depths = new Array( soilLayersXYPoints.length );
	for ( var idx = 0; idx < soilLayersXYPoints.length; idx++ )
		depths[ idx ] = this.convertPointsToOffsets( profileXYPoints,
			soilLayerXYPoints[ idx ] );

	this.soilDepths = depths;

	this.waterDepths = this.convertPointsToOffsets( profileXYPpoints,
		waterXYPoints );

	this.fireUpdate();
};

CHASM.setParameters = function( profileData, soilLayerDepths, waterLayerDepths )
{
	this.profilePoints = this.generateXYPoints( profileData );
	this.setSoilDepths = soilLayerdepths;
	this.waterDepths = waterLayerDepths;

	this.fireUpdate();
};

CHASM.getProfileData = function()
{
	return this.generateHLTheta( this.profilePoints );
};

CHASM.setProfileData = function( profileData )
{
	this.profilePoints = this.generateXYPoints( profileData );

	this.fireUpdate();
};

CHASM.getProfileXYPoints = function()
{
	return this.profilePoints;
};

CHASM.setProfileXYPoints = function( xyPoints )
{
	this.profilePoints = xyPoints;

	this.fireUpdate();
};

CHASM.getSoilDepths = function()
{
	return this.soilDepths;
};

CHASM.setSoilDepths = function( soilLayerDepths )
{
	this.soilDepths = soilLayerDepths;

	this.fireUpdate();
};

CHASM.getSoilPoints = function()
{
	var toReturn = new Array( this.soilDepths.length );
	for ( var idx = 0; idx < this.soilDepths.length; idx++ )
	{
		toReturn[ idx ] = this.convertOffsetToPoints( this.profilePoints,
			this.soilDepths[ idx ] );
	}

	return toReturn;
};

CHASM.setSoilPoints = function( soilXYPoints )
{
	var soils = new Array( soilXYPoints.length );
	for ( var idx = 0; idx < this.soilXYPoints.length; idx++ )
	{
		soils[ idx ] = this.convertPointsToOffsets( this.profilePoints,
			soilXYPoints[ idx ] );
	}

	this.fireUpdate();
};

CHASM.getWaterDepths = function()
{
	return this.waterDepths;
};

CHASM.setWaterDepths = function( waterLayerDepths )
{
	this.waterDepths = waterLayerDepths;

	this.fireUpdate();
};

CHASM.getWaterPoints = function()
{
	return this.convertOffsetToPoints( this.profilePoints, this.waterDepths );
};

CHASM.getColors = function()
{
	return this.color;
};

CHASM.setColor = function( colorSet )
{
	this.color = colorSet;
};

CHASM.setProfileColor = function( color )
{
	this.color.profile = color;
};

CHASM.getProfileColor = function()
{
	return this.color.profile;
};

CHASM.getSoilColor = function( layer )
{
	return this.color.soils[ layer ];
};

CHASM.getSoilColors = function()
{
	return this.color.soils;
};

CHASM.setSoilColors = function( colors )
{
	this.color.soils = colors;
};

CHASM.addSoilColor = function( color )
{
	this.color.soils[ this.color.soils.length ] = color;
};

CHASM.setWaterColor = function( color )
{
	this.color.water = color;
};

CHASM.setWaterPoints = function( waterPoints )
{
	this.waterDepths = this.convertPointsToOffsets( this.profilePoints,
		waterPoints );
};

CHASM.getWaterColor = function()
{
	return this.color.water;
};

// Given data array, generates array of XY coordinates
CHASM.generateXYPoints = function( data )
{
	if ( data )
	{
		var originHeight = 0;
		for ( var i = 0; i < data.length; i++ )
		{
			originHeight += data[ i ][ this.H ];
		}

		// one more point than there are measurements
		var xyPoints = new Array( data.length + 1 );

		xyPoints[ 0 ] = [ 0, originHeight ];

		for ( var i = 0; i < data.length; i++ )
		{
			xyPoints[ i + 1 ] = [
			                     xyPoints[ i ][ this.X ]
							+ data[ i ][ this.L ]
							* Math
									.cos( TRIG
											.degreesToRadians( data[ i ][ this.THETA ] ) ) ,
				xyPoints[ i ][ this.Y ] - data[ i ][ this.H ]  ];
		}

		return xyPoints;
	} else
	{
		return null;
	}
};

CHASM.generateHLTheta = function( xyPoints )
{
	if ( xyPoints && xyPoints.length > 1 )
	{
		var data = new Array( xyPoints.length - 1 );

		for ( var idx = 0; idx < xyPoints.length - 1; idx++ )
		{
			var y = xyPoints[ idx + 1 ][ this.Y ] - xyPoints[ idx ][ this.Y ];
			var x = xyPoints[ idx + 1 ][ this.X ] - xyPoints[ idx ][ this.X ];

			var hyp = Math.sqrt( x * x + y * y );

			data[ idx ][ this.H ] = y;
			data[ idx ][ this.L ] = hyp;
			data[ idx ][ this.THETA ] = Math.asin( y / hyp );
		}

		return data;
	} else
	{
		return null;
	}
};

CHASM.convertOffsetToPoints = function( lineSegments, offsets )
{

	// TODO: confirm offset size < line segments size

	var points = new Array( offsets.length );
	for ( var offsetIdx = 0; offsetIdx < offsets.length; offsetIdx++ )
	{
		points[ offsetIdx ] = new Array( 2 );
		points[ offsetIdx ][ this.X ] = lineSegments[ offsetIdx ][ this.X ];
		points[ offsetIdx ][ this.Y ] = lineSegments[ offsetIdx ][ this.Y ]
			- offsets[ offsetIdx ];
	}

	return points;
};

CHASM.convertPointsToOffsets = function( xyPoints, layerPoints )
{
	// TODO: confirm xyPoints and layerPoints are same size
	// confirm x coords in both arrays are the same

	var offsets = new Array( xyPoints.length );

	for ( var idx = 0; idx < xyPoints.length; idx++ )
	{
		offsets[ idx ] = xyPoints[ idx ][ this.Y ]
			- layerPoints[ idx ][ this.Y ];
	}

	return offsets;
};

CHASM.listeners = new Array();
CHASM.fireUpdate = function()
{
	for ( var idx = 0; idx < this.listeners.length; idx++ )
	{
		// try
		// {
		this.listeners[ idx ]( this );
		// } catch (err)
		// {
		// alert(err);
		// }
	}
};

CHASM.addListener = function( listener )
{
	this.listeners[ this.listeners.length ] = listener;
};

CHASM.validateProfileGeometry = function()
{
	if ( this.profilePoints && this.profilePoints.length > 0 )
	{
		var point = this.profilePoints[ 0 ];

		for ( var idx = 0; idx < this.profilePoints.length; idx++ )
		{
			if ( this.profilePoints[ idx ][ this.X ] < point[ this.X ]
				|| this.profilePoints[ idx ][ this.Y ] > point[ this.Y ] )
			{
				return false;
			}
			point = this.profilePoints[ idx ];
		}
	}

	return true;
};

CHASM.validateSoilGeometry = function()
{
	if ( this.soilDepths && this.soilDepths.length > 0 )
	{
		for ( var soilIdx = 0; soilIdx < this.soilDepths.legnth; soilIdx++ )
		{
			if ( this.soilDepths[ soilIdx ].length !== this.profilePoints.length )
			{
				return false;
			}
		}
	}

	// no soil layers is legal?
	return true;
};

CHASM.validateWaterGeometry = function()
{
	if ( this.waterDepths && this.waterDepths.length > 0
		&& this.waterDepths.length === this.profilePoints.length )
	{
		var depth = this.profilePoints[ 0 ][ this.Y ];
		for ( var idx = 0; idx < this.waterDepths.length; idx++ )
		{
			if ( this.profilePoints[ idx ][ this.Y ] - this.waterDepths[ idx ] > depth
				|| this.waterDepths[ idx ] <= 0 )
			{
				return false;
			}

			depth = this.profilePoints[ idx ][ this.Y ]
				- this.waterDepths[ idx ];
		}

		return true;
	}

	return true;
};