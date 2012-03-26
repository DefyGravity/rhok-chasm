var GRAPH =
{};

GRAPH.ui = null;

GRAPH.profileLines = new Array();
GRAPH.profilePoints = new Array();

GRAPH.soilLines = new Array();
GRAPH.soilPoints = new Array();

GRAPH.waterLines = new Array();
GRAPH.waterPoints = new Array();

/*
 	// Nav bar stuff
    JXG.addEvent(f, "click", g.zoomOut, g);
    e = k.createElement("span");
    c.appendChild(e);
    e.innerHTML = "&nbsp;o&nbsp;";
    JXG.addEvent(e, "click", g.zoom100, g);
    b = k.createElement("span");
    c.appendChild(b);
    b.innerHTML = "&nbsp;+&nbsp;";
    JXG.addEvent(b, "click", g.zoomIn, g);
    l = k.createElement("span");
    c.appendChild(l);
    l.innerHTML = "&nbsp;&larr;&nbsp;";
    JXG.addEvent(l, "click", g.clickLeftArrow, g);
    h = k.createElement("span");
    c.appendChild(h);
    h.innerHTML = "&nbsp;&uarr;&nbsp;";
    JXG.addEvent(h, "click", g.clickUpArrow, g);
    a = k.createElement("span");
    c.appendChild(a);
    a.innerHTML = "&nbsp;&darr;&nbsp;";
    JXG.addEvent(a, "click", g.clickDownArrow, g);
    d = k.createElement("span");
    c.appendChild(d);
    d.innerHTML = "&nbsp;&rarr;&nbsp;";
    JXG.addEvent(d, "click", g.clickRightArrow, g)

 */

GRAPH.plotArrayOfPoints = function( pointsArray, dashed, lineColor )
{

	// for every pair of points in the array, create a line segment on the graph
	for ( var idx = 0; idx < pointsArray.length - 1; idx++ )
	{
		this.createLine( pointsArray[ idx ][ 0 ], pointsArray[ idx ][ 1 ],
			pointsArray[ idx + 1 ][ 0 ], pointsArray[ idx + 1 ][ 1 ], dashed,
			lineColor );
	}
};

GRAPH.updateGraph = function( model )
{

	while ( this.profileLines.length > 0 )
	{
		this.ui.removeObject( this.profileLines.pop() );
	}

	var profilePoints = model.getProfileXYPoints();
	try {
		this.plotArrayOfPoints( profilePoints, !model
			.validateProfileGeometry(), model.getProfileColor() );
	} catch (error) {
	}
	
	var soilPoints = model.getSoilPoints();
	try {
		for ( var idx = 0; idx < soilPoints.length; idx++ )
		{
			this.plotArrayOfPoints( soilPoints[ idx ], !model
					.validateSoilGeometry(), model.getSoilColor( idx ) );
		}
	} catch (error) {
	}

	var waterPoints = model.getWaterPoints();
	try {
		this.plotArrayOfPoints( waterPoints, !model
				.validateWaterGeometry(), model.getWaterColor() );
	} catch (error) {
	}
	
	// calculate left hand x coordinate for bounding box
	var x1 = profilePoints[ profilePoints.length - 1 ][ model.X ] / 20 * -1;
	
	var x2 = profilePoints[ profilePoints.length - 1 ][ model.X ] * 21 / 20; 
	
	var y1 = profilePoints[ 0 ][ model.Y ];
	
	// calculate bottom right y coordinate for bounding box
	var y2 = profilePoints[ profilePoints.length - 1 ][ model.Y ];
	try {
		if ( waterPoints[ waterPoints.length - 1 ][ model.Y ] < y2 ) {
			y2 = waterPoints[ waterPoints.length - 1 ][ model.Y ];
		}
	} catch (error) {}
	try {
		for ( var layerIdx = 0; layerIdx < soilPoints.length; layerIdx++ )
		{
			try {
				for (var idx = 0; idx < soilPoints[ layerIdx ].length; idx++ )
				{
					if ( soilPoints[ layerIdx ][ idx ] < y2 )
					{
						y2 = soilPoints[ layerIdx ][ idx ];
					}
				}
			} catch (error) { continue; }	
		}
	} catch (error) {}
	y2 = y2 - (y1 - y2) / 20 ;
	
	// calculate size of bounding box 
	var xWidth = x2 - x1;
	var yHeight = y1 - y2;
	
	// determine which size to use to keep aspect ratio 1:1
	var bounds = Math.max( xWidth, yHeight ) / 2.0;
	
	var xCenter = x1 + xWidth / 2;
	var yCenter = y2 + yHeight / 2;
	
	var upperLeftX = xCenter - bounds;
	var upperLeftY = yCenter + bounds;
	
	var lowerRightX = xCenter + bounds;
	var lowerRightY = yCenter - bounds;

	this.ui.setBoundingBox( new Array( upperLeftX, upperLeftY, 
		lowerRightX, lowerRightY ), false );
};

GRAPH.createPoint = function( xCoord, yCoord )
{
	return this.ui.createElement( 'point', [ xCoord, yCoord ] );
};

GRAPH.createLine = function( xCoord1, yCoord1, xCoord2, yCoord2, dashed,
	lineColor )
{
	// createPoint( xCoord2, yCoord2);
	if ( dashed == true )
	{
		this.profileLines.push( this.ui.createElement(
			'line', [ [ xCoord1, yCoord1 ], [ xCoord2, yCoord2 ] ],
			{
				straightFirst : false,
				straightLast : false,
				strokeWidth : 3,
				strokeColor : lineColor,
				dash : 2
			} ));
	}
	else
	{
		this.profileLines.push( this.ui.createElement(
			'line', [ [ xCoord1, yCoord1 ], [ xCoord2, yCoord2 ] ],
			{
				straightFirst : false,
				straightLast : false,
				strokeWidth : 3,
				strokeColor : lineColor
			} ) );
	}
};

GRAPH.handleUpdate = function( model )
{
	GRAPH.updateGraph( model );
};
