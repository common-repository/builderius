<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class ArrowFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'foreach(
                   values,
                   (value, k) -> {
                       (k != "first" ? 
                         map(
                           (subVal, i) -> {
                             subVal * 2
                           },
                           value
                         )
                        :
                       merge(value, {3: 24}))
                   }
                 )',
                'expectedResult' => [
                    'first' => [1,2,3],
                    'second' => [4,5,6]
                ],
                'context' => [
                    'values' => [
                        'first' => [1,2,3],
                        'second' => [4,5,6]
                    ]
                ]
            ]
        ];
    }
}