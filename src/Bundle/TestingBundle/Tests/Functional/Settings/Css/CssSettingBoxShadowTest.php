<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingBoxShadowTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'boxShadow';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'inset_offset_xy_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                            'c1' => true
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: inset 10% 20px;}
'
            ],
            'inset_offset_xy_blur_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                            'c1' => true,
                            'd1' => 5,
                            'd2' => '%'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: inset 10% 20px 5%;}
'
            ],
            'inset_offset_xy_blur_stroke_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                            'c1' => true,
                            'd1' => 5,
                            'd2' => '%',
                            'e1' => 1,
                            'e2' => '%'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: inset 10% 20px 5% 1%;}
'
            ],
            'inset_offset_xy_blur_stroke_color_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                            'c1' => true,
                            'd1' => 5,
                            'd2' => '%',
                            'e1' => 1,
                            'e2' => '%',
                            'f1' => 'rgba(0,0,0,0.1)'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: inset 10% 20px 5% 1% rgba(0,0,0,0.1);}
'
            ],
            'offset_xy_blur_stroke_color_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                            'd1' => 5,
                            'd2' => '%',
                            'e1' => 1,
                            'e2' => '%',
                            'f1' => 'rgba(0,0,0,0.1)'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: 10% 20px 5% 1% rgba(0,0,0,0.1);}
'
            ],
            'offset_xy_color_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                            'f1' => 'rgba(0,0,0,0.1)'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: 10% 20px rgba(0,0,0,0.1);}
'
            ],
            'offset_x_blur_stroke_color_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'd1' => 5,
                            'd2' => '%',
                            'e1' => 1,
                            'e2' => '%',
                            'f1' => 'rgba(0,0,0,0.1)'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: 10% 0 5% 1% rgba(0,0,0,0.1);}
'
            ],
            'offset_y_blur_stroke_color_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a2' => 20,
                            'b2' => 'px',
                            'd1' => 5,
                            'd2' => '%',
                            'e1' => 1,
                            'e2' => '%',
                            'f1' => 'rgba(0,0,0,0.1)'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: 0 20px 5% 1% rgba(0,0,0,0.1);}
'
            ],
            'blur_stroke_color_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'd1' => 5,
                            'd2' => '%',
                            'e1' => 1,
                            'e2' => '%',
                            'f1' => 'rgba(0,0,0,0.1)'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: 0 0 5% 1% rgba(0,0,0,0.1);}
'
            ],
            'blur_color_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'd1' => 5,
                            'd2' => '%',
                            'f1' => 'rgba(0,0,0,0.1)'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: 0 0 5% rgba(0,0,0,0.1);}
'
            ],
            'stroke_color_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'e1' => 1,
                            'e2' => '%',
                            'f1' => 'rgba(0,0,0,0.1)'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: 0 0 0 1% rgba(0,0,0,0.1);}
'
            ],
            'color_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'f1' => 'rgba(0,0,0,0.1)'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: rgba(0,0,0,0.1);}
'
            ],
            'offset_xy_blur_stroke_color_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                            'd1' => 5,
                            'd2' => '%',
                            'e1' => 1,
                            'e2' => '%',
                            'f1' => 'rgba(0,0,0,0.1)',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: var(--css-var,10% 20px 5% 1% rgba(0,0,0,0.1));}
'
            ],
            'global_value_no_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: inherit;}
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
                'expectedCss' => '.uni-node-abcd {box-shadow: inherit;}
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
                'expectedCss' => '.uni-node-abcd {box-shadow: var(--css-var);}
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
                'expectedCss' => '.uni-node-abcd {box-shadow: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'two_values_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                            'f1' => 'rgba(0,0,0,0.1)',
                        ],
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                            'd1' => 5,
                            'd2' => '%',
                            'e1' => 1,
                            'e2' => '%',
                            'f1' => 'rgba(0,0,0,0.1)',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: 10% 20px rgba(0,0,0,0.1),10% 20px 5% 1% rgba(0,0,0,0.1);}
'
            ],
            'two_values_one_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'd1' => 5,
                            'd2' => '%',
                            'e1' => 1,
                            'e2' => '%',
                            'f1' => 'rgba(0,0,0,0.1)',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                            'd1' => 5,
                            'd2' => '%',
                            'e1' => 1,
                            'e2' => '%',
                            'f1' => 'rgba(0,0,0,0.1)',
                        ],
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: var(--css-var-1,0 0 5% 1% rgba(0,0,0,0.1)),10% 20px 5% 1% rgba(0,0,0,0.1);}
'
            ],
            'two_values_two_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'd1' => 5,
                            'd2' => '%',
                            'e1' => 1,
                            'e2' => '%',
                            'v1' => ['css-var-2']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {box-shadow: var(--css-var-1,10% 20px),var(--css-var-2,0 0 5% 1%);}
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
                'expectedCss' => '.uni-node-abcd {box-shadow: var(--css-var-1),var(--css-var-2);}
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
                'expectedCss' => '.uni-node-abcd {box-shadow: inherit;}
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
                'expectedCss' => '.uni-node-abcd {box-shadow: var(--css-var-1,--css-var-2,--css-var-3),var(--css-var-4,--css-var-5,--css-var-6);}
'
            ],
        ];
    }
}