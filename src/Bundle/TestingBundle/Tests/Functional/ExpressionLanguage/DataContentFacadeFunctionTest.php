<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class DataContentFacadeFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                'expression' => 'createTempVariable(
                    GET("flt"),
                    filter(
                       s.i1,
                       (value, k) -> {
                           value.a1 === "data-content" && value.b1 !== null
                       }
                    )
                 ) == true ?
                  (
                    count(tempVariable("flt")) === 1 ?
                     {"a1": tempVariable("flt")[0].b1} :
                     {"a1": null}
                  ) :
                  {"a1": null}',
                'expectedResult' => [
                    'a1' => '[[[data]]]'
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-content',
                                'b1' => "[[[data]]]"
                            ],
                            (object)[
                                'a1' => 'data-source',
                                'b1' => "[[[data-source]]]"
                            ],
                        ]
                    ]
                ]
            ],
            [
                'expression' =>
                   'createTempVariable(
                     "flt",
                     findIndex(
                       s.i1,
                       (value, k) -> {
                           value.a1 === "data-content"
                       }
                     )
                   ) == true ?
                   (
                     f.a1 !== null ? (
                       tempVariable("flt") !== -1 ?
                       set(s, "i1[" ~ tempVariable("flt") ~ "].b1", f.a1) :
                       {"i1": push(s.i1, {"a1": "data-content", "b1": f.a1})}
                     ) :
                     (
                       tempVariable("flt") !== -1 ?
                       unset(s, "i1[" ~ tempVariable("flt") ~ "]") :
                       s
                     )
                   ) :
                   s',
                'expectedResult' => (object)[
                    'i1' => [
                        (object)[
                            'a1' => 'data-content',
                            'b1' => "[[[data-facade]]]"
                        ],
                        (object)[
                            'a1' => 'data-source',
                            'b1' => "[[[data-source]]]"
                        ]
                    ]
                ],
                'context' => [
                    'f' => (object)[
                        'a1' => null//'[[[data-facade]]]'
                    ],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-content',
                                'b1' => "[[[data]]]"
                            ],
                            (object)[
                                'a1' => 'data-source',
                                'b1' => "[[[data-source]]]"
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }
}