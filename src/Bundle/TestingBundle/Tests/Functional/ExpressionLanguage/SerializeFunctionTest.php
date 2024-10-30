<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class SerializeFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'serialize(arr)',
                'expectedResult' => '[{"name":"apple","cat":"fruit","price":1.5},{"name":"carrot","cat":"vegetable","price":0.75},{"name":"cucumber","cat":"vegetable","price":1.3}]',
                'context' => [
                    'arr' => [
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
                    ]
                ]
            ],
            [
                'expression' => 'serialize(arr)',
                'expectedResult' => 1212312121323,
                'context' => [
                    'arr' => 1212312121323
                ]
            ],
        ];
    }
}