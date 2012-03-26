var graph = null;

// profile
var geometryLines = new Array();
var geometryPoints = new Array();
  
// soil
var soilOffsets = new Array();
var soilPoints = new Array();
var soilLines = new Array(4);
soilLines[0] = new Array();
soilLines[1] = new Array();
soilLines[2] = new Array();
soilLines[3] = new Array();
  
// water 
var waterTableOffsets = new Array();
var waterTableLines = new Array();
var waterTablePoints = new Array();
  
// establish line colors
var geometryColor = '#000000';
var soilColor = new Array('#DCBE84',  '#C7A675',  '#AA3961',  '#997B62');
var waterColor = '#B9E2FA';

function plotArrayOfPoints(pointsArray, dashed, lineColor) {
	// clear elements from array
	var storageArray = new Array(pointsArray.length);

	// for every pair of points in the array, create a line segment on the graph
	for ( var idx = 0; idx < pointsArray.length - 1; idx++) {
		storageArray[idx] = /*THIS.*/createLine(
				pointsArray[idx][0], 
				pointsArray[idx][1],
				pointsArray[idx + 1][0],
				pointsArray[idx + 1][1],
				dashed,
				lineColor);
	}

	/*THIS.*/updateBoundingBox();
	return storageArray;
}



function setSoilOffsets( inArray ) {
	if ( /*THIS.*/soilOffsets.length != inArray.length ) {
		/*THIS.*/soilOffsets = new Array( inArray.length );
	}
	/*THIS.*/soilOffsets = inArray;
}

function setWaterTableOffsets(inArray){
	if(/*THIS.*/waterTableOffsets.length != inArray.length){
		/*THIS.*/waterTableOffsets = new Array(inArray.length);
	}
	/*THIS.*/waterTableOffsets = inArray;
}

function setGeometry(inArray){
	if(/*THIS.*/geometryPoints.length != inArray.length){
		/*THIS.*/geometryPoints = new Array(inArray.length);
	}
	for(var x = 0; x < inArray.length; x++){
		/*THIS.*/geometryPoints[x] = new Array(inArray[x][0], inArray[x][1]);
	}
}

function updateGraph(){
	if(/*THIS.*/validateGeometry()){
		/*THIS.*/clearArrayOfElements(geometryLines);
		/*THIS.*/geometryLines = /*THIS.*/plotArrayOfPoints(
				/*THIS.*/geometryPoints, 
				false, 
				/*THIS.*/geometryColor
		);
	}
	if(/*THIS.*/validateSoilData()){
		/*THIS.*/convertSoilOffsetToPoints();
	
		for(var x = 0; x < /*THIS.*/soilPoints.length; x++){
			/*THIS.*/clearArrayOfElements(/*THIS.*/soilLines[x]);
			/*THIS.*/soilLines[x] = /*THIS.*/plotArrayOfPoints(
					/*THIS.*/soilPoints[x],
					false, 
					/*THIS.*/soilColor[x]
			);
		}
	}
	if(/*THIS.*/validateWaterTableData()){
		/*THIS.*/convertWaterTableOffsetToPoints();
		/*THIS.*/clearArrayOfElements(/*THIS.*/waterTableLines);
		/*THIS.*/waterTableLines = /*THIS.*/plotArrayOfPoints(
				/*THIS.*/waterTablePoints,
				false,
				waterColor);
	}
}

function validateWaterTableData(){
	//no negatives
	if(/*THIS.*/waterTableOffsets && /*THIS.*/waterTableOffsets.length > 0){
		for(var pNum = 0; pNum < /*THIS.*/waterTableOffsets[0].length; pNum++){

			if( /*THIS.*/waterTableOffsets[pNum] < 0 ){
				alert("Negative value in the water table");
				return false;
			}
		}
		return true;
	} else {
		return false;
	}
}

function validateSoilData(){
	//expected data format:
	//    p1     p2   ...
	// 0  offset offset
	// 1
	// ...
	// soilOffsets[depth][offset1, offset2, offset3 ...]
	//check dimensions - should be the same as the number of points in geometry
	//check values - should be increasing for each x,
	//the offset can't be greater than the geometry for that point (ie, no negative points)
	if (/*THIS.*/soilOffsets && /*THIS.*/soilOffsets.length > 0) {
		if (/*THIS.*/soilOffsets[0].length == /*THIS.*/geometryPoints.length) {
			for ( var depth = 0; depth < /*THIS.*/soilOffsets.length; depth++) {
				for ( var pNum = 0; pNum < /*THIS.*/soilOffsets[0].length; pNum++) {
					// check for increasing
					if (depth >= 1
							&& /*THIS.*/soilOffsets[depth][pNum] < /*THIS.*/soilOffsets[depth][pNum - 1]) {
						alert("Point number " + pNum + " at depth "	+ depth
								+ " is higher than the one that came before it.");
						return false;
					}
					// check to make sure offset is >= 0
					if ( /*THIS.*/soilOffsets[depth][pNum] < 0){
						alert("Point number " + pNum + " at depth "
								+ depth + " is negative");
						return false;
					}
				}
				return true;

			}
		} else {
			alert("There are  " + /*THIS.*/soilOffsets[0].length
					+ " data values for the soil table but "
					+ /*THIS.*/geometryPoints.length + " values for the geometry.");
			return false;
		}
	} else {
		return false;
	}

}

