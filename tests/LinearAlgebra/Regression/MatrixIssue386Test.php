<?php

namespace MathPHP\Tests\LinearAlgebra\Regression;

use MathPHP\LinearAlgebra\MatrixFactory;
use MathPHP\LinearAlgebra\Vector;

/**
 * Github issue #386 - https://github.com/markrogoyski/math-php/issues/386
 * Large 49x49 singular-ish matrix (depending on what the error tolerance is set to).
 * Very small determinant: 1.001788e-19
 * When doing Matrix->solve($b), it got a divide by zero error because it tried to solve using LU decomposition.
 */
class MatrixIssue386Test extends \PHPUnit\Framework\TestCase
{
    const MATRIX = [
        [1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0.25,0.583333333,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0.166666667,0.666666667,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0.166666667,0.583333333,0.25,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0.25,0,0,0,0,0,0,0.583333333,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0.0625,0.145833333,0.041666667,0,0,0,0,0.145833333,0.340277778,0.097222222,0,0,0,0,0.041666667,0.097222222,0.027777778,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0.041666667,0.166666667,0.041666667,0,0,0,0,0.097222222,0.388888889,0.097222222,0,0,0,0,0.027777778,0.111111111,0.027777778,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0.041666667,0.145833333,0.0625,0,0,0,0,0.097222222,0.340277778,0.145833333,0,0,0,0,0.027777778,0.097222222,0.041666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0.25,0,0,0,0,0,0,0.583333333,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0.666666667,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.041666667,0.097222222,0.027777778,0,0,0,0,0.166666667,0.388888889,0.111111111,0,0,0,0,0.041666667,0.097222222,0.027777778,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.027777778,0.111111111,0.027777778,0,0,0,0,0.111111111,0.444444444,0.111111111,0,0,0,0,0.027777778,0.111111111,0.027777778,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.027777778,0.097222222,0.041666667,0,0,0,0,0.111111111,0.388888889,0.166666667,0,0,0,0,0.027777778,0.097222222,0.041666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0.666666667,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0.583333333,0,0,0,0,0,0,0.25,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.041666667,0.097222222,0.027777778,0,0,0,0,0.145833333,0.340277778,0.097222222,0,0,0,0,0.0625,0.145833333,0.041666667,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.027777778,0.111111111,0.027777778,0,0,0,0,0.097222222,0.388888889,0.097222222,0,0,0,0,0.041666667,0.166666667,0.041666667,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.027777778,0.097222222,0.041666667,0,0,0,0,0.097222222,0.340277778,0.145833333,0,0,0,0,0.041666667,0.145833333,0.0625,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0.583333333,0,0,0,0,0,0,0.25,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.25,0.583333333,0.166666667,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0.666666667,0.166666667,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0.583333333,0.25,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1],
        [0.125,0,0,0,0,0,0,0.59375,0,0,0,0,0,0,0.260416667,0,0,0,0,0,0,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0.03125,0.072916667,0.020833333,0,0,0,0,0.1484375,0.346354167,0.098958333,0,0,0,0,0.065104167,0.151909722,0.043402778,0,0,0,0,0.005208333,0.012152778,0.003472222,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0.020833333,0.083333333,0.020833333,0,0,0,0,0.098958333,0.395833333,0.098958333,0,0,0,0,0.043402778,0.173611111,0.043402778,0,0,0,0,0.003472222,0.013888889,0.003472222,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0.020833333,0.072916667,0.03125,0,0,0,0,0.098958333,0.346354167,0.1484375,0,0,0,0,0.043402778,0.151909722,0.065104167,0,0,0,0,0.003472222,0.012152778,0.005208333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0.125,0,0,0,0,0,0,0.59375,0,0,0,0,0,0,0.260416667,0,0,0,0,0,0,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0,0,0,0,0,0,0.260416667,0,0,0,0,0,0,0.59375,0,0,0,0,0,0,0.125,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.005208333,0.012152778,0.003472222,0,0,0,0,0.065104167,0.151909722,0.043402778,0,0,0,0,0.1484375,0.346354167,0.098958333,0,0,0,0,0.03125,0.072916667,0.020833333,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.003472222,0.013888889,0.003472222,0,0,0,0,0.043402778,0.173611111,0.043402778,0,0,0,0,0.098958333,0.395833333,0.098958333,0,0,0,0,0.020833333,0.083333333,0.020833333,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.003472222,0.012152778,0.005208333,0,0,0,0,0.043402778,0.151909722,0.065104167,0,0,0,0,0.098958333,0.346354167,0.1484375,0,0,0,0,0.020833333,0.072916667,0.03125,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0,0,0,0,0,0,0.260416667,0,0,0,0,0,0,0.59375,0,0,0,0,0,0,0.125],
        [0.125,0.59375,0.260416667,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0.03125,0.1484375,0.065104167,0.005208333,0,0,0,0.072916667,0.346354167,0.151909722,0.012152778,0,0,0,0.020833333,0.098958333,0.043402778,0.003472222,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0.098958333,0.043402778,0.003472222,0,0,0,0.083333333,0.395833333,0.173611111,0.013888889,0,0,0,0.020833333,0.098958333,0.043402778,0.003472222,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0.098958333,0.043402778,0.003472222,0,0,0,0.072916667,0.346354167,0.151909722,0.012152778,0,0,0,0.03125,0.1484375,0.065104167,0.005208333,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.125,0.59375,0.260416667,0.020833333,0,0,0],
        [0,0,0,0.020833333,0.260416667,0.59375,0.125,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0.005208333,0.065104167,0.1484375,0.03125,0,0,0,0.012152778,0.151909722,0.346354167,0.072916667,0,0,0,0.003472222,0.043402778,0.098958333,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.003472222,0.043402778,0.098958333,0.020833333,0,0,0,0.013888889,0.173611111,0.395833333,0.083333333,0,0,0,0.003472222,0.043402778,0.098958333,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.003472222,0.043402778,0.098958333,0.020833333,0,0,0,0.012152778,0.151909722,0.346354167,0.072916667,0,0,0,0.005208333,0.065104167,0.1484375,0.03125,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0.260416667,0.59375,0.125],
        [0.015625,0.07421875,0.032552083,0.002604167,0,0,0,0.07421875,0.352539063,0.154622396,0.012369792,0,0,0,0.032552083,0.154622396,0.06781684,0.005425347,0,0,0,0.002604167,0.012369792,0.005425347,0.000434028,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0.002604167,0.032552083,0.07421875,0.015625,0,0,0,0.012369792,0.154622396,0.352539063,0.07421875,0,0,0,0.005425347,0.06781684,0.154622396,0.032552083,0,0,0,0.000434028,0.005425347,0.012369792,0.002604167,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.002604167,0.012369792,0.005425347,0.000434028,0,0,0,0.032552083,0.154622396,0.06781684,0.005425347,0,0,0,0.07421875,0.352539063,0.154622396,0.012369792,0,0,0,0.015625,0.07421875,0.032552083,0.002604167,0,0,0],
        [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.000434028,0.005425347,0.012369792,0.002604167,0,0,0,0.005425347,0.06781684,0.154622396,0.032552083,0,0,0,0.012369792,0.154622396,0.352539063,0.07421875,0,0,0,0.002604167,0.032552083,0.07421875,0.015625],
    ];

