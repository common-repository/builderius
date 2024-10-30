<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class Arrow2FunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'usort(array, (c, b) -> {b.id - c.id})',
                'expectedResult' => [
                    (object)['id' => 4],
                    (object)['id' => 3],
                    (object)['id' => 2],
                ],
                'context' => [
                    'array' => [
                        (object)['id' => 3],
                        (object)['id' => 2],
                        (object)['id' => 4],
                    ]
                ]
            ]
        ];
    }
}