function convertSoilOffsetToPoints() {
	// soil data comes in as offsets in terms of each point in the geometry. So,
	// to calculate each value, we need to iterate through the geometry.
	// resize soil points array
	var depth = /*THIS.*/soilOffsets.length;
	var numPPoints = /*THIS.*/soilOffsets[0].length;
	/*THIS.*/soilPoints = new Array(depth);
	for ( var currentDepth = 0; currentDepth < depth; currentDepth++) {
		/*THIS.*/soilPoints[currentDepth] = new Array(numPPoints);
		for ( var pNum = 0; pNum < numPPoints; pNum++) {
			var currentGeometryXCoord = /*THIS.*/geometryPoints[pNum][0];
			var currentGeometryYCoord = /*THIS.*/geometryPoints[pNum][1];
			/*THIS.*/soilPoints[currentDepth][pNum] = new Array(
					currentGeometryXCoord,
					currentGeometryYCoord - /*THIS.*/soilOffsets[currentDepth][pNum]);
		}
	}

}

function convertWaterTableOffsetToPoints(array) {

	var numPPoints = /*THIS.*/waterTableOffsets.length;
	/*THIS.*/waterTablePoints = new Array(numPPoints);
	/*THIS.*/waterTablePoints = new Array(numPPoints);
	for ( var pNum = 0; pNum < numPPoints; pNum++) {
		var currentGeometryXCoord = /*THIS.*/geometryPoints[pNum][0];
		var currentGeometryYCoord = geometryPoints[pNum][1];
		/*THIS.*/waterTablePoints[pNum] = new Array(
				currentGeometryXCoord,
				currentGeometryYCoord - waterTableOffsets[pNum]);
	}
}

function validateGeometry() {
	// can't have slopes above 90 degrees.
	for ( var p = 1; p < /*THIS.*/geometryPoints.length; p++) {
		var slope = (/*THIS.*/geometryPoints[p][1] - /*THIS.*/geometryPoints[p - 1][1])
				/ (/*THIS.*/geometryPoints[p][0] - /*THIS.*/geometryPoints[p - 1][0]);
		if (/*THIS.*/geometryPoints[p][0] - /*THIS.*/geometryPoints[p - 1][0] == 0
				&& /*THIS.*/geometryPoints[p][1] - /*THIS.*/geometryPoints[p - 1][1] != 0) {
			// slope is undefined - vertical line
			return true;
		} else if (slope > 0) {
			alert("Point number " + p
					+ " has caused a slope greater than 0 degrees.");
			return false;
		}
		if (/*THIS.*/geometryPoints[p][0] < /*THIS.*/geometryPoints[p - 1][0]) {
			alert("Point number " + p
					+ " has a smaller X value than the one before it.");
			return false;
		}
	}
	return true;
}

function clearArrayOfElements(array) {
	if (array) {
		for ( var x = 0; x < array.length; x++) {
			/*THIS.*/graph.removeObject(array[x]);
		}
	}
}

function updateBoundingBox() {
	var lastPointNumber = /*THIS.*/geometryPoints.length;
	var leftGraphBuffer = /*THIS.*/geometryPoints[lastPointNumber - 1][0] / 20 * -1;
	var bottomGraphBuffer = /*THIS.*/geometryPoints[0][1] / 20 * -1;
	/*THIS.*/graph.setBoundingBox(
			new Array(
					leftGraphBuffer, 
					/*THIS.*/geometryPoints[0][1],
					/*THIS.*/geometryPoints[lastPointNumber - 1][0],
					bottomGraphBuffer)
			, false);
}

function clearElement(element) {
	/*THIS.*/graph.removeObject(element);
}

function createPoint( xCoord, yCoord){
	return /*THIS.*/graph.createElement( 'point', [ xCoord, yCoord ] );
}

function createLine( xCoord1, yCoord1, xCoord2, yCoord2, dashed, lineColor){
//	createPoint( xCoord2, yCoord2);
	if(dashed == true){
		return /*THIS.*/graph.createElement(
				'line',
				[ [xCoord1, yCoord1], [xCoord2, yCoord2] ],
				{
					straightFirst:false, 
					straightLast:false, 
					strokeWidth:3, 
					strokeColor: lineColor, 
					dash:2
				}
		);
	} else {
		return /*THIS.*/graph.createElement(
				'line',
				[ [xCoord1, yCoord1], [xCoord2,yCoord2] ],
				{
					straightFirst:false,
					straightLast:false,
					strokeWidth:3,
					strokeColor: lineColor
				}
		);
	}
}