    const B = [
        0,
        -0.100074402,
        -0.279235927,
        -0.506044043,
        -0.768516341,
        -0.955919161,
        -1.117129522,
        -1.352203887,
        -1.630181622,
        -1.939321332,
        -1.88849433,
        -2.106903819,
        -2.392150397,
        -2.714509406,
        -3.062767193,
        -2.774951587,
        -3.045226542,
        -3.37367948,
        -3.732547604,
        -4.11141885,
        -3.593643233,
        -3.909183413,
        -4.272821737,
        -4.65943525,
        -5.059667743,
        -0.479425294,
        -0.610468305,
        -0.818221399,
        -1.071388315,
        -1.358070774,
        -3.194062398,
        -3.487868198,
        -3.834975015,
        -4.208858884,
        -4.599605356,
        -0.035621956,
        -1.022823342,
        -1.985046905,
        -2.898794698,
        -3.741752572,
        -0.633344193,
        -1.781366011,
        -2.885886041,
        -3.919931388,
        -4.858250183,
        -0.530845567,
        -1.211057585,
        -3.330455609,
        -4.402550402,
    ];

    const X = [
        0.00000000,
        -0.01139036,
        -0.08909642,
        -0.27152341,
        -0.50022549,
        -0.67596776,
        -0.76851634,
        -0.31994364,
        -0.33853214,
        -0.43014886,
        -0.63201718,
        -0.87876797,
        -1.06563542,
        -1.16352499,
        -0.95982455,
        -0.99271596,
        -1.11151769,
        -1.35077506,
        -1.63175101,
        -1.83943015,
        -1.94724405,
        -1.89621359,
        -1.94975644,
        -2.10643553,
        -2.39593601,
        -2.72139070,
        -2.95521779,
        -3.07528632,
        -2.78628705,
        -2.85917348,
        -3.04990309,
        -3.38257539,
        -3.74455966,
        -3.99882324,
        -4.12821379,
        -3.33432751,
        -3.41892359,
        -3.62924964,
        -3.98528957,
        -4.36573594,
        -4.62948616,
        -4.76298568,
        -3.59364323,
        -3.68372552,
        -3.90295272,
        -4.26917767,
        -4.65726700,
        -4.92466621,
        -5.05966774,
    ];

