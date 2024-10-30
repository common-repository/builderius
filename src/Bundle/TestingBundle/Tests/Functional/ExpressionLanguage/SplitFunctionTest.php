<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class SplitFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'split("apple, orange, melon", ", ")',
                'expectedResult' => ['apple', 'orange', 'melon']
            ],
            [
                'expression' => 'split("name:value", ":")',
                'expectedResult' => ['name', 'value']
            ]
        ];
    }
}