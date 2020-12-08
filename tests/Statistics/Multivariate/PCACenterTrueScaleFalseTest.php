<?php

namespace MathPHP\Tests\Statistics\Multivariate;

use MathPHP\Functions\Map\Multi;
use MathPHP\LinearAlgebra\Matrix;
use MathPHP\LinearAlgebra\MatrixFactory;
use MathPHP\SampleData;
use MathPHP\Statistics\Multivariate\PCA;
use MathPHP\Exception;

class PCACenterTrueScaleFalseTest extends \PHPUnit\Framework\TestCase
{
    /** @var PCA */
    private static $pca;

    /** @var Matrix  */
    private static $matrix;

    /**
     * R code for expected values:
     *   library(mdatools)
     *   data = mtcars[,c(1:7,10,11)]
     *   model = pca(data, center=TRUE, scale=FALSE)
     * @throws Exception\MathException
     */
    public static function setUpBeforeClass()
    {
        $mtCars = new SampleData\MtCars();

        // Remove and categorical variables
        self::$matrix = MatrixFactory::create($mtCars->getData())->columnExclude(8)->columnExclude(7);
        self::$pca = new PCA(self::$matrix, true, false);
    }

    /**
     * @test The class returns the correct R-squared values
     *
     * R code for expected values:
     *   model$calres$expvar / 100
     */
    public function testRsq()
    {
        // Given
        $expected = [9.270116e-01, 7.236743e-02, 4.676043e-04, 8.083193e-05, 4.070624e-05, 2.083882e-05, 4.638724e-06, 4.065453e-06, 2.317617e-06];

        // When
        $R2 = self::$pca->getR2();

        // Then
        $this->assertEquals($expected, $R2, '', .00001);
    }

    /**
     * @test The class returns the correct cumulative R-squared values
     *
     * R code for expected values:
     *   model$calres$cumexpvar / 100
     */
    public function testCumRsq()
    {
        // Given
        $expected = [0.9270116, 0.9993790, 0.9998466, 0.9999274, 0.9999681, 0.9999890, 0.9999936, 0.9999977, 1.0000000];

        // When
        $cumR2 = self::$pca->getCumR2();

        // Then
        $this->assertEquals($expected, $cumR2, '', .00001);
    }

    /**
     * @test The class returns the correct loadings
     *
     * R code for expected values:
     *   model$loadings
     *
     * @throws \Exception
     */
    public function testLoadings()
    {
        // Given
        $expected = [
            [-0.038118360,  0.009186679,  0.98365680,  0.040854772, -0.09376515, -0.133111460,  0.0361602003, -2.292248e-02, -0.029482354],
            [ 0.012035198, -0.003372536, -0.06344057, -0.236548841,  0.22554404, -0.822715264, -0.4045222924,  1.914389e-01, -0.108839579],
            [ 0.899573033,  0.435385992,  0.03123388, -0.005079093, -0.01053658,  0.007340014, -0.0009963135,  6.285318e-04,  0.006305575],
            [ 0.434787255, -0.899322036,  0.02541113,  0.035168110,  0.01667875,  0.001658475,  0.0026203774, -4.804737e-05, -0.003098007],
            [-0.002660085, -0.003900050,  0.03953535, -0.057314901, -0.13086355,  0.237708227, -0.0334089045,  9.408348e-01, -0.187651070],
            [ 0.006239435,  0.004860835, -0.08487901,  0.133441861, -0.24405170, -0.126208722,  0.2228246459, -1.633642e-01, -0.907247320],
            [-0.006671307,  0.025010854, -0.07050906,  0.910589254, -0.20719280, -0.202621340, -0.2172666165,  1.036007e-01,  0.152523736],
            [-0.002604770, -0.011272257,  0.04811434, -0.130320795, -0.27275503,  0.350400573, -0.8450418392, -2.005106e-01, -0.170562485],
            [ 0.005766046, -0.027779493, -0.10353404, -0.271781001, -0.86367709, -0.262858319,  0.1515876824, -5.449811e-03,  0.276712312],
        ];

        // And since each column could be multiplied by -1, we will compare the two and adjust.
        $loadings   = self::$pca->getLoadings();
        $load_array = $loadings->getMatrix();

        // Get an array that's roughly ones and negative ones.
        $quotiant = Multi::divide($expected[1], $load_array[1]);

        // Convert to exactly one or negative one. Cannot be zero.
        $signum = \array_map(
            function ($x) {
                return $x <=> 0;
            },
            $quotiant
        );
        $sign_change = MatrixFactory::diagonal($signum);

        // Multiplying a sign change matrix on the right changes column signs.
        $sign_adjusted = $loadings->multiply($sign_change);

        // Then
        $this->assertEquals($expected, $sign_adjusted->getMatrix(), '', .00001);
    }


