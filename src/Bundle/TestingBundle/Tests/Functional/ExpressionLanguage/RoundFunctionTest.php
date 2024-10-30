<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class RoundFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'round(4.7541, 0)',
                'expectedResult' => 5
            ],
            [
                'expression' => 'round(4.7541, 1)',
                'expectedResult' => 4.8
            ],
            [
                'expression' => 'round(4.7541, 2)',
                'expectedResult' => 4.75
            ]
        ];
    }
}