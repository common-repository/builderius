<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingTextIndentTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'textIndent';

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
                'expectedCss' => '.uni-node-abcd {text-indent: 10px;}
'
            ],
            'global_value_no_var' => [
                'submittedValues' => [
                    'b1' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {text-indent: inherit;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {text-indent: var(--css-var,10px);}
'
            ],
            'global_value_with_var' => [
                'submittedValues' => [
                    'b1' => 'initial',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {text-indent: var(--css-var,initial);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {text-indent: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'global_value_with_multiple_vars' => [
                'submittedValues' => [
                    'b1' => 'revert',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {text-indent: var(--css-var-1,--css-var-2,--css-var-3,revert);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {text-indent: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {text-indent: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}