    /**
     * @test The class returns the correct scores
     *
     * R code for expected values:
     *   model$calres$scores
     *   new = matrix(c(1:9), 1, 9)
     *   result = predict(model, new)
     *   result$scores
     *
     * @throws \Exception
     */
    public function testScores()
    {
        // Given
        $expected = [
            [ -79.596905,    2.136219, -2.1820822, -2.57465751, -0.71135479, -0.32047992,  0.157501456,  0.070619060,  0.209703818],
            [ -79.599050,    2.151464, -2.2432114, -2.03069985, -0.88961594, -0.46613109,  0.092652435,  0.086977555,  0.063769043],
            [-133.892150,   -5.056248, -2.1582751,  0.37417681,  1.15846858,  1.05439146,  0.051833068, -0.144327723, -0.121563182],
            [   8.517325,   44.982954,  1.2417493,  0.72954922,  0.42700378, -0.09005525, -0.022872949, -0.220050034,  0.224865203],
            [ 128.685064,   30.817359,  3.3473414, -0.53539129,  0.71430291, -0.30387460, -0.135679710,  0.058643754,  0.218925213],
            [ -23.219555,   35.103458, -3.2505355,  1.34779162,  0.82121536, -0.16633269, -0.226610224, -0.425190005,  0.086304304],
            [ 159.307948,  -32.260279,  0.6655009,  0.14246662,  0.77193661,  0.10914095,  0.475154467,  0.058203700,  0.376032478],
            [-112.615721,   39.698710, -0.4451302,  0.27197501, -1.25942420,  0.38172207,  0.038690309, -0.309295378, -0.099231680],
            [-103.534441,    7.509047, -1.5566816,  4.01931038, -1.11803119,  0.07824334, -0.573485531,  0.245455187,  0.243951944],
            [ -67.046515,   -6.212642, -3.5834890, -0.44585152, -0.98980621, -0.47509527, -0.098816525,  0.191515011, -0.196631818],
            [ -66.997152,   -6.210497, -5.0029140,  0.04330535, -0.98285067, -0.41031203, -0.279800775,  0.285766884, -0.063842280],
            [  55.209997,  -10.374782, -1.6048702,  0.13703937,  0.81484237, -1.04583765,  0.090221923, -0.086064946, -0.481567193],
            [  55.172235,  -10.363165, -0.7048221,  0.31055628,  0.77199275, -1.16325126,  0.003552401, -0.030431215, -0.169132476],
            [  55.249927,  -10.372209, -2.8029489,  0.59566906,  0.87381987, -0.97107617, -0.148149434,  0.050978054, -0.091572404],
            [ 242.814250,   52.502787, -0.9935675,  0.19644174, -1.52638334,  0.67181962,  0.036476815, -0.096261377,  0.175925908],
            [ 236.369213,   38.281506, -1.1149827,  0.48258451, -1.25163156,  0.62742287,  0.145831874, -0.083427275, -0.126121465],
            [ 224.737216,   16.112173,  2.9152331,  0.89940350, -1.12185041,  0.07881235,  0.422172118, -0.007427785, -0.457973891],
            [-172.361930,    6.576858,  5.6422180,  0.71957237, -0.06227959, -0.58775620,  0.136140492, -0.056409984, -0.308820587],
            [-181.065241,   17.785137,  3.2721557, -1.10287537, -0.71194970, -0.16125592,  0.229364731,  0.779629259,  0.277650532],
            [-179.696168,    4.189461,  6.8611114,  1.11911054, -0.15786301, -0.85264781,  0.015899511,  0.140370594, -0.027409151],
            [-121.224045,   -3.349732, -3.1225193,  1.83337308,  1.18651284,  0.63485748,  0.581440409,  0.073623072,  0.261361819],
            [  80.157831,   34.982959, -1.7590943, -1.43556764,  1.10251051, -0.30007010, -0.211611180, -0.288736259,  0.103610356],
            [  67.570808,   28.893619, -2.4991510, -1.01885876,  1.15876684, -0.34659009, -0.333905063,  0.134700857,  0.093694327],
            [ 150.353506,  -36.634796, -0.6025347, -0.23292505,  0.92621743,  0.34551137,  0.585172071,  0.475418340, -0.065661454],
            [ 164.651439,   48.240384,  5.0492661, -0.63275394,  0.15006100, -0.15066251, -0.071387544, -0.056589142,  0.106683147],
            [-171.895505,    6.645077,  0.6976215, -0.04470866,  0.59553532,  0.24225372,  0.016218017,  0.044922380, -0.003086897],
            [-123.805717,    2.037081,  1.4402425, -1.82648643, -0.07720272,  1.35053737, -0.187910222, -0.038600593, -0.262855744],
            [-137.080850,  -28.674781,  5.5533075, -0.60875625,  0.34063654,  0.49808620, -0.107164437, -0.654157034,  0.103563782],
            [ 159.414119,  -53.314034,  2.6072858, -0.67444128,  0.74059103,  1.13780719, -0.933670505,  0.492992984, -0.155938789],
            [ -64.762763,  -62.951467, -2.3926876, -1.77763922, -1.14846303, -0.21602357,  0.005250095, -0.510938030,  0.104971353],
            [ 145.362559, -139.046568,  1.5808340,  1.14005113, -0.95743816, -0.28877314, -0.030264214, -0.240059511,  0.219215280],
            [-115.179728,  -13.825052, -2.8543702,  0.57923616,  0.41173077,  1.10561930,  0.277756120,  0.058149599, -0.238819498],
        ];

        // And since each column could be multiplied by -1, we will compare the two and adjust.
        $scores = self::$pca->getScores();
        $score_array = $scores->getMatrix();

        // Get an array that's roughly ones and negative ones.
        $quotiant = Multi::divide($expected[1], $score_array[1]);

        // Convert to exactly one or negative one. Cannot be zero.
        $signum = \array_map(
            function ($x) {
                return $x <=> 0;
            },
            $quotiant
        );
        $signature = MatrixFactory::diagonal($signum);

        // Multiplying a sign change matrix on the right changes column signs.
        $sign_adjusted = $scores->multiply($signature);

        // Then
        $this->assertEquals($expected, $sign_adjusted->getMatrix(), '', .00001);

        // And Given
        $expected = MatrixFactory::create([[-266.1034, 28.53006, -29.10035, -15.48234, -4.270139, 6.143432, 1.080559, -1.65688, -3.441352]]);
        $sign_adjusted = $expected->multiply($signature);

        // When
        $scores = self::$pca->getScores(MatrixFactory::create([[1,2,3,4,5,6,7,8,9]]));

        // Then
        $this->assertEquals($sign_adjusted->getMatrix(), $scores->getMatrix(), '', .00001);
    }

