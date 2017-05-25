<?php
namespace MathPHP\Tests\LinearAlgebra;

use MathPHP\Exception;
use MathPHP\LinearAlgebra\MatrixFactory;
use MathPHP\LinearAlgebra\Eigenvalue;

class EigenvalueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testCase     closedFormPolynomialRootMethod returns the expected eigenvalues
     * @dataProvider dataProviderForEigenvalues
     * @param        array $A
     * @param        array $S
     */
    public function testClosedFormPolynomialRootMethod(array $A, array $S)
    {
        $A = MatrixFactory::create($A);
        $this->assertEquals($S, Eigenvalue::closedFormPolynomialRootMethod($A), '', 0.0001);
        $this->assertEquals($S, $A->eigenvalues(Eigenvalue::CLOSED_FORM_POLYNOMIAL_ROOT_METHOD), '', 0.0001);
    }

    public function dataProviderForEigenvalues(): array
    {
        return [
            [
                [
                    [0, 1],
                    [-2, -3],
                ],
                [-2, -1],
            ],
            [
                [
                    [6, -1],
                    [2, 3],
                ],
                [4, 5],
            ],
            [
                [
                    [1, -2],
                    [-2, 0],
                ],
                [(1 - sqrt(17))/2, (1 + sqrt(17))/2],
            ],
            [
                [
                    [-2, -4, 2],
                    [-2, 1, 2],
                    [4, 2, 5],
                ],
                [6, -5, 3],
            ],
            [
                [
                    [2, 0, 0],
                    [1, 2, 1],
                    [-1, 0, 1],
                ],
                [2, 1, 2],
            ],
            [
                [
                    [1, 2, 1],
                    [6, -1, 0],
                    [-1, -2, -1],
                ],
                [3, -4, 0],
            ],
            [
                [
                    [1, 2, 3],
                    [4, 5, 6],
                    [7, 8, 9],
                ],
                [(3*(5 + sqrt(33)))/2, (-3*(sqrt(33) - 5))/2, 0],
            ],
        ];
    }

    /**
     * @testCase     closedFormPolynomialRootMethod throws a BadDataException if the matrix is not the correct size (2x2 or 3x3)
     * @dataProvider dataProviderForEigenvalueException
     * @param        array $A
     */
    public function testClosedFormPolynomialRootMethodExceptionMatrixNotCorrectSize(array $A)
    {
        $A = MatrixFactory::create($A);

        $this->expectException(Exception\BadDataException::class);
        Eigenvalue::closedFormPolynomialRootMethod($A);
    }

    public function dataProviderForEigenvalueException(): array
    {
        return [
            '1x1' => [
                [
                    [1],
                ],
            ],
            '5x5' => [
                [
                    [1, 2, 3, 4, 5],
                    [2, 3, 4, 5, 6],
                    [3, 4, 5, 6, 7],
                    [4, 5, 6, 7, 8],
                    [5, 6, 7, 8, 9],
                ]
            ],
            'not_square' => [
                [
                    [1, 2, 3],
                    [2, 3, 4],
                ],
            ],
        ];
    }

    /**
     * @testCase Matrix eigenvalues throws a MatrixException if the eigenvalue method is not valid
     */
    public function testMatrixEigenvalueInvalidMethodException()
    {
        $A = MatrixFactory::create([
            [1, 2, 3],
            [2, 3, 4],
            [3, 4, 5],
        ]);
        $invalidMethod = 'SecretMethod';

        $this->expectException(Exception\MatrixException::class);
        $A->eigenvalues($invalidMethod);
    }

    /**
     * @testCase     eigenvector using closedFormPolynomialRootMethod returns the expected eigenvalues
     * @dataProvider dataProviderForEigenvector
     * @param        array $A
     * @param        array $S
     */
    public function testEigenvectorsUsingClosedFormPolynomialRootMethod(array $A, array $S)
    {
        $A = MatrixFactory::create($A);
        $this->assertEquals($S, Eigenvalue::eigenvectors($A)->getMatrix(), '', 0.0001);
    }

    public function dataProviderForEigenvector(): array
    {
        return [
            [
                [
                    [0, 1],
                    [-2, -3],
                ],
                [
                    [1/sqrt(5), \M_SQRT1_2],
                    [-2/sqrt(5), -\M_SQRT1_2],
                ]
            ],
            [
                [
                    [6, -1],
                    [2, 3],
                ],
                [
                    [1/sqrt(5), \M_SQRT1_2],
                    [2/sqrt(5), \M_SQRT1_2],
                ]
            ],
            [
                [
                    [-2, -4, 2],
                    [-2, 1, 2],
                    [4, 2, 5],
                ],
                [
                    [1/sqrt(293), 2/sqrt(6), 2/sqrt(14)],
                    [6/sqrt(293), 1/sqrt(6), -3/sqrt(14)],
                    [16/sqrt(293), -1/sqrt(6), -1/sqrt(14)],
                ]
            ],
            [ // RREF is a zero matrix
                [
                    [1, 0, 0],
                    [0, 1, 0],
                    [0, 0, 1],
                ],
                [
                    [1, 0, 0],
                    [0, 1, 0],
                    [0, 0, 1],
                ]
            ],
            [ // Matrix has duplicate eigenvalues. One vector is on an axis.
                [
                    [2, 0, 1],
                    [2, 1, 2],
                    [3, 0, 4],
                ],
                [
                    [0, 1/sqrt(14), \M_SQRT1_2],
                    [1, 2/sqrt(14), 0],
                    [0, 3/sqrt(14), -1 * \M_SQRT1_2],
                ]
            ],
            [ // Matrix has duplicate eigenvalues. no solution on the axis
                [
                    [2, 2, -3],
                    [2, 5, -6],
                    [3, 6, -8],
                ],
                [
                    [1/\M_SQRT3, 1/sqrt(14), 5/sqrt(42)],
                    [1/\M_SQRT3, 2/sqrt(14), -4/sqrt(42)],
                    [1/\M_SQRT3, 3/sqrt(14), -1/sqrt(42)],
                ]
            ],
            [ // The top row of the rref has a solitary 1 in position 0,0
                [
                    [4, 1, 2],
                    [0, 0, -2],
                    [2, 2, 5],
                ],
                [
                    [ 5/sqrt(65), 0, 1/3],
                    [-2/sqrt(65), -2/sqrt(5), 2/3],
                    [6/sqrt(65), 1/sqrt(5), -2/3],
                ]
            ],
        ];
    }

    /**
     * @testCase eigenvectors throws a BadDataException when the matrix is not square
     */
    public function testEigenvalueMatrixNotCorrectSize()
    {
        $A = MatrixFactory::create([[1,2]]);

        $this->expectException(Exception\BadDataException::class);
        Eigenvalue::eigenvectors($A, [0]);
    }

    /**
     * @testCase     eigenvectors throws a BadDataException when the array of eigenvales is too long or short
     * @dataProvider dataProviderForIncorrectNumberOfEigenvalues
     * @param        array $A
     * @param        array $B
     */
    public function testIncorrectNumberOfEigenvalues(array $A, array $B)
    {
        $A = MatrixFactory::create($A);

        $this->expectException(Exception\BadDataException::class);
        Eigenvalue::eigenvectors($A, $B);
    }

    public function dataProviderForIncorrectNumberOfEigenvalues(): array
    {
        return [
            [
                [
                    [0, 1],
                    [-2, -3],
                ],
                [1,2,3],
            ],
        ];
    }

    /**
     * @testCase     eigenvectors throws a BadDataException when an eigenvalue that is provided is not a number
     * @dataProvider dataProviderForEigenvalueNotNumeric
     * @param        array $A
     * @param        array $V
     */
    public function testEigenvalueNotNumeric(array $A, array $B)
    {
        $A = MatrixFactory::create($A);

        $this->expectException(Exception\BadDataException::class);
        Eigenvalue::eigenvectors($A, $B);
    }

    public function dataProviderForEigenvalueNotNumeric(): array
    {
        return [
            [
                [
                    [0, 1],
                    [-2, -3],
                ],
                ["test"],
            ],
        ];
    }

    /**
     * @testCase     eigenvectors throws a BadDataException when there is an incorrect eigenvalue provided
     * @dataProvider dataProviderForEigenvalueNotAnEigenvalue
     * @param        array $A
     * @param        array $B
     */
    public function testEigenvalueNotAnEigenvalue(array $A, array $B)
    {
        $A = MatrixFactory::create($A);

        $this->expectException(Exception\BadDataException::class);
        Eigenvalue::eigenvectors($A, $B);
    }

    public function dataProviderForEigenvalueNotAnEigenvalue(): array
    {
        return [
            [
                [
                    [0, 1],
                    [-2, -3],
                ],
                [-2, 0],
            ],
            [
                [
                    [0, 1],
                    [-2, -3],
                ],
                [0, -3],
            ],
        ];
    }
}
