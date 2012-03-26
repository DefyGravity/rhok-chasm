<?php
class Chasm_Trig_Functions
{
    /**
     * Converts the given angle from degrees to radians.
     * @static
     * @param float $degrees the angle in degrees
     * @return float the angle in radians
     */
    public static function degreesToRadians( $degrees ) {
        return $degrees * pi() / 180.0;
    }

    /**
     * Converts the given angle from radians to degrees.
     * @static
     * @param float $radians the angle in radians
     * @return float the angle in degrees
     */
    public static function radiansToDegrees( $radians ) {
        return $radians * 180.0 / pi();
    }

    /**
      * Computes the height (or altitude) of a right triangle given the length of the hypotenuse and the angle of opposite the height (theta) in degrees.
      * @static
      * @param float $l the length of the hypotenuse
      * @param float $theta the angle (in degrees) opposite the height
      * @return float the height of the triangle
      */
    public static function getH( $l, $theta ) {
        return $l * sin( degreesToRadians( $theta ) );
    }

    /**
      * Computes the length of the hypotenuse of a right triangle given the length of the height and the angle of opposite the height (theta) in degrees.
      * @static
      * @param float $h the height of the triangle
      * @param float $theta the angle (in degrees) opposite the height
      * @return float the length of the hypotenuse
      */
    public static function getL( $h, $theta ) {
        return $h / sin( degreesToRadians( $theta ) );
    }

    /**
      * Computes the the angle of opposite the height (theta) in degrees given the height (or altitude) of a right triangle and the length of the hypotenuse.
      * @static
      * @param float $l the length of the hypotenuse
      * @param float $theta the angle (in degrees) opposite the height
      * @return float the height of the triangle
      */
    public static function getTheta( $h, $l ) {
        return radiansToDegrees( asin( $h / $l ) );
    }
}
?>