    /**
     * @test   Large 49x49 matrix with lots of zeros and small floating point values should be recognized as a singular matrix.
     *         The singular determination uses the determinant to see if it is zero.
     *         The determinant has to take in the floating point error tolerance, otherwise, smallNumber E-19 is not going to equal 0.
     *
     *         R Shows this matrix as being singular:
     *         > M = rbind(c(1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0.25,0.583333333,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0.166666667,0.666666667,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0.166666667,0.583333333,0.25,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0.25,0,0,0,0,0,0,0.583333333,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0.0625,0.145833333,0.041666667,0,0,0,0,0.145833333,0.340277778,0.097222222,0,0,0,0,0.041666667,0.097222222,0.027777778,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0.041666667,0.166666667,0.041666667,0,0,0,0,0.097222222,0.388888889,0.097222222,0,0,0,0,0.027777778,0.111111111,0.027777778,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0.041666667,0.145833333,0.0625,0,0,0,0,0.097222222,0.340277778,0.145833333,0,0,0,0,0.027777778,0.097222222,0.041666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0.25,0,0,0,0,0,0,0.583333333,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0.666666667,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.041666667,0.097222222,0.027777778,0,0,0,0,0.166666667,0.388888889,0.111111111,0,0,0,0,0.041666667,0.097222222,0.027777778,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.027777778,0.111111111,0.027777778,0,0,0,0,0.111111111,0.444444444,0.111111111,0,0,0,0,0.027777778,0.111111111,0.027777778,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.027777778,0.097222222,0.041666667,0,0,0,0,0.111111111,0.388888889,0.166666667,0,0,0,0,0.027777778,0.097222222,0.041666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0.666666667,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0.583333333,0,0,0,0,0,0,0.25,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.041666667,0.097222222,0.027777778,0,0,0,0,0.145833333,0.340277778,0.097222222,0,0,0,0,0.0625,0.145833333,0.041666667,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.027777778,0.111111111,0.027777778,0,0,0,0,0.097222222,0.388888889,0.097222222,0,0,0,0,0.041666667,0.166666667,0.041666667,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.027777778,0.097222222,0.041666667,0,0,0,0,0.097222222,0.340277778,0.145833333,0,0,0,0,0.041666667,0.145833333,0.0625,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0.583333333,0,0,0,0,0,0,0.25,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.25,0.583333333,0.166666667,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0.666666667,0.166666667,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0.583333333,0.25,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1),
     *           c(0.125,0,0,0,0,0,0,0.59375,0,0,0,0,0,0,0.260416667,0,0,0,0,0,0,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0.03125,0.072916667,0.020833333,0,0,0,0,0.1484375,0.346354167,0.098958333,0,0,0,0,0.065104167,0.151909722,0.043402778,0,0,0,0,0.005208333,0.012152778,0.003472222,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0.020833333,0.083333333,0.020833333,0,0,0,0,0.098958333,0.395833333,0.098958333,0,0,0,0,0.043402778,0.173611111,0.043402778,0,0,0,0,0.003472222,0.013888889,0.003472222,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0.020833333,0.072916667,0.03125,0,0,0,0,0.098958333,0.346354167,0.1484375,0,0,0,0,0.043402778,0.151909722,0.065104167,0,0,0,0,0.003472222,0.012152778,0.005208333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0.125,0,0,0,0,0,0,0.59375,0,0,0,0,0,0,0.260416667,0,0,0,0,0,0,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0,0,0,0,0,0,0.260416667,0,0,0,0,0,0,0.59375,0,0,0,0,0,0,0.125,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.005208333,0.012152778,0.003472222,0,0,0,0,0.065104167,0.151909722,0.043402778,0,0,0,0,0.1484375,0.346354167,0.098958333,0,0,0,0,0.03125,0.072916667,0.020833333,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.003472222,0.013888889,0.003472222,0,0,0,0,0.043402778,0.173611111,0.043402778,0,0,0,0,0.098958333,0.395833333,0.098958333,0,0,0,0,0.020833333,0.083333333,0.020833333,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.003472222,0.012152778,0.005208333,0,0,0,0,0.043402778,0.151909722,0.065104167,0,0,0,0,0.098958333,0.346354167,0.1484375,0,0,0,0,0.020833333,0.072916667,0.03125,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0,0,0,0,0,0,0.260416667,0,0,0,0,0,0,0.59375,0,0,0,0,0,0,0.125),
     *           c(0.125,0.59375,0.260416667,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0.03125,0.1484375,0.065104167,0.005208333,0,0,0,0.072916667,0.346354167,0.151909722,0.012152778,0,0,0,0.020833333,0.098958333,0.043402778,0.003472222,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0.098958333,0.043402778,0.003472222,0,0,0,0.083333333,0.395833333,0.173611111,0.013888889,0,0,0,0.020833333,0.098958333,0.043402778,0.003472222,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0.098958333,0.043402778,0.003472222,0,0,0,0.072916667,0.346354167,0.151909722,0.012152778,0,0,0,0.03125,0.1484375,0.065104167,0.005208333,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.125,0.59375,0.260416667,0.020833333,0,0,0),
     *           c(0,0,0,0.020833333,0.260416667,0.59375,0.125,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0.005208333,0.065104167,0.1484375,0.03125,0,0,0,0.012152778,0.151909722,0.346354167,0.072916667,0,0,0,0.003472222,0.043402778,0.098958333,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.003472222,0.043402778,0.098958333,0.020833333,0,0,0,0.013888889,0.173611111,0.395833333,0.083333333,0,0,0,0.003472222,0.043402778,0.098958333,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.003472222,0.043402778,0.098958333,0.020833333,0,0,0,0.012152778,0.151909722,0.346354167,0.072916667,0,0,0,0.005208333,0.065104167,0.1484375,0.03125,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0.260416667,0.59375,0.125),
     *           c(0.015625,0.07421875,0.032552083,0.002604167,0,0,0,0.07421875,0.352539063,0.154622396,0.012369792,0,0,0,0.032552083,0.154622396,0.06781684,0.005425347,0,0,0,0.002604167,0.012369792,0.005425347,0.000434028,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0.002604167,0.032552083,0.07421875,0.015625,0,0,0,0.012369792,0.154622396,0.352539063,0.07421875,0,0,0,0.005425347,0.06781684,0.154622396,0.032552083,0,0,0,0.000434028,0.005425347,0.012369792,0.002604167,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.002604167,0.012369792,0.005425347,0.000434028,0,0,0,0.032552083,0.154622396,0.06781684,0.005425347,0,0,0,0.07421875,0.352539063,0.154622396,0.012369792,0,0,0,0.015625,0.07421875,0.032552083,0.002604167,0,0,0),
     *           c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.000434028,0.005425347,0.012369792,0.002604167,0,0,0,0.005425347,0.06781684,0.154622396,0.032552083,0,0,0,0.012369792,0.154622396,0.352539063,0.07421875,0,0,0,0.002604167,0.032552083,0.07421875,0.015625))
     *         > is.singular.matrix(M)
     *         [1] TRUE
     * @throws \Exception
     */
    public function testLargeSingularMatrixWithLotsOfFloatingPointValues()
    {
        // Given
        $A = MatrixFactory::create(self::MATRIX);

        // When
        $isSingular    = $A->isSingular();
        $isNonsingular = $A->isNonsingular();

        // Then
        $this->assertTrue($isSingular);
        $this->assertFalse($isNonsingular);
    }

