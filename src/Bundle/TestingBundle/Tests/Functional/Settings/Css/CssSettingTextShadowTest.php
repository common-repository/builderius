<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingTextShadowTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'textShadow';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'one_value_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => 'px',
                            'a2' => 10,
                            'b2' => 'px',
                            'd1' => 2,
                            'd2' => '%',
                            'f1' => 'rgba(164, 156, 156, 1.00)'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {text-shadow: 10px 10px 2% rgba(164, 156, 156, 1.00);}
'
            ],
            'one_value_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => 'px',
                            'a2' => 10,
                            'b2' => 'px',
                            'd1' => 2,
                            'd2' => '%',
                            'f1' => 'rgba(164, 156, 156, 1.00)',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {text-shadow: var(--css-var,10px 10px 2% rgba(164, 156, 156, 1.00));}
'
            ],
            'global_value' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                ],
                'expectedCss' => '.uni-node-abcd {text-shadow: inherit;}
'
            ],
            'global_value_with_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => 'px',
                            'a2' => 10,
                            'b2' => 'px',
                            'd1' => 2,
                            'd2' => '%',
                            'f1' => 'rgba(164, 156, 156, 1.00)',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {text-shadow: inherit;}
'
            ],
            'one_wrong_value_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%'
                        ]
                    ]
                ],
                'expectedCss' => ''
            ],
            'one_wrong_value_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {text-shadow: var(--css-var);}
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
                'expectedCss' => '.uni-node-abcd {text-shadow: var(--css-var);}
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
                'expectedCss' => '.uni-node-abcd {text-shadow: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'two_values_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 20,
                            'b1' => 'px',
                            'a2' => 20,
                            'b2' => 'px',
                            'd1' => 2,
                            'd2' => '%',
                            'f1' => 'rgba(164, 156, 156, 1.00)',
                        ],
                        [
                            'a1' => 10,
                            'b1' => 'px',
                            'a2' => 10,
                            'b2' => 'px',
                            'd1' => 2,
                            'd2' => '%',
                            'f1' => 'rgba(164, 156, 156, 1.00)',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {text-shadow: 20px 20px 2% rgba(164, 156, 156, 1.00),10px 10px 2% rgba(164, 156, 156, 1.00);}
'
            ],
            'two_values_one_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 20,
                            'b1' => 'px',
                            'a2' => 20,
                            'b2' => 'px',
                            'd1' => 2,
                            'd2' => '%',
                            'f1' => 'rgba(164, 156, 156, 1.00)',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 10,
                            'b1' => 'px',
                            'a2' => 10,
                            'b2' => 'px',
                            'd1' => 2,
                            'd2' => '%',
                            'f1' => 'rgba(164, 156, 156, 1.00)',
                        ],
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {text-shadow: var(--css-var-1,20px 20px 2% rgba(164, 156, 156, 1.00)),10px 10px 2% rgba(164, 156, 156, 1.00);}
'
            ],
            'two_values_two_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 20,
                            'b1' => 'px',
                            'a2' => 20,
                            'b2' => 'px',
                            'd1' => 2,
                            'd2' => '%',
                            'f1' => 'rgba(164, 156, 156, 1.00)',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 10,
                            'b1' => 'px',
                            'a2' => 10,
                            'b2' => 'px',
                            'd1' => 2,
                            'd2' => '%',
                            'f1' => 'rgba(164, 156, 156, 1.00)',
                            'v1' => ['css-var-1']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {text-shadow: var(--css-var-1,20px 20px 2% rgba(164, 156, 156, 1.00)),var(--css-var-1,10px 10px 2% rgba(164, 156, 156, 1.00));}
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
                'expectedCss' => '.uni-node-abcd {text-shadow: var(--css-var-1),var(--css-var-2);}
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
                'expectedCss' => '.uni-node-abcd {text-shadow: var(--css-var-1,--css-var-2,--css-var-3),var(--css-var-4,--css-var-5,--css-var-6);}
'
            ],
        ];
    }
}