    /**
     * @test The class returns the correct eigenvalues
     *
     * R code for expected values:
     *   model$eigenvals
     */
    public function testEigenvalues()
    {
        // Given
        $expected = [1.864106e+04, 1.455220e+03, 9.402948e+00, 1.625431e+00, 8.185525e-01, 4.190430e-01, 9.327903e-02, 8.175127e-02, 4.660443e-02];

        // When
        $eigenvalues = self::$pca->getEigenvalues()->getVector();

        // Then
        $this->assertEquals($expected, $eigenvalues, '', .01);
    }

    /**
     * @test The class returns the correct critical T² distances
     *
     * R code for expected values:
     *   model$T2lim
     */
    public function testCriticalT2()
    {
        // Given
        $expected = [4.159615, 6.852714, 9.40913, 12.01948, 14.76453, 17.69939, 20.87304, 24.33584, 28.14389];

        // When
        $criticalT2 = self::$pca->getCriticalT2();

        // Then
        $this->assertEquals($expected, $criticalT2, '', .00001);
    }

    /**
     * @test The class returns the correct critical Q distances
     *
     * R code for expected values:
     *   model$Qlim
     */
    public function testCriticalQ()
    {
        // Given
        $expected = [5472.807,40.26957, 8.489111, 4.141048, 1.9273752, 0.5910146, 0.3933023, 0.17461579, 0];

        // When
        $criticalQ = self::$pca->getCriticalQ();

        // Then
        $this->assertEquals($expected, $criticalQ, '', .001);
    }

