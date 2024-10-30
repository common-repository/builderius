<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingBackgroundPositionTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'backgroundPosition';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'keyword_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'c1' => 'top',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: top;}
'
            ],
            'two_keywords_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'top',
                            'b2' => 'left'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: top left;}
'
            ],
            'one_of_keywords_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b2' => 'right'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: right;}
'
            ],
            'two_samekeywords_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'bottom',
                            'b2' => 'bottom'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: bottom;}
'
            ],
            'two_opositekeywords1_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'left',
                            'b2' => 'right'
                        ]
                    ]
                ],
                'expectedCss' => ''
            ],
            'two_opositekeywords2_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'right',
                            'b2' => 'left'
                        ]
                    ]
                ],
                'expectedCss' => ''
            ],
            'two_opositekeywords3_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'bottom',
                            'b2' => 'top'
                        ]
                    ]
                ],
                'expectedCss' => ''
            ],
            'two_opositekeywords4_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'top',
                            'b2' => 'bottom'
                        ]
                    ]
                ],
                'expectedCss' => ''
            ],
            'keyword_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'c1' => 'top',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: var(--css-var,top);}
'
            ],
            'x_position_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 25,
                            'b1' => '%'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: 25%;}
'
            ],
            'x_position_y_keyword_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 25,
                            'b1' => '%',
                            'b2' => 'bottom'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: 25% bottom;}
'
            ],
            'y_position_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a2' => 25,
                            'b2' => '%'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: 50% 25%;}
'
            ],
            'x_keyword_y_position_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'left',
                            'a2' => 25,
                            'b2' => '%'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: left 25%;}
'
            ],
            'xy_position_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 25,
                            'b1' => '%',
                            'a2' => 75,
                            'b2' => '%'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: 25% 75%;}
'
            ],
            'xy_position_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 25,
                            'b1' => '%',
                            'a2' => 75,
                            'b2' => '%',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: var(--css-var,25% 75%);}
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
                'expectedCss' => '.uni-node-abcd {background-position: inherit;}
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
                'expectedCss' => '.uni-node-abcd {background-position: var(--css-var);}
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
                'expectedCss' => '.uni-node-abcd {background-position: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'two_values_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 25,
                            'b1' => '%',
                            'a2' => 75,
                            'b2' => '%'
                        ],
                        [
                            'a1' => 30,
                            'b1' => '%',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: 25% 75%,30%;}
'
            ],
            'two_values_one_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 25,
                            'b1' => '%',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 25,
                            'b1' => '%',
                            'a2' => 75,
                            'b2' => '%'
                        ],
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: var(--css-var-1,25%),25% 75%;}
'
            ],
            'two_values_two_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a2' => 75,
                            'b2' => '%',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 25,
                            'b1' => '%',
                            'v1' => ['css-var-2']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-position: var(--css-var-1,50% 75%),var(--css-var-2,25%);}
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
                'expectedCss' => '.uni-node-abcd {background-position: var(--css-var-1),var(--css-var-2);}
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
                'expectedCss' => '.uni-node-abcd {background-position: inherit;}
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
                'expectedCss' => '.uni-node-abcd {background-position: var(--css-var-1,--css-var-2,--css-var-3),var(--css-var-4,--css-var-5,--css-var-6);}
'
            ],
        ];
    }
}