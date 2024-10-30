<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class IsEmailFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => "isEmail('example@example.com')",
                'expectedResult' => true
            ],
            [
                'expression' => "isEmail('example.com')",
                'expectedResult' => false
            ]
        ];
    }
}