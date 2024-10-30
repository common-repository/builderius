<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingTransformTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'transform';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'globalvalue_no_var' => [
                'submittedValues' => [
                    'g1' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {-ms-transform: inherit;-webkit-transform: inherit;transform: inherit;}
'
            ],
            'globalvalue_with_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'v1' => ['css-var'],
                ],
                'expectedCss' => '.uni-node-abcd {-ms-transform: var(--css-var,inherit);-webkit-transform: var(--css-var,inherit);transform: var(--css-var,inherit);}
'
            ],
            'globalvalue_one_translateX_no_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'c1' => 'translateX'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {-ms-transform: inherit;-webkit-transform: inherit;transform: inherit;}
'
            ],
            'one_translateX_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'c1' => 'translateX'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {-ms-transform: translateX(10%);-webkit-transform: translateX(10%);transform: translateX(10%);}
'
            ],
            'one_scaleX_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'c1' => 'scaleX'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {-ms-transform: scaleX(10);-webkit-transform: scaleX(10);transform: scaleX(10);}
'
            ],
            'one_translateX_with_var' => [
                'submittedValues' => [
                    'v1' => ['css-var'],
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'c1' => 'translateX'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {-ms-transform: var(--css-var,translateX(10%));-webkit-transform: var(--css-var,translateX(10%));transform: var(--css-var,translateX(10%));}
'
            ],
            'one_scaleX_with_var' => [
                'submittedValues' => [
                    'v1' => ['css-var'],
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'c1' => 'scaleX'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {-ms-transform: var(--css-var,scaleX(10));-webkit-transform: var(--css-var,scaleX(10));transform: var(--css-var,scaleX(10));}
'
            ],
            'one_single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-transform: var(--css-var);-webkit-transform: var(--css-var);transform: var(--css-var);}
'
            ],
            'one_multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-transform: var(--css-var-1,--css-var-2,--css-var-3);-webkit-transform: var(--css-var-1,--css-var-2,--css-var-3);transform: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'two_translateX_scaleX_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'c1' => 'translateX'
                        ],
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'c1' => 'scaleX'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {-ms-transform: translateX(10%) scaleX(10);-webkit-transform: translateX(10%) scaleX(10);transform: translateX(10%) scaleX(10);}
'
            ],
            'two_translateX_scaleX_with_var' => [
                'submittedValues' => [
                    'v1' => ['css-var-1'],
                    'i1' => [
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'c1' => 'translateX'
                        ],
                        [
                            'a1' => 10,
                            'b1' => '%',
                            'c1' => 'scaleX'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {-ms-transform: var(--css-var-1,translateX(10%) scaleX(10));-webkit-transform: var(--css-var-1,translateX(10%) scaleX(10));transform: var(--css-var-1,translateX(10%) scaleX(10));}
'
            ],
        ];
    }
}