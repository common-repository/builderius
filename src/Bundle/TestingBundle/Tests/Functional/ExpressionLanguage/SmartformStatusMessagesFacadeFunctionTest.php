<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class SmartformStatusMessagesFacadeFunctionTest extends AbstractElFunctionTest
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
                value.a1 === "data-status-messages" && value.b1 !== null
              }
            )
          ) == true ?
          (
            count(tempVariable("flt")) === 1 ?
              {"a1": (isset(unserialize(tempVariable("flt")[0].b1), "success") != false ? unserialize(tempVariable("flt")[0].b1).success : ""), "b1": (isset(unserialize(tempVariable("flt")[0].b1), "error") != false ? unserialize(tempVariable("flt")[0].b1).error : "")} :
            {"a1": "", "b1": ""}
          ) :
          {"a1": "", "b1": ""}';

        $facadeToSettingExpression = '
                  (!(f.a1 in [null, ""]) || !(f.b1 in [null, ""])) ?
                  (
                    createTempVariable(
                      "finalJson",
                      (!(f.a1 in [null, ""]) && !(f.b1 in [null, ""])) ? serialize({"success": f.a1, "error": f.b1}) : (!(f.a1 in [null, ""]) ? serialize({"success": f.a1}) : serialize({"error": f.b1}))
                    ) == true ?
                    (
                      isset(s, "i1") == true ?
                      (
                        createTempVariable(
                          "flt",
                          findIndex(
                            s.i1,
                            (value, k) -> {
                              value.a1 === "data-status-messages"
                            }
                          )
                        ) == true ?
                        (
                          tempVariable("flt") !== -1 ?
                          (
                            set(s, "i1[" ~ tempVariable("flt") ~ "].b1", tempVariable("finalJson"))
                          ) :
                          (
                            {"i1": push(s.i1, {"a1": "data-status-messages", "b1": tempVariable("finalJson")})}
                          )
                        ) : s
                      ) :
                      (
                        {"i1": [{"a1": "data-status-messages", "b1": tempVariable("finalJson")}]}
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
                            value.a1 === "data-status-messages"
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
                    'a1' => 'success message',
                    'b1' => 'error message'
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-status-messages',
                                'b1' => '{"success":"success message","error":"error message"}'
                            ]
                        ]
                    ]
                ]
            ],
            'setting_to_facade_no_error_message' => [
                'expression' => $settingToFacadeExpression,
                'expectedResult' => [
                    'a1' => 'success message',
                    'b1' => ''
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-status-messages',
                                'b1' => '{"success":"success message"}'
                            ]
                        ]
                    ]
                ]
            ],
            'setting_to_facade_no_success_message' => [
                'expression' => $settingToFacadeExpression,
                'expectedResult' => [
                    'a1' => '',
                    'b1' => 'error message',
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-status-messages',
                                'b1' => '{"error":"error message"}'
                            ]
                        ]
                    ]
                ]
            ],
            'setting_to_facade_empty1' => [
                'expression' => $settingToFacadeExpression,
                'expectedResult' => [
                    'a1' => '',
                    'b1' => '',
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-status-messages',
                                'b1' => ''
                            ]
                        ]
                    ]
                ]
            ],
            'setting_to_facade_empty2' => [
                'expression' => $settingToFacadeExpression,
                'expectedResult' => [
                    'a1' => '',
                    'b1' => '',
                ],
                'context' => [
                    's' => (object)[]
                ]
            ],
            'facade_to_setting_all_filled' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => (object)[
                    'i1' => [
                        (object)[
                            'a1' => 'data-status-messages',
                            'b1' => '{"success":"facade success message","error":"facade error message"}'
                        ]
                    ]
                ],
                'context' => [
                    'f' => (object)[
                        'a1' => 'facade success message',
                        'b1' => 'facade error message'
                    ],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-status-messages',
                                'b1' => '{"success":"success message","error":"error message"}'
                            ]
                        ]
                    ]
                ],
            ],
            'facade_to_setting_no_error_message_in_facade' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => (object)[
                    'i1' => [
                        (object)[
                            'a1' => 'data-status-messages',
                            'b1' => '{"success":"facade success message"}'
                        ]
                    ]
                ],
                'context' => [
                    'f' => (object)[
                        'a1' => 'facade success message',
                        'b1' => null
                    ],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-status-messages',
                                'b1' => '{"success":"success message","error":"error message"}'
                            ]
                        ]
                    ]
                ],
            ],
            'facade_to_setting_no_success_message_in_facade' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => (object)[
                    'i1' => [
                        (object)[
                            'a1' => 'data-status-messages',
                            'b1' => '{"error":"facade error message"}'
                        ]
                    ]
                ],
                'context' => [
                    'f' => (object)[
                        'a1' => null,
                        'b1' => 'facade error message'
                    ],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-status-messages',
                                'b1' => '{"success":"success message","error":"error message"}'
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
                            'a1' => 'data-status-messages',
                            'b1' => '{"error":"facade error message"}'
                        ]
                    ]
                ],
                'context' => [
                    'f' => (object)[
                        'a1' => null,
                        'b1' => 'facade error message'
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
                    'f' => (object)[
                        'a1' => null,
                        'b1' => null
                    ],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-status-messages',
                                'b1' => '{"error":"facade error message"}'
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
                        'a1' => null,
                        'b1' => null
                    ],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-status-messages',
                                'b1' => '{"error":"facade error message"}'
                            ]
                        ]
                    ]
                ],
            ],
        ];
    }
}