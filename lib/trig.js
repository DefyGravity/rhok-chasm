var TRIG = {};
// Convert degrees to radians
TRIG.degreesToRadians = function(degrees) {
	return degrees * Math.PI / 180.0;
};

// Convert radians to degrees
TRIG.radiansToDegrees = function(radians) {
	return radians * 180.0 / Math.PI;
};