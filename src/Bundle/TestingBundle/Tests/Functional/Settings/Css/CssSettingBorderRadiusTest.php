<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingBorderRadiusTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'borderRadius';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'bordertopleft_one_value_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: 10px;}
'
            ],
            'bordertopleft_two_same_values_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'c1' => 10,
                    'd1' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: 10px;}
'
            ],
            'bordertopleft_two_different_values_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'c1' => 20,
                    'd1' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: 10px 20px;}
'
            ],
            'bordertopleft_value_globalvalue_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'c1' => 10,
                    'd1' => 'px',
                    'g1' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: inherit;}
'
            ],
            'bordertopleft_value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: var(--css-var,10px);}
'
            ],
            'bordertopleft_global_value_with_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: var(--css-var,inherit);}
'
            ],
            'bordertopleft_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'bordertopleft_single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: var(--css-var);}
'
            ],
            'bordertopleft_multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'bordertopright_one_value_no_var' => [
                'submittedValues' => [
                    'a2' => 20,
                    'b2' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-top-right-radius: 20px;}
'
            ],
            'bordertopright_two_same_values_no_var' => [
                'submittedValues' => [
                    'a2' => 20,
                    'b2' => 'px',
                    'c2' => 20,
                    'd2' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-top-right-radius: 20px;}
'
            ],
            'bordertopright_two_different_values_no_var' => [
                'submittedValues' => [
                    'a2' => 20,
                    'b2' => 'px',
                    'c2' => 5,
                    'd2' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-top-right-radius: 20px 5px;}
'
            ],
            'bordertopright_value_globalvalue_no_var' => [
                'submittedValues' => [
                    'a2' => 20,
                    'b2' => 'px',
                    'c2' => 20,
                    'd2' => 'px',
                    'g2' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {border-top-right-radius: inherit;}
'
            ],
            'bordertopright_value_with_var' => [
                'submittedValues' => [
                    'a2' => 20,
                    'b2' => 'px',
                    'v2' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-top-right-radius: var(--css-var,20px);}
'
            ],
            'bordertopright_global_value_with_var' => [
                'submittedValues' => [
                    'g2' => 'inherit',
                    'v2' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-top-right-radius: var(--css-var,inherit);}
'
            ],
            'bordertopright_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a2' => 20,
                    'b2' => 'px',
                    'v2' => ['css-var-2', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-top-right-radius: var(--css-var-2,--css-var-2,--css-var-3,20px);}
'
            ],
            'bordertopright_single_var' => [
                'submittedValues' => [
                    'v2' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-top-right-radius: var(--css-var);}
'
            ],
            'bordertopright_multiple_vars' => [
                'submittedValues' => [
                    'v2' => ['css-var-2', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-top-right-radius: var(--css-var-2,--css-var-2,--css-var-3);}
'
            ],
            'borderbottomleft_one_value_no_var' => [
                'submittedValues' => [
                    'a3' => 30,
                    'b3' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-left-radius: 30px;}
'
            ],
            'borderbottomleft_two_same_values_no_var' => [
                'submittedValues' => [
                    'a3' => 30,
                    'b3' => 'px',
                    'c3' => 30,
                    'd3' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-left-radius: 30px;}
'
            ],
            'borderbottomleft_two_different_values_no_var' => [
                'submittedValues' => [
                    'a3' => 30,
                    'b3' => 'px',
                    'c3' => 20,
                    'd3' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-left-radius: 30px 20px;}
'
            ],
            'borderbottomleft_value_globalvalue_no_var' => [
                'submittedValues' => [
                    'a3' => 30,
                    'b3' => 'px',
                    'c3' => 30,
                    'd3' => 'px',
                    'g3' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-left-radius: inherit;}
'
            ],
            'borderbottomleft_value_with_var' => [
                'submittedValues' => [
                    'a3' => 30,
                    'b3' => 'px',
                    'v3' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-left-radius: var(--css-var,30px);}
'
            ],
            'borderbottomleft_global_value_with_var' => [
                'submittedValues' => [
                    'g3' => 'inherit',
                    'v3' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-left-radius: var(--css-var,inherit);}
'
            ],
            'borderbottomleft_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a3' => 30,
                    'b3' => 'px',
                    'v3' => ['css-var-3', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-left-radius: var(--css-var-3,--css-var-2,--css-var-3,30px);}
'
            ],
            'borderbottomleft_single_var' => [
                'submittedValues' => [
                    'v3' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-left-radius: var(--css-var);}
'
            ],
            'borderbottomleft_multiple_vars' => [
                'submittedValues' => [
                    'v3' => ['css-var-3', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-left-radius: var(--css-var-3,--css-var-2,--css-var-3);}
'
            ],
            'borderbottomright_one_value_no_var' => [
                'submittedValues' => [
                    'a4' => 40,
                    'b4' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-right-radius: 40px;}
'
            ],
            'borderbottomright_two_same_values_no_var' => [
                'submittedValues' => [
                    'a4' => 40,
                    'b4' => 'px',
                    'c4' => 40,
                    'd4' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-right-radius: 40px;}
'
            ],
            'borderbottomright_two_different_values_no_var' => [
                'submittedValues' => [
                    'a4' => 40,
                    'b4' => 'px',
                    'c4' => 5,
                    'd4' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-right-radius: 40px 5px;}
'
            ],
            'borderbottomright_value_globalvalue_no_var' => [
                'submittedValues' => [
                    'a4' => 40,
                    'b4' => 'px',
                    'c4' => 40,
                    'd4' => 'px',
                    'g4' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-right-radius: inherit;}
'
            ],
            'borderbottomright_value_with_var' => [
                'submittedValues' => [
                    'a4' => 40,
                    'b4' => 'px',
                    'v4' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-right-radius: var(--css-var,40px);}
'
            ],
            'borderbottomright_global_value_with_var' => [
                'submittedValues' => [
                    'g4' => 'inherit',
                    'v4' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-right-radius: var(--css-var,inherit);}
'
            ],
            'borderbottomright_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a4' => 40,
                    'b4' => 'px',
                    'v4' => ['css-var-4', 'css-var-4', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-right-radius: var(--css-var-4,--css-var-4,--css-var-3,40px);}
'
            ],
            'borderbottomright_single_var' => [
                'submittedValues' => [
                    'v4' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-right-radius: var(--css-var);}
'
            ],
            'borderbottomright_multiple_vars' => [
                'submittedValues' => [
                    'v4' => ['css-var-4', 'css-var-4', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {border-bottom-right-radius: var(--css-var-4,--css-var-4,--css-var-3);}
'
            ],
            'all_values_no_var' => [
                'submittedValues' => [
                    'a1' => 30,
                    'b1' => 'px',
                    'c1' => 20,
                    'd1' => 'px',
                    'a2' => 10,
                    'b2' => 'px',
                    'c2' => 20,
                    'd2' => 'px',
                    'a3' => 30,
                    'b3' => 'px',
                    'c3' => 20,
                    'd3' => 'px',
                    'a4' => 40,
                    'b4' => 'px',
                    'c4' => 30,
                    'd4' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: 30px 20px;border-top-right-radius: 10px 20px;border-bottom-left-radius: 30px 20px;border-bottom-right-radius: 40px 30px;}
'
            ],
            'all_samevalues_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'c1' => 10,
                    'd1' => 'px',
                    'a2' => 20,
                    'b2' => 'px',
                    'c2' => 20,
                    'd2' => 'px',
                    'a3' => 30,
                    'b3' => 'px',
                    'c3' => 30,
                    'd3' => 'px',
                    'a4' => 40,
                    'b4' => 'px',
                    'c4' => 40,
                    'd4' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: 10px;border-top-right-radius: 20px;border-bottom-left-radius: 30px;border-bottom-right-radius: 40px;}
'
            ],
            'all_values_with global_no_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'a2' => 20,
                    'b2' => 'px',
                    'c2' => 20,
                    'd2' => 'px',
                    'g3' => 'initial',
                    'a4' => 40,
                    'b4' => 'px',
                    'c4' => 40,
                    'd4' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: inherit;border-top-right-radius: 20px;border-bottom-left-radius: initial;border-bottom-right-radius: 40px;}
'
            ],
            'all_values_with global_with_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'v1' => ['--css-var-1'],
                    'a2' => 10,
                    'b2' => 'px',
                    'c2' => 20,
                    'd2' => 'px',
                    'v2' => ['--css-var-2'],
                    'g3' => 'initial',
                    'v3' => ['--css-var-3'],
                    'a4' => 30,
                    'b4' => 'px',
                    'c4' => 40,
                    'd4' => 'px',
                    'v4' => ['--css-var-4'],
                ],
                'expectedCss' => '.uni-node-abcd {border-top-left-radius: var(--css-var-1,inherit);border-top-right-radius: var(--css-var-2,10px 20px);border-bottom-left-radius: var(--css-var-3,initial);border-bottom-right-radius: var(--css-var-4,30px 40px);}
'
            ],
        ];
    }
}