    /**
     * @test If the error tolerance is set to an extremely low value, then the matrix will not identify as being singular.
     *
     * R behavior:
     *  > det(M)
     *  [1] 1.001788e-19
     *  > is.singular.matrix(M, tol=1e-19)
     *  [1] FALSE
     * @throws \Exception
     */
    public function testLargeSingularMatrixWithLotsOfFloatingPointValuesUsingErrorTolerance()
    {
        // Given
        $A = MatrixFactory::create(self::MATRIX);

        // And
        $ε = 1e-19;
        $A->setError($ε);

        // When
        $isSingular    = $A->isSingular();
        $isNonsingular = $A->isNonsingular();

        // Then
        $this->assertFalse($isSingular);
        $this->assertTrue($isNonsingular);
    }

    /**
     * @test Determinant of large singular matrix with lots of zeros and floating point values.
     *
     * R behavior:
     *  > det(M)
     *  [1] 1.001788e-19
     * @throws \Exception
     */
    public function testDeterminantOfSingularMatrix()
    {
        // Given
        $A = MatrixFactory::create(self::MATRIX);

        // When
        $det = $A->det();

        // Then
        $this->assertEquals(1.001788e-19, $det, '', 1e-25);
    }

    /**
     * @test   Augmenting A with b and then computing the RREF solves Ax = b
     *         The right most column of augmented Ab is x
     * @throws \Exception
     */
    public function testSolveRref()
    {
        // Given
        $A = MatrixFactory::create(self::MATRIX);
        $b = new Vector(self::B);

        // When
        $Ab   = $A->augment($b->asColumnMatrix());
        $rref = $Ab->rref();
        $x    = new Vector(\array_column($rref->getMatrix(), $rref->getN() - 1));

        // Then
        $this->assertEquals(self::X, $x->getVector(), '', 0.00000001);
    }

