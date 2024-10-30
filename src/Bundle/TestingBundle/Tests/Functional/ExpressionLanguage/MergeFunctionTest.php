<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class MergeFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'merge({"dog": "waf"}, {"cat": "purrr"})',
                'expectedResult' => ['dog' => 'waf', 'cat' => 'purrr']
            ],
            [
                'expression' => 'merge({"dog": "waf"}, {"dog": "bow", "cat": "purrr"})',
                'expectedResult' => ['dog' => 'bow', 'cat' => 'purrr']
            ]
        ];
    }
}