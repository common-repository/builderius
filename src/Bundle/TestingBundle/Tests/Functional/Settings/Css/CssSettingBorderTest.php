<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingBorderTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'border';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'bordertop_width_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-top: 10px;}
'
            ],
            'bordertop_style_no_var' => [
                'submittedValues' => [
                    'c1' => 'none'
                ],
                'expectedCss' => '.uni-node-abcd {border-top: none;}
'
            ],
            'bordertop_color_no_var' => [
                'submittedValues' => [
                    'd1' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-top: #F33;}
'
            ],
            'bordertop_width_style_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'c1' => 'dotted'
                ],
                'expectedCss' => '.uni-node-abcd {border-top: 10px dotted;}
'
            ],
            'bordertop_width_color_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'd1' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-top: 10px #F33;}
'
            ],
            'bordertop_style_color_no_var' => [
                'submittedValues' => [
                    'c1' => 'dotted',
                    'd1' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-top: dotted #F33;}
'
            ],
            'bordertop_width_style_color_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'c1' => 'dotted',
                    'd1' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-top: 10px dotted #F33;}
'
            ],
            'bordertop_global_value_no_var' => [
                'submittedValues' => [
                    'g1' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {border-top: inherit;}
'
            ],
            'bordertop_value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-top: var(--css-var,10px);}
'
            ],
            'bordertop_global_value_with_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-top: var(--css-var,inherit);}
'
            ],
            'bordertop_width_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-top: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'bordertop_style_value_with_multiple_vars' => [
                'submittedValues' => [
                    'c1' => 'none',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-top: var(--css-var-1,--css-var-2,--css-var-3,none);}
'
            ],
            'bordertop_color_value_with_multiple_vars' => [
                'submittedValues' => [
                    'd1' => '#F33',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-top: var(--css-var-1,--css-var-2,--css-var-3,#F33);}
'
            ],
            'bordertop_all_values_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'c1' => 'dotted',
                    'd1' => '#F33',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-top: var(--css-var-1,--css-var-2,--css-var-3,10px dotted #F33);}
'
            ],
            'bordertop_single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-top: var(--css-var);}
'
            ],
            'bordertop_multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-top: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'borderbottom_width_no_var' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: 10px;}
'
            ],
            'borderbottom_style_no_var' => [
                'submittedValues' => [
                    'c2' => 'none'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: none;}
'
            ],
            'borderbottom_color_no_var' => [
                'submittedValues' => [
                    'd2' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: #F33;}
'
            ],
            'borderbottom_width_style_no_var' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px',
                    'c2' => 'dotted'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: 10px dotted;}
'
            ],
            'borderbottom_width_color_no_var' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px',
                    'd2' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: 10px #F33;}
'
            ],
            'borderbottom_style_color_no_var' => [
                'submittedValues' => [
                    'c2' => 'dotted',
                    'd2' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: dotted #F33;}
'
            ],
            'borderbottom_width_style_color_no_var' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px',
                    'c2' => 'dotted',
                    'd2' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: 10px dotted #F33;}
'
            ],
            'borderbottom_global_value_no_var' => [
                'submittedValues' => [
                    'g2' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: inherit;}
'
            ],
            'borderbottom_value_with_var' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px',
                    'v2' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: var(--css-var,10px);}
'
            ],
            'borderbottom_global_value_with_var' => [
                'submittedValues' => [
                    'g2' => 'inherit',
                    'v2' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: var(--css-var,inherit);}
'
            ],
            'borderbottom_width_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px',
                    'v2' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'borderbottom_style_value_with_multiple_vars' => [
                'submittedValues' => [
                    'c2' => 'none',
                    'v2' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: var(--css-var-1,--css-var-2,--css-var-3,none);}
'
            ],
            'borderbottom_color_value_with_multiple_vars' => [
                'submittedValues' => [
                    'd2' => '#F33',
                    'v2' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: var(--css-var-1,--css-var-2,--css-var-3,#F33);}
'
            ],
            'borderbottom_all_values_with_multiple_vars' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px',
                    'c2' => 'dotted',
                    'd2' => '#F33',
                    'v2' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: var(--css-var-1,--css-var-2,--css-var-3,10px dotted #F33);}
'
            ],
            'borderbottom_single_var' => [
                'submittedValues' => [
                    'v2' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: var(--css-var);}
'
            ],
            'borderbottom_multiple_vars' => [
                'submittedValues' => [
                    'v2' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'borderleft_width_no_var' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-left: 10px;}
'
            ],
            'borderleft_style_no_var' => [
                'submittedValues' => [
                    'c3' => 'none'
                ],
                'expectedCss' => '.uni-node-abcd {border-left: none;}
'
            ],
            'borderleft_color_no_var' => [
                'submittedValues' => [
                    'd3' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-left: #F33;}
'
            ],
            'borderleft_width_style_no_var' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px',
                    'c3' => 'dotted'
                ],
                'expectedCss' => '.uni-node-abcd {border-left: 10px dotted;}
'
            ],
            'borderleft_width_color_no_var' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px',
                    'd3' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-left: 10px #F33;}
'
            ],
            'borderleft_style_color_no_var' => [
                'submittedValues' => [
                    'c3' => 'dotted',
                    'd3' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-left: dotted #F33;}
'
            ],
            'borderleft_width_style_color_no_var' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px',
                    'c3' => 'dotted',
                    'd3' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-left: 10px dotted #F33;}
'
            ],
            'borderleft_global_value_no_var' => [
                'submittedValues' => [
                    'g3' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {border-left: inherit;}
'
            ],
            'borderleft_value_with_var' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px',
                    'v3' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-left: var(--css-var,10px);}