    /**
     * @test   Solve (bug that was reported)
     *         Original implementation of Matrix->solve($b) would use LU decomposition and fail by division by zero for this matrix.
     * @throws \Exception
     */
    public function testSolve()
    {
        // Given
        $A = MatrixFactory::create(self::MATRIX);
        $b = new Vector(self::B);

        // When
        $x = $A->solve($b);

        // Then
        $this->assertEquals(self::X, $x->getVector(), '', 0.00000001);
    }

    /*
     * R code to create the matrix (for verification purposes)
     *  > A = rbind(c(1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0.25,0.583333333,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0.166666667,0.666666667,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0.166666667,0.583333333,0.25,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0.25,0,0,0,0,0,0,0.583333333,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0.0625,0.145833333,0.041666667,0,0,0,0,0.145833333,0.340277778,0.097222222,0,0,0,0,0.041666667,0.097222222,0.027777778,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0.041666667,0.166666667,0.041666667,0,0,0,0,0.097222222,0.388888889,0.097222222,0,0,0,0,0.027777778,0.111111111,0.027777778,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0.041666667,0.145833333,0.0625,0,0,0,0,0.097222222,0.340277778,0.145833333,0,0,0,0,0.027777778,0.097222222,0.041666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0.25,0,0,0,0,0,0,0.583333333,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0.666666667,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.041666667,0.097222222,0.027777778,0,0,0,0,0.166666667,0.388888889,0.111111111,0,0,0,0,0.041666667,0.097222222,0.027777778,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.027777778,0.111111111,0.027777778,0,0,0,0,0.111111111,0.444444444,0.111111111,0,0,0,0,0.027777778,0.111111111,0.027777778,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.027777778,0.097222222,0.041666667,0,0,0,0,0.111111111,0.388888889,0.166666667,0,0,0,0,0.027777778,0.097222222,0.041666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0.666666667,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0.583333333,0,0,0,0,0,0,0.25,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.041666667,0.097222222,0.027777778,0,0,0,0,0.145833333,0.340277778,0.097222222,0,0,0,0,0.0625,0.145833333,0.041666667,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.027777778,0.111111111,0.027777778,0,0,0,0,0.097222222,0.388888889,0.097222222,0,0,0,0,0.041666667,0.166666667,0.041666667,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.027777778,0.097222222,0.041666667,0,0,0,0,0.097222222,0.340277778,0.145833333,0,0,0,0,0.041666667,0.145833333,0.0625,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0,0,0,0,0,0,0.583333333,0,0,0,0,0,0,0.25,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.25,0.583333333,0.166666667,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0.666666667,0.166666667,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.166666667,0.583333333,0.25,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1),
     *    c(0.125,0,0,0,0,0,0,0.59375,0,0,0,0,0,0,0.260416667,0,0,0,0,0,0,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0.03125,0.072916667,0.020833333,0,0,0,0,0.1484375,0.346354167,0.098958333,0,0,0,0,0.065104167,0.151909722,0.043402778,0,0,0,0,0.005208333,0.012152778,0.003472222,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0.020833333,0.083333333,0.020833333,0,0,0,0,0.098958333,0.395833333,0.098958333,0,0,0,0,0.043402778,0.173611111,0.043402778,0,0,0,0,0.003472222,0.013888889,0.003472222,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0.020833333,0.072916667,0.03125,0,0,0,0,0.098958333,0.346354167,0.1484375,0,0,0,0,0.043402778,0.151909722,0.065104167,0,0,0,0,0.003472222,0.012152778,0.005208333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0.125,0,0,0,0,0,0,0.59375,0,0,0,0,0,0,0.260416667,0,0,0,0,0,0,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0,0,0,0,0,0,0.260416667,0,0,0,0,0,0,0.59375,0,0,0,0,0,0,0.125,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.005208333,0.012152778,0.003472222,0,0,0,0,0.065104167,0.151909722,0.043402778,0,0,0,0,0.1484375,0.346354167,0.098958333,0,0,0,0,0.03125,0.072916667,0.020833333,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.003472222,0.013888889,0.003472222,0,0,0,0,0.043402778,0.173611111,0.043402778,0,0,0,0,0.098958333,0.395833333,0.098958333,0,0,0,0,0.020833333,0.083333333,0.020833333,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.003472222,0.012152778,0.005208333,0,0,0,0,0.043402778,0.151909722,0.065104167,0,0,0,0,0.098958333,0.346354167,0.1484375,0,0,0,0,0.020833333,0.072916667,0.03125,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0,0,0,0,0,0,0.260416667,0,0,0,0,0,0,0.59375,0,0,0,0,0,0,0.125),
     *    c(0.125,0.59375,0.260416667,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0.03125,0.1484375,0.065104167,0.005208333,0,0,0,0.072916667,0.346354167,0.151909722,0.012152778,0,0,0,0.020833333,0.098958333,0.043402778,0.003472222,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0.098958333,0.043402778,0.003472222,0,0,0,0.083333333,0.395833333,0.173611111,0.013888889,0,0,0,0.020833333,0.098958333,0.043402778,0.003472222,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0.098958333,0.043402778,0.003472222,0,0,0,0.072916667,0.346354167,0.151909722,0.012152778,0,0,0,0.03125,0.1484375,0.065104167,0.005208333,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.125,0.59375,0.260416667,0.020833333,0,0,0),
     *    c(0,0,0,0.020833333,0.260416667,0.59375,0.125,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0.005208333,0.065104167,0.1484375,0.03125,0,0,0,0.012152778,0.151909722,0.346354167,0.072916667,0,0,0,0.003472222,0.043402778,0.098958333,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.003472222,0.043402778,0.098958333,0.020833333,0,0,0,0.013888889,0.173611111,0.395833333,0.083333333,0,0,0,0.003472222,0.043402778,0.098958333,0.020833333,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.003472222,0.043402778,0.098958333,0.020833333,0,0,0,0.012152778,0.151909722,0.346354167,0.072916667,0,0,0,0.005208333,0.065104167,0.1484375,0.03125,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.020833333,0.260416667,0.59375,0.125),
     *    c(0.015625,0.07421875,0.032552083,0.002604167,0,0,0,0.07421875,0.352539063,0.154622396,0.012369792,0,0,0,0.032552083,0.154622396,0.06781684,0.005425347,0,0,0,0.002604167,0.012369792,0.005425347,0.000434028,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0.002604167,0.032552083,0.07421875,0.015625,0,0,0,0.012369792,0.154622396,0.352539063,0.07421875,0,0,0,0.005425347,0.06781684,0.154622396,0.032552083,0,0,0,0.000434028,0.005425347,0.012369792,0.002604167,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.002604167,0.012369792,0.005425347,0.000434028,0,0,0,0.032552083,0.154622396,0.06781684,0.005425347,0,0,0,0.07421875,0.352539063,0.154622396,0.012369792,0,0,0,0.015625,0.07421875,0.032552083,0.002604167,0,0,0),
     *    c(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0.000434028,0.005425347,0.012369792,0.002604167,0,0,0,0.005425347,0.06781684,0.154622396,0.032552083,0,0,0,0.012369792,0.154622396,0.352539063,0.07421875,0,0,0,0.002604167,0.032552083,0.07421875,0.015625))
     */

