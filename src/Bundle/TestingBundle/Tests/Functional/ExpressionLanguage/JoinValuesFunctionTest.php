<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class JoinValuesFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'joinValues({ "apple": "$3.11", "orange": "$2.89", "melon": "$0.99" }, ", ")',
                'expectedResult' => '$3.11, $2.89, $0.99'
            ]
        ];
    }
}