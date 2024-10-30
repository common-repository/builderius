<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class JoinKeysFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'joinKeys({ "apple": "$3.11", "orange": "$2.89", "melon": "$0.99" }, ", ")',
                'expectedResult' => 'apple, orange, melon'
            ]
        ];
    }
}