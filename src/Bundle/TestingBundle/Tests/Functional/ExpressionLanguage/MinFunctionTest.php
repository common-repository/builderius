<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class MinFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'min(2, 3, 1, 6, 7)',
                'expectedResult' => 1
            ],
            [
                'expression' => 'min([2, 4, 5])',
                'expectedResult' => 2
            ]
        ];
    }
}