    /*
     * R code to create b vector (for verification purposes)
     *  > b = c(0,
     *    -0.100074402,
     *    -0.279235927,
     *    -0.506044043,
     *    -0.768516341,
     *    -0.955919161,
     *    -1.117129522,
     *    -1.352203887,
     *    -1.630181622,
     *    -1.939321332,
     *    -1.88849433,
     *    -2.106903819,
     *    -2.392150397,
     *    -2.714509406,
     *    -3.062767193,
     *    -2.774951587,
     *    -3.045226542,
     *    -3.37367948,
     *    -3.732547604,
     *    -4.11141885,
     *    -3.593643233,
     *    -3.909183413,
     *    -4.272821737,
     *    -4.65943525,
     *    -5.059667743,
     *    -0.479425294,
     *    -0.610468305,
     *    -0.818221399,
     *    -1.071388315,
     *    -1.358070774,
     *    -3.194062398,
     *    -3.487868198,
     *    -3.834975015,
     *    -4.208858884,
     *    -4.599605356,
     *    -0.035621956,
     *    -1.022823342,
     *    -1.985046905,
     *    -2.898794698,
     *    -3.741752572,
     *    -0.633344193,
     *    -1.781366011,
     *    -2.885886041,
     *    -3.919931388,
     *    -4.858250183,
     *    -0.530845567,
     *    -1.211057585,
     *    -3.330455609,
     *    -4.402550402
     *  )
     */

