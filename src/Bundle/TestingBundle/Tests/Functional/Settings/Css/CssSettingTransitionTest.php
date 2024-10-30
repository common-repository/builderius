<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingTransitionTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'transition';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'property_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'background',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: background;}
'
            ],
            'property_duration_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'width',
                            'b1' => 2,
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: width 2s;}
'
            ],
            'property_duration_delay_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'width',
                            'b1' => 2,
                            'd1' => 1
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: width 2s 1s;}
'
            ],
            'property_duration_delay_function_no_args_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'width',
                            'b1' => 2,
                            'c1' => 'ease-in-out',
                            'd1' => 1
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: width 2s ease-in-out 1s;}
'
            ],
            'property_noduration_delay_function_no_args_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'width',
                            'c1' => 'ease-in-out',
                            'd1' => 1
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: width 0s ease-in-out 1s;}
'
            ],
            'property_duration_delay_function_with_args_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'width',
                            'b1' => 2,
                            'c1' => 'ease-in-out',
                            /*'c1' => 'steps',
                            'c2' => [4, 'jump-end'],*/
                            'd1' => 1
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: width 2s ease-in-out 1s;}
'
            ],
            'property_duration_delay_function2_with_args_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'width',
                            'b1' => 2,
                            'c1' => 'ease-in-out',
                            /*'c1' => 'cubic-bezier',
                            'c2' => [0.1, 0.7, 1.0, 0.1],*/
                            'd1' => 1
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: width 2s ease-in-out 1s;}
'
            ],
            'property_duration_function_with_args_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'width',
                            'b1' => 2,
                            'c1' => 'ease-in-out',
                            /*'c1' => 'cubic-bezier',
                            'c2' => [0.1, 0.7, 1.0, 0.1],*/
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: width 2s ease-in-out;}
'
            ],
            'noproperty_duration_function_with_args_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 2,
                            'c1' => 'ease-in-out',
                            /*'c1' => 'cubic-bezier',
                            'c2' => [0.1, 0.7, 1.0, 0.1],*/
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: 2s ease-in-out;}
'
            ],
            'offset_xy_blur_stroke_color_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'width',
                            'b1' => 2,
                            'c1' => 'ease-in-out',
                            /*'c1' => 'steps',
                            'c2' => [4, 'jump-end'],*/
                            'd1' => 1,
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: var(--css-var,width 2s ease-in-out 1s);}
'
            ],
            'global_value_no_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                ],
                'expectedCss' => '.uni-node-abcd {transition: inherit;}
'
            ],
            'global_value_with_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'i1' => [
                        [
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: inherit;}
'
            ],
            'one_single_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: var(--css-var);}
'
            ],
            'one_multiple_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'two_values_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'width',
                            'b1' => 2,
                            'c1' => 'ease-in-out',
                            'd1' => 1,
                        ],
                        [
                            'a1' => 'width',
                            'b1' => 2,
                            'c1' => 'ease-in-out',
                            /*'c1' => 'steps',
                            'c2' => [4, 'jump-end'],*/
                            'd1' => 1,
                        ]
                    ]
                ],
                '.uni-node-abcd {transition: width 2s ease-in-out 1s,width 2s ease-in-out 1s;}
'
            ],
            'two_values_one_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'width',
                            'b1' => 2,
                            'c1' => 'ease-in-out',
                            'd1' => 1,
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 'width',
                            'b1' => 2,
                            'c1' => 'ease-in-out',
                            /*'c1' => 'steps',
                            'c2' => [4, 'jump-end'],*/
                            'd1' => 1,
                        ],
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: var(--css-var-1,width 2s ease-in-out 1s),width 2s ease-in-out 1s;}
'
            ],
            'two_values_two_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'width',
                            'b1' => 2,
                            'c1' => 'ease-in-out',
                            /*'c1' => 'steps',
                            'c2' => [4, 'jump-end'],*/
                            'd1' => 1,
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 'width',
                            'b1' => 2,
                            'c1' => 'ease-in-out',
                            'd1' => 1,
                            'v1' => ['css-var-2']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: var(--css-var-1,width 2s ease-in-out 1s),var(--css-var-2,width 2s ease-in-out 1s);}
'
            ],
            'two_single_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'v1' => ['css-var-1']
                        ],
                        [
                            'v1' => ['css-var-2']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: var(--css-var-1),var(--css-var-2);}
'
            ],
            'global_with_two_single_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'i1' => [
                        [
                            'v1' => ['css-var-1']
                        ],
                        [
                            'v1' => ['css-var-2']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: inherit;}
'
            ],
            'two_multiple_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                        ],
                        [
                            'v1' => ['css-var-4', 'css-var-5', 'css-var-6']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {transition: var(--css-var-1,--css-var-2,--css-var-3),var(--css-var-4,--css-var-5,--css-var-6);}
'
            ],
        ];
    }
}