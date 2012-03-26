// Constant for Height
var H = 0;

// Constant for Length
var L = 1;

// Constant for Theta
var THETA = 2;

// Constant for X coordinate
var X = 0;

// Constant for Y coordinate
var Y = 1;

// Given, the div IDs for the h/l/theta form elements and completes the missing value
function autocomplete( h_id, l_id, theta_id ) {
	var h = document.getElementById(h_id).value;
	var l = document.getElementById(l_id).value;
	var theta = document.getElementById(theta_id).value;
	
	if ( l && theta && !h) {
        alert("missing height");
		document.getElementById(h_id).value = getH( l, theta );
	} else if ( h && theta && !l ) {
        alert("missing length");
		document.getElementById(l_id).value = getL( h, theta );
	} else if( h && l && !theta ) {
        alert("missing theta");
		document.getElementById(theta_id).value = getTheta( h, l );
	} else {
        alert("missing stuff");
    }
    
}

// Get the height value given length and theta (in degrees)
function getH( l, theta ) {
	return l * Math.sin( TRIG.degreesToRadians( theta ) );
}

// Get the length value given height and theta(in degrees)
function getL( h, theta ) {
	return h / Math.sin( TRIG.degreesToRadians( theta ) );
}

// Get the theta value (in degrees) given the height and length
function getTheta( h, l ) {
	return TRIG.radiansToDegrees( Math.asin( h / l ) );
}