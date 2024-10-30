<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ExpressionLanguage;

class SmartformTransformationsFacadeFunctionTest extends AbstractElFunctionTest
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
                value.a1 === "data-transformations" && value.b1 !== null
              }
            )
          ) == true ?
          (
            count(tempVariable("flt")) === 1 ?
              foreach(unserialize(tempVariable("flt")[0].b1), (v) -> { {"a1": (isset(v, "name") != false ? v.name : ""), "b1": (isset(v, "value") != false ? v.value : ""), "c1": (isset(v, "defValue") != false ? v.defValue : "")} }, false) :
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
                            isset(v, "a1") && isset(v, "b1") && isset(v, "c1") && !(v.a1 in [null, ""]) && !(v.b1 in [null, ""]) && !(v.c1 in [null, ""])
                          ) ?
                          {"name": v.a1, "value": v.b1, "defValue": v.c1} :
                          (
                            (
                              isset(v, "a1") && isset(v, "b1") && !(v.a1 in [null, ""]) && !(v.b1 in [null, ""])
                            ) ?
                            {"name": v.a1, "value": v.b1} : 
                            (
                              (
                                isset(v, "a1") && isset(v, "c1") && !(v.a1 in [null, ""]) && !(v.c1 in [null, ""])
                              ) ?
                              {"name": v.a1, "defValue": v.c1} :
                              (
                                (
                                  isset(v, "b1") && isset(v, "c1") && !(v.b1 in [null, ""]) && !(v.c1 in [null, ""])
                                ) ?
                                {"value": v.b1, "defValue": v.c1} :
                                (
                                  isset(v, "a1") && !(v.a1 in [null, ""])
                                ) ?
                                {"name": v.a1} :
                                (
                                  (isset(v, "b1") && !(v.b1 in [null, ""])) ? {"value": v.b1} : {"defValue": v.c1}
                                )
                              ) 
                            )
                          )
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
                              value.a1 === "data-transformations"
                            }
                          )
                        ) == true ?
                        (
                          tempVariable("flt") !== -1 ?
                          (
                            set(s, "i1[" ~ tempVariable("flt") ~ "].b1", serialize(tempVariable("finalJson")))
                          ) :
                          (
                            {"i1": push(s.i1, {"a1": "data-transformations", "b1": serialize(tempVariable("finalJson"))})}
                          )
                        ) : s
                      ) :
                      (
                        {"i1": [{"a1": "data-transformations", "b1": serialize(tempVariable("finalJson"))}]}
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
                            value.a1 === "data-transformations"
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
                        'a1' => 'area',
                        'b1' => 'width*height',
                        'c1' => '0'
                    ],
                    [
                        'a1' => 'total',
                        'b1' => 'pricaRaw*qty',
                        'c1' => '0'
                    ]
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-transformations',
                                'b1' => '[{"name":"area","value":"width*height","defValue":"0"},{"name": "total", "value": "pricaRaw*qty", "defValue": 0}]'
                            ]
                        ]
                    ]
                ]
            ],
            'setting_to_facade_no_def_value' => [
                'expression' => $settingToFacadeExpression,
                'expectedResult' => [
                    [
                        'a1' => 'area',
                        'b1' => 'width*height',
                        'c1' => ''
                    ],
                    [
                        'a1' => 'total',
                        'b1' => '',
                        'c1' => '0'
                    ]
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-transformations',
                                'b1' => '[{"name":"area","value":"width*height"},{"name": "total", "defValue": 0}]'
                            ]
                        ]
                    ]
                ]
            ],
            'setting_to_facade_no_name' => [
                'expression' => $settingToFacadeExpression,
                'expectedResult' => [
                    [
                        'a1' => '',
                        'b1' => 'width*height',
                        'c1' => '0'
                    ],
                    [
                        'a1' => 'total',
                        'b1' => 'pricaRaw*qty',
                        'c1' => ''
                    ]
                ],
                'context' => [
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-transformations',
                                'b1' => '[{"value":"width*height","defValue":"0"},{"name": "total", "value": "pricaRaw*qty"}]'
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
                                'a1' => 'data-transformations',
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
                            'a1' => 'data-transformations',
                            'b1' => '[{"name":"area","value":"width*height","defValue":"0"}]'
                        ]
                    ]
                ],
                'context' => [
                    'f' => [
                        (object)[
                            'a1' => 'area',
                            'b1' => 'width*height',
                            'c1' => '0'
                        ]
                    ],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-transformations',
                                'b1' => '[{"name":"area","value":"width*height","defValue":"0"},{"name": "total", "value": "pricaRaw*qty", "defValue": 0}]'
                            ]
                        ]
                    ]
                ],
            ],
            'facade_to_setting_no_def_value' => [
                'expression' => $facadeToSettingExpression,
                'expectedResult' => (object)[
                    'i1' => [
                        (object)[
                            'a1' => 'data-transformations',
                            'b1' => '[{"name":"area","value":"width*height"}]'
                        ]
                    ]
                ],
                'context' => [
                    'f' => [
                        (object)[
                            'a1' => 'area',
                            'b1' => 'width*height'
                        ]
                    ],
                    's' => (object)[
                        'i1' => [
                            (object)[
                                'a1' => 'data-transformations',
                                'b1' => '[{"name":"area","value":"width*height","defValue":"0"},{"name": "total", "value": "pricaRaw*qty", "defValue": 0}]'
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
                            'a1' => 'data-transformations',
                            'b1' => '[{"name":"area","value":"width*height","defValue":"0"}]'
                        ]
                    ]
                ],
                'context' => [
                    'f' => [
                        (object)[
                            'a1' => 'area',
                            'b1' => 'width*height',
                            'c1' => '0'
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
                                'a1' => 'data-transformations',
                                'b1' => '[{"name":"area","value":"width*height","defValue":"0"}]'
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
                                'a1' => 'data-transformations',
                                'b1' => '[{"name":"area","value":"width*height","defValue":"0"}]'
                            ]
                        ]
                    ]
                ],
            ],
        ];
    }
}