    /*
     * R code to solve Ax = b
     *  > solve(A, b)
     *              [,1]
     *  [1,]  0.00000000
     *  [2,] -0.01139036
     *  [3,] -0.08909642
     *  [4,] -0.27152341
     *  [5,] -0.50022549
     *  [6,] -0.67596776
     *  [7,] -0.76851634
     *  [8,] -0.31994364
     *  [9,] -0.33853214
     * [10,] -0.43014886
     * [11,] -0.63201718
     * [12,] -0.87876797
     * [13,] -1.06563542
     * [14,] -1.16352499
     * [15,] -0.95982455
     * [16,] -0.99271596
     * [17,] -1.11151769
     * [18,] -1.35077506
     * [19,] -1.63175101
     * [20,] -1.83943015
     * [21,] -1.94724405
     * [22,] -1.89621359
     * [23,] -1.94975644
     * [24,] -2.10643553
     * [25,] -2.39593601
     * [26,] -2.72139070
     * [27,] -2.95521779
     * [28,] -3.07528632
     * [29,] -2.78628705
     * [30,] -2.85917348
     * [31,] -3.04990309
     * [32,] -3.38257539
     * [33,] -3.74455966
     * [34,] -3.99882324
     * [35,] -4.12821379
     * [36,] -3.33432751
     * [37,] -3.41892359
     * [38,] -3.62924964
     * [39,] -3.98528957
     * [40,] -4.36573594
     * [41,] -4.62948616
     * [42,] -4.76298568
     * [43,] -3.59364323
     * [44,] -3.68372552
     * [45,] -3.90295272
     * [46,] -4.26917767
     * [47,] -4.65726700
     * [48,] -4.92466621
     * [49,] -5.05966774
     */
}
