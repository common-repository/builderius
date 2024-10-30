<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class UnsetFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'unset({"apple": "$3.11", "orange": "$2.89", "melon": "$0.99"}, ["orange", "melon"])',
                'expectedResult' => ["apple" => "$3.11"]
            ]
        ];
    }
}