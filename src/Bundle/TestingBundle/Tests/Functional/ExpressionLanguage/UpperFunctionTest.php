<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class UpperFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'upper("Some Title")',
                'expectedResult' => 'SOME TITLE'
            ]
        ];
    }
}