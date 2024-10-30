<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingBackgroundSizeTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'backgroundSize';

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
                            'c1' => 'contain'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: contain;}
'
            ],
            'keyword_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'c1' => 'cover',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: var(--css-var,cover);}
'
            ],
            'two_auto_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'auto',
                            'b2' => 'auto',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: auto;}
'
            ],
            'two_auto_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'auto',
                            'b2' => 'auto',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: var(--css-var,auto);}
'
            ],
            'custom_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: 10% 20px;}
'
            ],
            'custom_just_height_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a2' => 20,
                            'b2' => 'px',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: auto 20px;}
'
            ],
            'custom_just_width_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 20,
                            'b1' => 'px',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: 20px;}
'
            ],
            'custom_auto_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'b2' => 'auto',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: 10% auto;}
'
            ],
            'custom_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: var(--css-var,10% 20px);}
'
            ],
            'custom_auto_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'auto',
                            'a2' => 20,
                            'b2' => 'px',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: var(--css-var,auto 20px);}
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
                'expectedCss' => '.uni-node-abcd {background-size: inherit;}
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
                'expectedCss' => '.uni-node-abcd {background-size: var(--css-var);}
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
                'expectedCss' => '.uni-node-abcd {background-size: var(--css-var-1,--css-var-2,--css-var-3);}
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
                        ],
                        [
                            'a1' => 10,
                            'b1' => 'auto',
                            'a2' => 20,
                            'b2' => 'px',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: 10% 20px,auto 20px;}
'
            ],
            'two_values_one_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => 'auto',
                            'a2' => 20,
                            'b2' => 'px',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'a2' => 20,
                            'b2' => 'px',
                        ],
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: var(--css-var-1,auto 20px),10% 20px;}
'
            ],
            'two_values_two_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => 'auto',
                            'a2' => 20,
                            'b2' => 'px',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'c1' => 'contain',
                            'v1' => ['css-var-2']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-size: var(--css-var-1,auto 20px),var(--css-var-2,contain);}
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
                'expectedCss' => '.uni-node-abcd {background-size: var(--css-var-1),var(--css-var-2);}
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
                'expectedCss' => '.uni-node-abcd {background-size: inherit;}
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
                'expectedCss' => '.uni-node-abcd {background-size: var(--css-var-1,--css-var-2,--css-var-3),var(--css-var-4,--css-var-5,--css-var-6);}
'
            ],
        ];
    }
}