<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class SmartformValidationSchemeFacadeFunctionTest extends AbstractElFunctionTest
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
                value.a1 === "data-validation-scheme" && value.b1 !== null
              }
            )
          ) == true ?
          (
            count(tempVariable("flt")) === 1 ?
              foreach(unserialize(tempVariable("flt")[0].b1), (v,k) -> { {"a1": k, "b1": (isset(v, "type") != false ? v.type : ""), "c1": (isset(v, "validators") != false ? foreach(v.validators, (val) -> { foreach(val, (vv,kk) -> { {"a1": kk,"b1": vv} }, false)[0] }) : [])} }, false) :
            []
          ) :
          []';

        $facadeToSettingExpression = '
                  count(f) > 0 ?
                  (
                    foreach(
                      f,
                      (v) -> {
                        (
                          isset(v, "a1") && isset(v, "b1") && isset(v, "c1") && !(v.a1 in [null, ""]) && !(v.b1 in [null, ""]) && !(v.c1 in [null, ""])
                        ) ?
                        createTempVariable("finalJson", merge((tempVariable("finalJson") !== false ? tempVariable("finalJson") : {}),{(v.a1):{"type": v.b1, "validators": foreach(filter(v.c1, (vvv) -> {isset(vvv, "a1") && isset(vvv, "b1")}), (vv) -> { {(vv.a1):vv.b1} })}})) :
                        (
                          (
                            isset(v, "a1") && isset(v, "b1") && !(v.a1 in [null, ""]) && !(v.b1 in [null, ""])
                          ) ?
                          createTempVariable("finalJson", merge((tempVariable("finalJson") !== false ? tempVariable("finalJson") : {}),{(v.a1):{"type": v.b1, "validators": []}})) : {}
                        )
                      }
                    ) ?    
                    (
                      isset(s, "i1") == true ?
                      (
                        createTempVariable(
                          "flt",
                          findIndex(
                            s.i1,
                            (value, k) -> {
                              value.a1 === "data-validation-scheme"
                            }
                          )
                        ) == true ?
                        (
                          tempVariable("flt") !== -1 ?
                          (
                            set(s, "i1[" ~ tempVariable("flt") ~ "].b1", serialize(tempVariable("finalJson")))
                          ) :
                          (
                            {"i1": push(s.i1, {"a1": "data-validation-scheme", "b1": serialize(tempVariable("finalJson"))})}
                          )
                        ) : s
                      ) :
                      (
                        {"i1": [{"a1": "data-validation-scheme", "b1": serialize(tempVariable("finalJson"))}]}
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
                            value.a1 === "data-validation-scheme"
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
                        'a1' => 'date',
                        'b1' => 'string',
                        'c1' => [
                            [
                               'a1' => 'date != null',
                               'b1' => 'Required!'
                            ],
                            [
                                'a1' => 'date > 25',
                                'b1' => 'Should be higher'
                            ]
                        ]
                    ],
                    [
                        'a1' => 'age',
                        'b1' => 'number',
                        'c1' => [
                            [
                                'a1' => 'age != null',
                                'b1' => 'Required!'
                            ],
                            [
                                'a1' => 'age > 25',
                                'b1' => 'Should be higher'
                            ]
                        ]
                    ]
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-validation-scheme',
                                'b1' => '{"date":{"type":"string","validators":[{"date != null":"Required!"},{"date > 25":"Should be higher"}]},"age":{"type":"number","validators":[{"age != null":"Required!"},{"age > 25":"Should be higher"}]}}'
                            ]
                        ]
                    ]
                ]
            ],
            'setting_to_facade_empty_validators' => [
                'expression' => $settingToFacadeExpression,
                'expectedResult' => [
                    [
                        'a1' => 'date',
                        'b1' => 'string',
                        'c1' => []
                    ],
                    [
                        'a1' => 'age',
                        'b1' => 'number',
                        'c1' => [
                            [
                                'a1' => 'age != null',
                                'b1' => 'Required!'
                            ],
                            [
                                'a1' => 'age > 25',
                                'b1' => 'Should be higher'
                            ]
                        ]
                    ]
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-validation-scheme',
                                'b1' => '{"date":{"type":"string","validators":[]},"age":{"type":"number","validators":[{"age != null":"Required!"},{"age > 25":"Should be higher"}]}}'
                            ]
                        ]
                    ]
                ]
            ],
            'setting_to_facade_no_validators' => [
                'expression' => $settingToFacadeExpression,
                'expectedResult' => [
                    [
                        'a1' => 'date',
                        'b1' => 'string',
                        'c1' => []
                    ],
                    [
                        'a1' => 'age',
                        'b1' => 'number',
                        'c1' => []
                    ]
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-validation-scheme',
                                'b1' => '{"date":{"type":"string","validators":[]},"age":{"type":"number"}}'
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
                                'a1' => 'data-validation-scheme',
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
                'expectedResult' => [
                    'i1' => [
                        (object)[
                            'a1' => 'data-validation-scheme',
                            'b1' => '{"date":{"type":"string","validators":[{"date != null":"Required!"},{"date > 25":"Should be higher"}]},"age":{"type":"number","validators":[{"age != null":"Required!"},{"age > 25":"Should be higher"}]}}'
                        ]
                    ]
                ],
                'context' => [
                    'f' => [
                        (object)[
                            'a1' => 'date',
                            'b1' => 'string',
                            'c1' => [
                                (object)[
                                    'a1' => 'date != null',
                                    'b1' => 'Required!'
                                ],
                                (object)[
                                    'a1' => 'date > 25',
                                    'b1' => 'Should be higher'
                                ]
                            ]
                        ],
                        (object)[
                            'a1' => 'age',
                            'b1' => 'number',
                            'c1' => [
                                (object)[
                                    'a1' => 'age != null',
                                    'b1' => 'Required!'
                                ],
                                (object)[
                                    'a1' => 'age > 25',
                                    'b1' => 'Should be higher'
                                ]
                            ]
                        ]
                    ],
                    's' => (object)[
                        'i1' => [

                        ]
                    ]
                ],
            ],
            'facade_to_setting_empty_validators' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => [
                    'i1' => [
                        (object)[
                            'a1' => 'data-validation-scheme',
                            'b1' => '{"date":{"type":"string","validators":[]},"age":{"type":"number","validators":[{"age != null":"Required!"},{"age > 25":"Should be higher"}]}}'
                        ]
                    ]
                ],
                'context' => [
                    'f' => [
                        (object)[
                            'a1' => 'date',
                            'b1' => 'string',
                            'c1' => []
                        ],
                        (object)[
                            'a1' => 'age',
                            'b1' => 'number',
                            'c1' => [
                                (object)[
                                    'a1' => 'age != null',
                                    'b1' => 'Required!'
                                ],
                                (object)[
                                    'a1' => 'age > 25',
                                    'b1' => 'Should be higher'
                                ]
                            ]
                        ]
                    ],
                    's' => (object)[
                        'i1' => [

                        ]
                    ]
                ],
            ],
            'facade_to_setting_partial_validators' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => [
                    'i1' => [
                        (object)[
                            'a1' => 'data-validation-scheme',
                            'b1' => '{"date":{"type":"string","validators":[]},"age":{"type":"number","validators":[{"age != null":"Required!"},{"age > 25":"Should be higher"}]}}'
                        ]
                    ]
                ],
                'context' => [
                    'f' => [
                        (object)[
                            'a1' => 'date',
                            'b1' => 'string',
                            'c1' => []
                        ],
                        (object)[
                            'a1' => 'age',
                            'b1' => 'number',
                            'c1' => [
                                (object)[
                                    'b1' => 'Required!'
                                ],
                                (object)[
                                    'a1' => 'age > 25',
                                    'b1' => 'Should be higher'
                                ]
                            ]
                        ]
                    ],
                    's' => (object)[
                        'i1' => [

                        ]
                    ]
                ],
            ],
            'facade_to_setting_no_validators' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => [
                    'i1' => [
                        (object)[
                            'a1' => 'data-validation-scheme',
                            'b1' => '{"date":{"type":"string","validators":[]},"age":{"type":"number","validators":[{"age != null":"Required!"},{"age > 25":"Should be higher"}]}}'
                        ]
                    ]
                ],
                'context' => [
                    'f' => [
                        (object)[
                            'a1' => 'date',
                            'b1' => 'string'
                        ],
                        (object)[
                            'a1' => 'age',
                            'b1' => 'number',
                            'c1' => [
                                (object)[
                                    'a1' => 'age != null',
                                    'b1' => 'Required!'
                                ],
                                (object)[
                                    'a1' => 'age > 25',
                                    'b1' => 'Should be higher'
                                ]
                            ]
                        ]
                    ],
                    's' => (object)[
                        'i1' => [

                        ]
                    ]
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
                                'a1' => 'data-validation-scheme',
                                'b1' => '{"date":{"type":"string","validators":[]},"age":{"type":"number","validators":[{"age != null":"Required!"},{"age > 25":"Should be higher"}]}}'
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
                                'a1' => 'data-validation-scheme',
                                'b1' => '{"date":{"type":"string","validators":[]},"age":{"type":"number","validators":[{"age != null":"Required!"},{"age > 25":"Should be higher"}]}}'
                            ]
                        ]
                    ]
                ],
            ],
        ];
    }
}