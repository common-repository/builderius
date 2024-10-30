<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class MaxFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'max(2, 3, 1, 6, 7)',
                'expectedResult' => 7
            ],
            [
                'expression' => 'max([2, 4, 5])',
                'expectedResult' => 5
            ]
        ];
    }
}