'
            ],
            'borderleft_global_value_with_var' => [
                'submittedValues' => [
                    'g3' => 'inherit',
                    'v3' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-left: var(--css-var,inherit);}
'
            ],
            'borderleft_width_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px',
                    'v3' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-left: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'borderleft_style_value_with_multiple_vars' => [
                'submittedValues' => [
                    'c3' => 'none',
                    'v3' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-left: var(--css-var-1,--css-var-2,--css-var-3,none);}
'
            ],
            'borderleft_color_value_with_multiple_vars' => [
                'submittedValues' => [
                    'd3' => '#F33',
                    'v3' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-left: var(--css-var-1,--css-var-2,--css-var-3,#F33);}
'
            ],
            'borderleft_all_values_with_multiple_vars' => [
                'submittedValues' => [
                    'a3' => 10,
                    'b3' => 'px',
                    'c3' => 'dotted',
                    'd3' => '#F33',
                    'v3' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-left: var(--css-var-1,--css-var-2,--css-var-3,10px dotted #F33);}
'
            ],
            'borderleft_single_var' => [
                'submittedValues' => [
                    'v3' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-left: var(--css-var);}
'
            ],
            'borderleft_multiple_vars' => [
                'submittedValues' => [
                    'v3' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-left: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'borderright_width_no_var' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-right: 10px;}
'
            ],
            'borderright_style_no_var' => [
                'submittedValues' => [
                    'c4' => 'none'
                ],
                'expectedCss' => '.uni-node-abcd {border-right: none;}
'
            ],
            'borderright_color_no_var' => [
                'submittedValues' => [
                    'd4' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-right: #F33;}
'
            ],
            'borderright_width_style_no_var' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px',
                    'c4' => 'dotted'
                ],
                'expectedCss' => '.uni-node-abcd {border-right: 10px dotted;}
'
            ],
            'borderright_width_color_no_var' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px',
                    'd4' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-right: 10px #F33;}
'
            ],
            'borderright_style_color_no_var' => [
                'submittedValues' => [
                    'c4' => 'dotted',
                    'd4' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-right: dotted #F33;}
'
            ],
            'borderright_width_style_color_no_var' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px',
                    'c4' => 'dotted',
                    'd4' => '#F33'
                ],
                'expectedCss' => '.uni-node-abcd {border-right: 10px dotted #F33;}
'
            ],
            'borderright_global_value_no_var' => [
                'submittedValues' => [
                    'g4' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {border-right: inherit;}
'
            ],
            'borderright_value_with_var' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px',
                    'v4' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-right: var(--css-var,10px);}
'
            ],
            'borderright_global_value_with_var' => [
                'submittedValues' => [
                    'g4' => 'inherit',
                    'v4' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-right: var(--css-var,inherit);}
'
            ],
            'borderright_width_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px',
                    'v4' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-right: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'borderright_style_value_with_multiple_vars' => [
                'submittedValues' => [
                    'c4' => 'none',
                    'v4' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-right: var(--css-var-1,--css-var-2,--css-var-3,none);}
'
            ],
            'borderright_color_value_with_multiple_vars' => [
                'submittedValues' => [
                    'd4' => '#F33',
                    'v4' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-right: var(--css-var-1,--css-var-2,--css-var-3,#F33);}
'
            ],
            'borderright_all_values_with_multiple_vars' => [
                'submittedValues' => [
                    'a4' => 10,
                    'b4' => 'px',
                    'c4' => 'dotted',
                    'd4' => '#F33',
                    'v4' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-right: var(--css-var-1,--css-var-2,--css-var-3,10px dotted #F33);}
'
            ],
            'borderright_single_var' => [
                'submittedValues' => [
                    'v4' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-right: var(--css-var);}
'
            ],
            'borderright_multiple_vars' => [
                'submittedValues' => [
                    'v4' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-right: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'borderright_borderbottom_multiple_vars' => [
                'submittedValues' => [
                    'v2' => ['css-var-1', 'css-var-2', 'css-var-3'],
                    'v4' => ['css-var-4', 'css-var-5', 'css-var-6']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom: var(--css-var-1,--css-var-2,--css-var-3);border-right: var(--css-var-4,--css-var-5,--css-var-6);}
'
            ],
            'bordertop_borderright_value_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'a4' => 10,
                    'b4' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-top: 10px;border-right: 10px;}
'
            ],
            'bordertop_borderright_global_value_no_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'g4' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {border-top: inherit;border-right: inherit;}
'
            ],
            'bordertop_borderright_value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1'],
                    'g4' => 'inherit',
                    'v4' => ['css-var-2']
                ],
                'expectedCss' => '.uni-node-abcd {border-top: var(--css-var-1,10px);border-right: var(--css-var-2,inherit);}
'
            ],
            'bordertop_borderleft_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3'],
                    'g3' => 'inherit',
                    'v3' => ['css-var-4', 'css-var-5', 'css-var-6']
                ],
                'expectedCss' => '.uni-node-abcd {border-top: var(--css-var-1,--css-var-2,--css-var-3,10px);border-left: var(--css-var-4,--css-var-5,--css-var-6,inherit);}
'
            ],
            'bordertop_borderbottom_single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var-1'],
                    'v2' => ['css-var-2']
                ],
                'expectedCss' => '.uni-node-abcd {border-top: var(--css-var-1);border-bottom: var(--css-var-2);}
'
            ],
            'border_values_no_vars' => [
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
                'expectedCss' => '.uni-node-abcd {border-top: 10px;border-bottom: 10px;border-left: 10px;border-right: 10px;}
'
            ]
        ];
    }
}