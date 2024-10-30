<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingPaddingTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'padding';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'paddingtop_value_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: 10px;}
'
            ],
            'paddingtop_global_value_no_var' => [
                'submittedValues' => [
                    'b1' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: inherit;}
'
            ],
            'paddingtop_value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: var(--css-var,10px);}
'
            ],
            'paddingtop_global_value_with_var' => [
                'submittedValues' => [
                    'b1' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: var(--css-var,inherit);}
'
            ],
            'paddingtop_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'paddingtop_single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: var(--css-var);}
'
            ],
            'paddingtop_multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'paddingright_value_no_var' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {padding-right: 10px;}
'
            ],
            'paddingright_global_value_no_var' => [
                'submittedValues' => [
                    'b2' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {padding-right: inherit;}
'
            ],
            'paddingright_value_with_var' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px',
                    'v2' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {padding-right: var(--css-var,10px);}
'
            ],
            'paddingright_global_value_with_var' => [
                'submittedValues' => [
                    'b2' => 'inherit',
                    'v2' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {padding-right: var(--css-var,inherit);}
'
            ],
            'paddingright_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px',
                    'v2' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {padding-right: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'paddingright_single_var' => [
                'submittedValues' => [
                    'v2' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {padding-right: var(--css-var);}
'
            ],
            'paddingright_multiple_vars' => [
                'submittedValues' => [
                    'v2' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {padding-right: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'paddingbottom_value_no_var' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {padding-bottom: 10px;}
'
            ],
            'paddingbottom_global_value_no_var' => [
                'submittedValues' => [
                    'b3' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {padding-bottom: inherit;}
'
            ],
            'paddingbottom_value_with_var' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px',
                    'v3' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {padding-bottom: var(--css-var,10px);}
'
            ],
            'paddingbottom_global_value_with_var' => [
                'submittedValues' => [
                    'b3' => 'inherit',
                    'v3' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {padding-bottom: var(--css-var,inherit);}
'
            ],
            'paddingbottom_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px',
                    'v3' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {padding-bottom: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'paddingbottom_single_var' => [
                'submittedValues' => [
                    'v3' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {padding-bottom: var(--css-var);}
'
            ],
            'paddingbottom_multiple_vars' => [
                'submittedValues' => [
                    'v3' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {padding-bottom: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'paddingleft_value_no_var' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {padding-left: 10px;}
'
            ],
            'paddingleft_global_value_no_var' => [
                'submittedValues' => [
                    'b4' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {padding-left: inherit;}
'
            ],
            'paddingleft_value_with_var' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px',
                    'v4' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {padding-left: var(--css-var,10px);}
'
            ],
            'paddingleft_global_value_with_var' => [
                'submittedValues' => [
                    'b4' => 'inherit',
                    'v4' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {padding-left: var(--css-var,inherit);}
'
            ],
            'paddingleft_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px',
                    'v4' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {padding-left: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'paddingleft_single_var' => [
                'submittedValues' => [
                    'v4' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {padding-left: var(--css-var);}
'
            ],
            'paddingleft_multiple_vars' => [
                'submittedValues' => [
                    'v4' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {padding-left: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'paddingtop_paddingright_value_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'a2' => 10,
                    'b2' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: 10px;padding-right: 10px;}
'
            ],
            'paddingtop_paddingright_global_value_no_var' => [
                'submittedValues' => [
                    'b1' => 'inherit',
                    'b2' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: inherit;padding-right: inherit;}
'
            ],
            'paddingtop_paddingright_value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1'],
                    'b2' => 'inherit',
                    'v2' => ['css-var-2']
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: var(--css-var-1,10px);padding-right: var(--css-var-2,inherit);}
'
            ],
            'paddingtop_paddingleft_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3'],
                    'b4' => 'inherit',
                    'v4' => ['css-var-4', 'css-var-5', 'css-var-6']
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: var(--css-var-1,--css-var-2,--css-var-3,10px);padding-left: var(--css-var-4,--css-var-5,--css-var-6,inherit);}
'
            ],
            'paddingtop_paddingbottom_single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var-1'],
                    'v3' => ['css-var-2']
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: var(--css-var-1);padding-bottom: var(--css-var-2);}
'
            ],
            'paddingright_paddingbottom_multiple_vars' => [
                'submittedValues' => [
                    'v2' => ['css-var-1', 'css-var-2', 'css-var-3'],
                    'v3' => ['css-var-4', 'css-var-5', 'css-var-6']
                ],
                'expectedCss' => '.uni-node-abcd {padding-right: var(--css-var-1,--css-var-2,--css-var-3);padding-bottom: var(--css-var-4,--css-var-5,--css-var-6);}
'
            ],
            'padding_values_no_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'a2' => 10,
                    'b2' => 'px',
                    'a3' => 10,
                    'b3' => 'px',
                    'a4' => 10,
                    'b4' => 'px',
                ],
                'expectedCss' => '.uni-node-abcd {padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px;}
'
            ]
        ];
    }
}