<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class JoinFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => "join(['apple', 'orange', 'melon'], ', ')",
                'expectedResult' => 'apple, orange, melon'
            ],
            [
                'expression' => "join(['name', 'value'], ':')",
                'expectedResult' => 'name:value'
            ]
        ];
    }
}