<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingBackgroundClipTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'backgroundClip';

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
                            'a1' => 'border-box'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {-webkit-background-clip: border-box;background-clip: border-box;}
'
            ],
            'one_value_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'border-box',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {-webkit-background-clip: var(--css-var,border-box);background-clip: var(--css-var,border-box);}
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
                'expectedCss' => '.uni-node-abcd {-webkit-background-clip: inherit;background-clip: inherit;}
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
                'expectedCss' => '.uni-node-abcd {-webkit-background-clip: var(--css-var);background-clip: var(--css-var);}
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
                'expectedCss' => '.uni-node-abcd {-webkit-background-clip: var(--css-var-1,--css-var-2,--css-var-3);background-clip: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'two_values_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'border-box',
                        ],
                        [
                            'a1' => 'padding-box',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {-webkit-background-clip: border-box,padding-box;background-clip: border-box,padding-box;}
'
            ],
            'two_values_one_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'border-box',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 'padding-box',
                        ],
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {-webkit-background-clip: var(--css-var-1,border-box),padding-box;background-clip: var(--css-var-1,border-box),padding-box;}
'
            ],
            'two_values_two_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'border-box',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 'padding-box',
                            'v1' => ['css-var-2']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {-webkit-background-clip: var(--css-var-1,border-box),var(--css-var-2,padding-box);background-clip: var(--css-var-1,border-box),var(--css-var-2,padding-box);}
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
                'expectedCss' => '.uni-node-abcd {-webkit-background-clip: var(--css-var-1),var(--css-var-2);background-clip: var(--css-var-1),var(--css-var-2);}
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
                'expectedCss' => '.uni-node-abcd {-webkit-background-clip: inherit;background-clip: inherit;}
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
                'expectedCss' => '.uni-node-abcd {-webkit-background-clip: var(--css-var-1,--css-var-2,--css-var-3),var(--css-var-4,--css-var-5,--css-var-6);background-clip: var(--css-var-1,--css-var-2,--css-var-3),var(--css-var-4,--css-var-5,--css-var-6);}
'
            ],
        ];
    }
}