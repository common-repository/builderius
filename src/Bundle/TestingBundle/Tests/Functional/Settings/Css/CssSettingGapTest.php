<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingGapTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'gap';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'single_value1_no_var' => [
                'submittedValues' => [
                    'a1' => 100,
                    'b1' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {gap: 100px;}
'
            ],
            'single_value2_no_var' => [
                'submittedValues' => [
                    'a2' => 10,
                    'b2' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {gap: 10px;}
'
            ],
            'double_value_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'a2' => 20,
                    'b2' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {gap: 10px 20px;}
'
            ],
            'inherit_no_var' => [
                'submittedValues' => [
                    'g1' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {gap: inherit;}
'
            ],
            'single_value_with_var' => [
                'submittedValues' => [
                    'a1' => 100,
                    'b1' => 'px',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {gap: var(--css-var,100px);}
'
            ],
            'double_value_with_var' => [
                'submittedValues' => [
                    'a1' => 100,
                    'b1' => 'px',
                    'a2' => 200,
                    'b2' => 'px',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {gap: var(--css-var,100px 200px);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 100,
                    'b1' => 'px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {gap: var(--css-var-1,--css-var-2,--css-var-3,100px);}
'
            ],
            'inherit_with_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {gap: var(--css-var,inherit);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {gap: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {gap: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}