<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class SmartformDisableSubmitRulesFacadeFunctionTest extends AbstractElFunctionTest
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        $settingToFacadeExpression = 'isset(s, "i1") && createTempVariable(
            "flt",
            filter(
              s.i1,
              (value, k) -> {
                value.a1 === "data-disable-submit-rules" && value.b1 !== null
              }
            )
          ) == true ?
          (
            count(tempVariable("flt")) === 1 ?
              foreach(unserialize(tempVariable("flt")[0].b1), (v) -> { {"a1": v} }, false) :
            []
          ) :
          []';

        $facadeToSettingExpression = '
                  count(f) > 0 ?
                  (
                    createTempVariable(
                      "finalJson",
                      foreach(
                        f,
                        (v) -> {
                          (
                            isset(v, "a1") && !(v.a1 in [null, ""])
                          ) ?
                          v.a1 : null
                        }
                      )
                    ) ?    
                    (
                      isset(s, "i1") == true ?
                      (
                        createTempVariable(
                          "flt",
                          findIndex(
                            s.i1,
                            (value, k) -> {
                              value.a1 === "data-disable-submit-rules"
                            }
                          )
                        ) == true ?
                        (
                          tempVariable("flt") !== -1 ?
                          (
                            set(s, "i1[" ~ tempVariable("flt") ~ "].b1", serialize(tempVariable("finalJson")))
                          ) :
                          (
                            {"i1": push(s.i1, {"a1": "data-disable-submit-rules", "b1": serialize(tempVariable("finalJson"))})}
                          )
                        ) : s
                      ) :
                      (
                        {"i1": [{"a1": "data-disable-submit-rules", "b1": serialize(tempVariable("finalJson"))}]}
                      )
                    ) : s
                  ) :
                  (
                    isset(s, "i1") == true ?
                    (
                      createTempVariable(
                        "flt",
                        findIndex(
                          s.i1,
                          (value, k) -> {
                            value.a1 === "data-disable-submit-rules"
                          }
                        )
                      ) == true && tempVariable("flt") !== -1 ?
                      (
                        unset(s, "i1[" ~ tempVariable("flt") ~ "]")
                      ) : s
                    ) : s
                  )';

        return [
            'setting_to_facade_all_filled' => [
                'expression' => $settingToFacadeExpression,
                'expectedResult' => [
                    [
                        'a1' => 'width < 10'
                    ],
                    [
                        'a1' => 'area == null'
                    ]
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-disable-submit-rules',
                                'b1' => '["width < 10", "area == null"]'
                            ]
                        ]
                    ]
                ]
            ],
            'setting_to_facade_empty1' => [
                'expression' => $settingToFacadeExpression,
                'expectedResult' => [],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-disable-submit-rules',
                                'b1' => ''
                            ]
                        ]
                    ]
                ]
            ],
            'setting_to_facade_empty2' => [
                'expression' => $settingToFacadeExpression,
                'expectedResult' => [],
                'context' => [
                    's' => (object)[]
                ]
            ],
            'facade_to_setting_all_filled' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => (object)[
                    'i1' => [
                        (object)[
                            'a1' => 'data-disable-submit-rules',
                            'b1' => '["width < 10","area == null"]'
                        ]
                    ]
                ],
                'context' => [
                    'f' => [
                        (object)[
                            'a1' => 'width < 10'
                        ],
                        (object)[
                            'a1' => 'area == null'
                        ]
                    ],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-disable-submit-rules',
                                'b1' => '["width < 11","area == 123"]'
                            ]
                        ]
                    ]
                ],
            ],
            'facade_to_setting_empty_setting' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => [
                    'i1' => [
                        [
                            'a1' => 'data-disable-submit-rules',
                            'b1' => '["width < 10","area == null"]'
                        ]
                    ]
                ],
                'context' => [
                    'f' => [
                        (object)[
                            'a1' => 'width < 10'
                        ],
                        (object)[
                            'a1' => 'area == null'
                        ]
                    ],
                    's' => (object)[]
                ],
            ],
            'facade_to_setting_empty_facade1' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => (object)[
                    'i1' => [
                        1 => (object)[
                            'a1' => 'data-test',
                            'b1' => 'my custom test'
                        ]
                    ]
                ],
                'context' => [
                    'f' => [],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-disable-submit-rules',
                                'b1' => '["width < 10","area == null"]'
                            ],
                            (object)[
                                'a1' => 'data-test',
                                'b1' => 'my custom test'
                            ]
                        ]
                    ]
                ],
            ],
            'facade_to_setting_empty_facade2' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => (object)[
                    'i1' => []
                ],
                'context' => [
                    'f' => (object)[
                        'a1' => null
                    ],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-disable-submit-rules',
                                'b1' => '["width < 10","area == null"]'
                            ]
                        ]
                    ]
                ],
            ],
        ];
    }
}