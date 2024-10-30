<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingBackgroundRepeatTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'backgroundRepeat';

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
                            'a1' => 'repeat-x'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-repeat: repeat-x;}
'
            ],
            'one_value_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'repeat-x',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-repeat: var(--css-var,repeat-x);}
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
                'expectedCss' => '.uni-node-abcd {background-repeat: inherit;}
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
                'expectedCss' => '.uni-node-abcd {background-repeat: var(--css-var);}
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
                'expectedCss' => '.uni-node-abcd {background-repeat: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'two_values_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'repeat-x',
                        ],
                        [
                            'a1' => 'repeat space',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-repeat: repeat-x,repeat space;}
'
            ],
            'two_values_one_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'repeat-x',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 'repeat space',
                        ],
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-repeat: var(--css-var-1,repeat-x),repeat space;}
'
            ],
            'two_values_two_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'repeat-x',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 'repeat space',
                            'v1' => ['css-var-2']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-repeat: var(--css-var-1,repeat-x),var(--css-var-2,repeat space);}
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
                'expectedCss' => '.uni-node-abcd {background-repeat: var(--css-var-1),var(--css-var-2);}
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
                'expectedCss' => '.uni-node-abcd {background-repeat: inherit;}
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
                'expectedCss' => '.uni-node-abcd {background-repeat: var(--css-var-1,--css-var-2,--css-var-3),var(--css-var-4,--css-var-5,--css-var-6);}
'
            ],
        ];
    }
}