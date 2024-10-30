<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class TestFunctionTest extends AbstractElFunctionTest
{

    /**
     * @dataProvider dataProvider
     * @param string $expression
     * @param mixed $expectedResult
     */
    public function testFunction($expression, $expectedResult, array $context = [])
    {
        $act = $this->el->evaluate($expression, $context);
        static::assertEquals($expectedResult, $this->el->evaluate($expression, $context));
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $data = [
            [
                'expression' => "unserialize('{\"query\": {\"queried_post\": {\"' ~ value.key ~ '\": {\"__aliasFor\": \"meta_value\", \"__args\": {\"key\": \"' ~ value.key ~ '\"}}}}}')",
                'expectedResult' => (object)
                [
                    'query' => (object) [
                        'queried_post' => (object) ['acf_val' => (object) ["__args" => (object) ["key" => "acf_val"], '__aliasFor' => 'meta_value']]
                    ],
                ],
                'context' => [
                    'subFunc' => '',
                    'value' => [
                        'key' => "acf_val"
                    ]
                ]
            ]
        ];

        return $data;
    }
}