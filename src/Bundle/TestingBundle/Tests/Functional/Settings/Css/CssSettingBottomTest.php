<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingBottomTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'bottom';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'value_no_var' => [
                'submittedValues' => [
                    'a1' => 10
                ],
                'expectedCss' => '.uni-node-abcd {bottom: 10;}
'
            ],
            'global_value_no_var' => [
                'submittedValues' => [
                    'b1' => 'inherit',
                    'v1' => []
                ],
                'expectedCss' => '.uni-node-abcd {bottom: inherit;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {bottom: var(--css-var,10);}
'
            ],
            'global_value_with_var' => [
                'submittedValues' => [
                    'b1' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {bottom: var(--css-var,inherit);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {bottom: var(--css-var-1,--css-var-2,--css-var-3,10);}
'
            ],
            'global_value_with_multiple_vars' => [
                'submittedValues' => [
                    'b1' => 'initial',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {bottom: var(--css-var-1,--css-var-2,--css-var-3,initial);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {bottom: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {bottom: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}