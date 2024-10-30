<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingLetterSpacingTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'letterSpacing';

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
                    'a1' => 10,
                    'b1' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {letter-spacing: 10px;}
'
            ],
            'global_value_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {letter-spacing: inherit;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {letter-spacing: var(--css-var,10px);}
'
            ],
            'global_value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'unset',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {letter-spacing: var(--css-var,unset);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {letter-spacing: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'global_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'initial',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {letter-spacing: var(--css-var-1,--css-var-2,--css-var-3,initial);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {letter-spacing: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {letter-spacing: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}