    /**
     * @test The class returns the correct T² distances
     *
     * R code for expected values:
     *   model$calres$T2
     *
     * @throws \Exception
     */
    public function testGetT²Distances()
    {
        // Given
        $expected = [
            [0.339876943,  0.3430128,  0.8493947,  4.927613,  5.545809,  5.790908,  6.056849,  6.117852,  7.061447],
            [0.339895260,  0.3430761,  0.8782271,  3.415242,  4.382091,  4.900601,  4.992631,  5.085169,  5.172425],
            [0.961699895,  0.9792681,  1.4746608,  1.560797,  3.200337,  5.853385,  5.882188,  6.136991,  6.454077],
            [0.003891668,  1.3943799,  1.5583647,  1.885812,  2.108561,  2.127915,  2.133523,  2.725832,  3.810801],
            [0.888353100,  1.5409758,  2.7325908,  2.908940,  3.532271,  3.752629,  3.949983,  3.992051,  5.020456],
            [0.028922586,  0.8757037,  1.9993918,  3.116968,  3.940855,  4.006878,  4.557400,  6.768822,  6.928645],
            [1.361457931,  2.0766251,  2.1237265,  2.136213,  2.864189,  2.892615,  5.313006,  5.354445,  8.388501],
            [0.680342156,  1.7633314,  1.7844036,  1.829912,  3.767661,  4.115386,  4.131434,  5.301613,  5.512901],
            [0.575041286,  0.6137885,  0.8715011, 10.810316, 12.337395, 12.352004, 15.877830, 16.614800, 17.891772],
            [0.241146939,  0.2676700,  1.6333474,  1.755643,  2.952532,  3.491177,  3.595860,  4.044514,  4.874136],
            [0.240791981,  0.2672968,  2.9291372,  2.930291,  4.110417,  4.512180,  5.351474,  6.350391,  6.437847],
            [0.163517709,  0.2374832,  0.5113982,  0.522952,  1.334101,  3.944278,  4.031543,  4.122149,  9.098221],
            [0.163294101,  0.2370941,  0.2899258,  0.349261,  1.077342,  4.306495,  4.306630,  4.317958,  4.931757],
            [0.163754318,  0.2376832,  1.0732214,  1.291515,  2.224334,  4.474674,  4.709970,  4.741759,  4.921688],
            [3.162843318,  5.0570880,  5.1620738,  5.185815,  8.032115,  9.109192,  9.123457,  9.236803,  9.900902],
            [2.997168473,  4.0042147,  4.1364271,  4.279705,  6.193548,  7.132974,  7.360966,  7.446104,  7.787415],
            [2.709438704,  2.8878324,  3.7916536,  4.289323,  5.826852,  5.841675,  7.752386,  7.753061, 12.253493],
            [1.593720042,  1.6234441,  5.0090443,  5.327596,  5.332335,  6.156731,  6.355428,  6.394352,  8.440727],
            [1.758731382,  1.9760945,  3.1147802,  3.863095,  4.482325,  4.544380,  5.108367, 12.543381, 14.197511],
            [1.732235644,  1.7442968,  6.7506893,  7.521198,  7.551643,  9.286568,  9.289278,  9.530301,  9.546421],
            [0.788327867,  0.7960385,  1.8329608,  3.900878,  5.620759,  6.582580, 10.206898, 10.273201, 11.738942],
            [0.344684102,  1.1856617,  1.5147513,  2.782633,  4.267608,  4.482483,  4.962541,  5.982325,  6.212670],
            [0.244933144,  0.8186205,  1.4828542,  2.121499,  3.761883,  4.048548,  5.243807,  5.465752,  5.654117],
            [1.212708604,  2.1349804,  2.1735904,  2.206969,  3.255012,  3.539895,  7.210885,  9.975644, 10.068155],
            [1.454321415,  3.0534849,  5.7648779,  6.011199,  6.038709,  6.092878,  6.147512,  6.186683,  6.430894],
            [1.585106253,  1.6154502,  1.6672079,  1.668438,  2.101718,  2.241767,  2.244587,  2.269272,  2.269476],
            [0.822262985,  0.8251146,  1.0457154,  3.098127,  3.105408,  7.458067,  7.836612,  7.854838,  9.337382],
            [1.008051950,  1.5730820,  4.8528219,  5.080813,  5.222567,  5.814607,  5.937723, 11.172155, 11.402293],
            [1.363273219,  3.3165080,  4.0394663,  4.319313,  4.989368,  8.078801, 17.424316, 20.397262, 20.919034],
            [0.224998733,  2.9482209,  3.5570675,  5.501168,  7.112509,  7.223873,  7.224169, 10.417485, 10.653922],
            [1.133533749, 14.4194622, 14.6852338, 15.484847, 16.604736, 16.803737, 16.813556, 17.518482, 18.549614],
            [0.711674544,  0.8430169,  1.7094930,  1.915909,  2.123009,  5.040118,  5.867190,  5.908551,  7.132357],
        ];

        // When
        $T²Distances = self::$pca->getT2Distances()->getMatrix();

        // Then
        $this->assertEquals($expected, $T²Distances, '', .00001);
    }

