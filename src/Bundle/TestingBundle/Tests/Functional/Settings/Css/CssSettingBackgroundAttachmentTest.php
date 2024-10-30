<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingBackgroundAttachmentTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'backgroundAttachment';

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
                            'a1' => 'scroll'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-attachment: scroll;}
'
            ],
            'one_value_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'scroll',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-attachment: var(--css-var,scroll);}
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
                'expectedCss' => '.uni-node-abcd {background-attachment: inherit;}
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
                'expectedCss' => '.uni-node-abcd {background-attachment: var(--css-var);}
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
                'expectedCss' => '.uni-node-abcd {background-attachment: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'two_values_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'scroll',
                        ],
                        [
                            'a1' => 'local',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-attachment: scroll,local;}
'
            ],
            'two_values_one_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'scroll',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 'local',
                        ],
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-attachment: var(--css-var-1,scroll),local;}
'
            ],
            'two_values_two_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'scroll',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'a1' => 'local',
                            'v1' => ['css-var-2']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-attachment: var(--css-var-1,scroll),var(--css-var-2,local);}
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
                'expectedCss' => '.uni-node-abcd {background-attachment: var(--css-var-1),var(--css-var-2);}
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
                'expectedCss' => '.uni-node-abcd {background-attachment: inherit;}
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
                'expectedCss' => '.uni-node-abcd {background-attachment: var(--css-var-1,--css-var-2,--css-var-3),var(--css-var-4,--css-var-5,--css-var-6);}
'
            ],
        ];
    }
}