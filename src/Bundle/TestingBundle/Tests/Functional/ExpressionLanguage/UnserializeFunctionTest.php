<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class UnserializeFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'unserialize(str_arr)',
                'expectedResult' => [
                    [
                        "name" => "apple",
                        "cat" => "fruit",
                        "price" => 1.5
                    ],
                    [
                        "name" => "carrot",
                        "cat" => "vegetable",
                        "price" => 0.75
                    ],
                    [
                        "name" => "cucumber",
                        "cat" => "vegetable",
                        "price" => 1.3
                    ]
                ],
                'context' => [
                    'str_arr' => '[{"name":"apple","cat":"fruit","price":1.5},{"name":"carrot","cat":"vegetable","price":0.75},{"name":"cucumber","cat":"vegetable","price":1.3}]'
                ]
            ],
            [
                'expression' => 'unserialize(str)',
                'expectedResult' => 1212312121323,
                'context' => [
                    'str' => 1212312121323
                ]
            ],
        ];
    }
}