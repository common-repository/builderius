<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class ForeachFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => "foreach(arrItem, 'food', 'merge(food, {\"price\":\"$1.99\"})')",
                'expectedResult' => [
                    ["name" => "apple", "cat" => "fruit", "price" => "$1.99"],
                    ["name" => "carrot", "cat" => "vegetable", "price" => "$1.99"]
                ],
                'context' => [
                    "arrItem" => [
                        ["name" => "apple", "cat" => "fruit"],
                        ["name" => "carrot", "cat" => "vegetable"]
                    ]
                ]
            ]
        ];
    }
}