    /**
     * @test The class returns the correct T² distances
     *
     * library(mdatools)
     *   new = matrix(c(1:9), 1, 9)
     *   result = predict(model, new)
     *   result$T2
     *
     * @throws \Exception
     */
    public function testT2WithNewData()
    {
        // Given
        $expected = [[3.798658, 4.357999, 94.41811, 241.8884, 264.1644, 354.2309, 366.7483, 400.3288, 654.4443]];
        $newdata  = MatrixFactory::create([[1,2,3,4,5,6,7,8,9]]);

        // When
        $T²Distances = self::$pca->getT2Distances($newdata)->getMatrix();

        // Then
        $this->assertEquals($expected, $T²Distances, '', .0001);
    }

    /**
     * @test The class returns the correct Q residuals
     *
     * R code for expected values:
     *   model$calres$Q
     *
     * @throws \Exception
     */
    public function testGetQResiduals()
    {
        // Given
        $expected = [
            [   16.63628, 12.0728464,  7.3113637, 0.6825025, 0.17647683, 0.073769451, 0.048962743, 4.397569e-02, 3.588701e-28],
            [   14.81345, 10.1846500,  5.1526527, 1.0289108, 0.23749426, 0.020216060, 0.011631586, 4.066491e-03, 5.071089e-28],
            [   32.85589,  7.2902451,  2.6320938, 2.4920856, 1.15003611, 0.038294766, 0.035608099, 1.477761e-02, 1.201312e-27],
            [ 2025.83029,  2.3641352,  0.8221938, 0.2899517, 0.10761950, 0.099509549, 0.098986377, 5.056436e-02, 2.211253e-28],
            [  961.87329, 12.1636827,  0.9589886, 0.6723447, 0.16211610, 0.069776322, 0.051367339, 4.792825e-02, 3.401131e-28],
            [ 1245.57692, 13.3241715,  2.7581907, 0.9416484, 0.26725373, 0.239587166, 0.188234973, 7.448433e-03, 2.078479e-28],
            [ 1042.16715,  1.4415460,  0.9986545, 0.9783577, 0.38247161, 0.370559863, 0.144788095, 1.414004e-01, 1.713313e-27],
            [ 1578.09853,  2.1109798,  1.9128390, 1.8388686, 0.25271923, 0.107007497, 0.105510557, 9.846926e-03, 7.616415e-28],
            [   76.66866, 20.2828759, 17.8596182, 1.7047622, 0.45476847, 0.448646454, 0.119760800, 5.951255e-02, 2.136652e-27],
            [   52.92764, 14.3307158,  1.4893222, 1.2905386, 0.31082229, 0.085106777, 0.075342071, 3.866407e-02, 5.287772e-28],
            [   64.89968, 26.3294022,  1.3002538, 1.2983784, 0.33238298, 0.164027022, 0.085738549, 4.075837e-03, 5.387897e-28],
            [  112.23570,  4.5995869,  2.0239784, 2.0051986, 1.34123052, 0.247454132, 0.239314137, 2.319070e-01, 1.022969e-28],
            [  109.96708,  2.5718901,  2.0751160, 1.9786708, 1.38269798, 0.029544473, 0.029531853, 2.860579e-02, 2.070421e-28],
            [  117.53356,  9.9508270,  2.0943042, 1.7394826, 0.97592145, 0.032932522, 0.010984267, 8.385505e-03, 2.035954e-28],
            [ 2760.39114,  3.8485001,  2.8613238, 2.8227344, 0.49288834, 0.041546736, 0.040216178, 3.094993e-02, 1.509981e-27],
            [ 1468.95413,  3.4804490,  2.2372625, 2.0043747, 0.43779313, 0.044133670, 0.022866734, 1.590662e-02, 2.715148e-27],
            [  270.56241, 10.9602950,  2.4617109, 1.6527843, 0.39423594, 0.388024554, 0.209795257, 2.097401e-01, 1.648442e-27],
            [   76.07389, 32.8188304,  0.9842070, 0.4664226, 0.46254383, 0.117086475, 0.098552241, 9.537015e-02, 1.646972e-27],
            [  329.50482, 13.1937329,  2.4867297, 1.2703956, 0.76352325, 0.737519779, 0.684911599, 7.708982e-02, 1.561513e-27],
            [   66.65148, 49.0998957,  2.0250454, 0.7726370, 0.74771625, 0.020707960, 0.020455165, 7.512615e-04, 1.426917e-27],
            [   26.55474, 15.3340435,  5.5839169, 2.2226600, 0.81484732, 0.411803306, 0.073730357, 6.831000e-02, 4.944679e-28],
            [ 1230.40713,  6.5997216,  3.5053090, 1.4444545, 0.22892509, 0.138883024, 0.094103733, 1.073511e-02, 1.028564e-27],
            [  843.72636,  8.8851097,  2.6393540, 1.6012808, 0.25854023, 0.138415539, 0.026922948, 8.778627e-03, 2.414561e-28],
            [ 1344.07559,  1.9673193,  1.6042713, 1.5500172, 0.69213848, 0.572760377, 0.230334024, 4.311427e-03, 9.015109e-28],
            [ 2353.09499, 25.9603633,  0.4652749, 0.0648973, 0.04237900, 0.019679806, 0.014583625, 1.138129e-02, 5.900926e-28],
            [   45.06137,  0.9043144,  0.4176386, 0.4156398, 0.06097744, 0.002290573, 0.002027549, 9.528934e-06, 1.631984e-27],
            [   11.49586,  7.3461559,  5.2718575, 1.9358048, 1.92984458, 0.105893399, 0.070583148, 6.909314e-02, 7.019999e-28],
            [  854.26711, 32.0240630,  1.1848384, 0.8142542, 0.69822096, 0.450131099, 0.438646882, 1.072546e-02, 3.693348e-28],
            [ 2852.62121, 10.2349904,  3.4370509, 2.9821799, 2.43370481, 1.139099600, 0.267358988, 2.431691e-02, 9.744412e-28],
            [ 3973.40984, 10.5226926,  4.7977389, 1.6377377, 0.31877040, 0.272104219, 0.272076656, 1.101899e-02, 6.515476e-28],
            [19338.85351,  4.9054303,  2.4063942, 1.1066776, 0.18998976, 0.106599830, 0.105683908, 4.805534e-02, 4.785878e-27],
            [  201.14449, 10.0124247,  1.8649954, 1.5294809, 1.35995863, 0.137564591, 0.060416128, 5.703475e-02, 4.364743e-28],
        ];

        // When
        $qResiduals = self::$pca->getQResiduals()->getMatrix();

        // Then
        $this->assertEquals($expected, $qResiduals, '', .00001);
    }

    /**
     * @test The class returns the correct Q residuals
     *
     * library(mdatools)
     *   new = matrix(c(1:9), 1, 9)
     *   result = predict(model, new)
     *   result$Q
     *
     * @throws \Exception
     */
    public function testQWithNewData()
    {
        // Given
        $expected = [[1972.229, 1158.265, 311.4343, 71.73161, 53.49752, 15.75576, 14.58816, 11.84291, 4.90987e-27]];
        $newData  = MatrixFactory::create([[1,2,3,4,5,6,7,8,9]]);

        // When
        $qResiduals = self::$pca->getQResiduals($newData)->getMatrix();

        // Then
        $this->assertEquals($expected, $qResiduals, '', .001);
    }
}
