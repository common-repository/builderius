<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class SmartformFormatSubmitDataFacadeFunctionTest extends AbstractElFunctionTest
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
                value.a1 === "data-format-submit-data" && value.b1 !== null
              }
            )
          ) == true ?
          (
            count(tempVariable("flt")) === 1 ?
              foreach(unserialize(tempVariable("flt")[0].b1), (value, key) -> { {"a1":key,"b1":value} }, false) :
            []
          ) :
          []';

        $facadeToSettingExpression = 'count(f) > 0 ?
          (
            count(foreach(
              f,
              (v) -> {
                (isset(v, "a1") == true && isset(v, "b1") == true) ?
                  createTempVariable(
                    "finalJson",
                    merge((tempVariable("finalJson") !== false ? tempVariable("finalJson") : {}), {(v.a1) : v.b1}
                  )
                ) : false
              },
              false
            )) > 0 ?
            (
              isset(s, "i1") == true ?
              (
                createTempVariable(
                  "flt",
                  findIndex(
                    s.i1,
                    (value, k) -> {
                      value.a1 === "data-format-submit-data"
                    }
                  )
                ) == true ?
                (
                  tempVariable("flt") !== -1 ?
                  (
                    set(s, "i1[" ~ tempVariable("flt") ~ "].b1", serialize(tempVariable("finalJson")))
                  ) :
                  (
                    {"i1": push(s.i1, {"a1": "data-format-submit-data", "b1": serialize(tempVariable("finalJson"))})}
                  )
                ) : s
              ) :
              (
                tempVariable("finalJson") != false ? {"i1": [{"a1": "data-format-submit-data", "b1": serialize(tempVariable("finalJson"))}]} : s
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
                    value.a1 === "data-format-submit-data"
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
                        'a1' => 'width',
                        'b1' => "material == 'paper' ? widthP : widthC"
                    ],
                    [
                        'a1' => 'area',
                        'b1' => 'area'
                    ],
                    [
                        'a1' => 'input',
                        'b1' => 'input'
                    ]
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-format-submit-data',
                                'b1' => '{"width":"material == \'paper\' ? widthP : widthC", "area":"area", "input":"input"}'
                            ]
                        ]
                    ]
                ]
            ],
            'setting_to_facade_no_value' => [
                'expression' => $settingToFacadeExpression,
                'expectedResult' => [
                    [
                        'a1' => 'width',
                        'b1' => "material == 'paper' ? widthP : widthC"
                    ],
                    [
                        'a1' => 'area',
                        'b1' => 'area'
                    ],
                    [
                        'a1' => 'input',
                        'b1' => null
                    ]
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-format-submit-data',
                                'b1' => '{"width":"material == \'paper\' ? widthP : widthC", "area":"area", "input":null}'
                            ]
                        ]
                    ]
                ]
            ],
            'setting_to_facade_empty' => [
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
                            'a1' => 'data-format-submit-data',
                            'b1' => '{"width":"material == \'paper\' ? widthP : widthC","area":"area","input":"input"}'
                        ]
                    ],
                ],
                'context' => [
                    'f' => [
                        (object)[
                            'a1' => 'width',
                            'b1' => "material == 'paper' ? widthP : widthC"
                        ],
                        (object)[
                            'a1' => 'area',
                            'b1' => 'area'
                        ],
                        (object)[
                            'a1' => 'input',
                            'b1' => 'input'
                        ]
                    ],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-format-submit-data',
                                'b1' => '{"width":"material == \'paper\' ? widthP : widthC", "area":"area"}'
                            ]
                        ]
                    ]
                ]
            ],
            'facade_to_setting_no_value' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => (object)[
                    'i1' => [
                        (object)[
                            'a1' => 'data-format-submit-data',
                            'b1' => '{"width":"material == \'paper\' ? widthP : widthC","area":"area","input":"input"}'
                        ]
                    ],
                ],
                'context' => [
                    'f' => [
                        (object)[
                            'a1' => 'width',
                            'b1' => "material == 'paper' ? widthP : widthC"
                        ],
                        (object)[
                            'a1' => 'area',
                            'b1' => 'area'
                        ],
                        (object)[
                            'a1' => 'input',
                            'b1' => null
                        ]
                    ],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-format-submit-data',
                                'b1' => '{"width":"material == \'paper\' ? widthP : widthC", "area":"area"}'
                            ]
                        ]
                    ]
                ]
            ],
            'facade_to_setting_empty' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => [
                    'i1' => [
                        [
                            'a1' => 'data-format-submit-data',
                            'b1' => '{"width":"material == \'paper\' ? widthP : widthC","area":"area","input":"input"}'
                        ]
                    ],
                ],
                'context' => [
                    'f' => [
                        (object)[
                            'a1' => 'width',
                            'b1' => "material == 'paper' ? widthP : widthC"
                        ],
                        (object)[
                            'a1' => 'area',
                            'b1' => 'area'
                        ],
                        (object)[
                            'a1' => 'input',
                            'b1' => 'input'
                        ]
                    ],
                    's' => (object)[]
                ]
            ],
        ];
    }
}