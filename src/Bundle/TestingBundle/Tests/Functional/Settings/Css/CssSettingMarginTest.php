<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingMarginTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'margin';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'margintop_value_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {margin-top: 10px;}
'
            ],
            'margintop_global_value_no_var' => [
                'submittedValues' => [
                    'b1' => 'auto'
                ],
                'expectedCss' => '.uni-node-abcd {margin-top: auto;}
'
            ],
            'margintop_value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {margin-top: var(--css-var,10px);}
'
            ],
            'margintop_global_value_with_var' => [
                'submittedValues' => [
                    'b1' => 'auto',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {margin-top: var(--css-var,auto);}
'
            ],
            'margintop_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {margin-top: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'margintop_single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {margin-top: var(--css-var);}
'
            ],
            'margintop_multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {margin-top: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'marginright_value_no_var' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {margin-right: 10px;}
'
            ],
            'marginright_global_value_no_var' => [
                'submittedValues' => [
                    'b2' => 'auto'
                ],
                'expectedCss' => '.uni-node-abcd {margin-right: auto;}
'
            ],
            'marginright_value_with_var' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px',
                    'v2' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {margin-right: var(--css-var,10px);}
'
            ],
            'marginright_global_value_with_var' => [
                'submittedValues' => [
                    'b2' => 'auto',
                    'v2' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {margin-right: var(--css-var,auto);}
'
            ],
            'marginright_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px',
                    'v2' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {margin-right: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'marginright_single_var' => [
                'submittedValues' => [
                    'v2' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {margin-right: var(--css-var);}
'
            ],
            'marginright_multiple_vars' => [
                'submittedValues' => [
                    'v2' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {margin-right: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'marginbottom_value_no_var' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {margin-bottom: 10px;}
'
            ],
            'marginbottom_global_value_no_var' => [
                'submittedValues' => [
                    'b3' => 'auto'
                ],
                'expectedCss' => '.uni-node-abcd {margin-bottom: auto;}
'
            ],
            'marginbottom_value_with_var' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px',
                    'v3' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {margin-bottom: var(--css-var,10px);}
'
            ],
            'marginbottom_global_value_with_var' => [
                'submittedValues' => [
                    'b3' => 'auto',
                    'v3' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {margin-bottom: var(--css-var,auto);}
'
            ],
            'marginbottom_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px',
                    'v3' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {margin-bottom: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'marginbottom_single_var' => [
                'submittedValues' => [
                    'v3' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {margin-bottom: var(--css-var);}
'
            ],
            'marginbottom_multiple_vars' => [
                'submittedValues' => [
                    'v3' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {margin-bottom: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'marginleft_value_no_var' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {margin-left: 10px;}
'
            ],
            'marginleft_global_value_no_var' => [
                'submittedValues' => [
                    'b4' => 'auto'
                ],
                'expectedCss' => '.uni-node-abcd {margin-left: auto;}
'
            ],
            'marginleft_value_with_var' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px',
                    'v4' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {margin-left: var(--css-var,10px);}
'
            ],
            'marginleft_global_value_with_var' => [
                'submittedValues' => [
                    'b4' => 'auto',
                    'v4' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {margin-left: var(--css-var,auto);}
'
            ],
            'marginleft_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px',
                    'v4' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {margin-left: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'marginleft_single_var' => [
                'submittedValues' => [
                    'v4' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {margin-left: var(--css-var);}
'
            ],
            'marginleft_multiple_vars' => [
                'submittedValues' => [
                    'v4' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {margin-left: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'margintop_marginright_value_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'a2' => 10,
                    'b2' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {margin-top: 10px;margin-right: 10px;}
'
            ],
            'margintop_marginright_global_value_no_var' => [
                'submittedValues' => [
                    'b1' => 'auto',
                    'b2' => 'auto'
                ],
                'expectedCss' => '.uni-node-abcd {margin-top: auto;margin-right: auto;}
'
            ],
            'margintop_marginright_value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1'],
                    'b2' => 'auto',
                    'v2' => ['css-var-2']
                ],
                'expectedCss' => '.uni-node-abcd {margin-top: var(--css-var-1,10px);margin-right: var(--css-var-2,auto);}
'
            ],
            'margintop_marginleft_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3'],
                    'b4' => 'auto',
                    'v4' => ['css-var-4', 'css-var-5', 'css-var-6']
                ],
                'expectedCss' => '.uni-node-abcd {margin-top: var(--css-var-1,--css-var-2,--css-var-3,10px);margin-left: var(--css-var-4,--css-var-5,--css-var-6,auto);}
'
            ],
            'margintop_marginbottom_single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var-1'],
                    'v3' => ['css-var-2']
                ],
                'expectedCss' => '.uni-node-abcd {margin-top: var(--css-var-1);margin-bottom: var(--css-var-2);}
'
            ],
            'marginright_marginbottom_multiple_vars' => [
                'submittedValues' => [
                    'v2' => ['css-var-1', 'css-var-2', 'css-var-3'],
                    'v3' => ['css-var-4', 'css-var-5', 'css-var-6']
                ],
                'expectedCss' => '.uni-node-abcd {margin-right: var(--css-var-1,--css-var-2,--css-var-3);margin-bottom: var(--css-var-4,--css-var-5,--css-var-6);}
'
            ],
            'margin_values_no_vars' => [
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
                'expectedCss' => '.uni-node-abcd {margin-top: 10px;margin-right: 10px;margin-bottom: 10px;margin-left: 10px;}
'
            ]
        ];
    }
}