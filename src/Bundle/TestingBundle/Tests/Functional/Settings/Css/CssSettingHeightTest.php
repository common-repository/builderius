<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingHeightTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'height';

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
                    'a1' => 100,
                    'b1' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {height: 100px;}
'
            ],
            'inherit_no_var' => [
                'submittedValues' => [
                    'b1' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {height: inherit;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 100,
                    'b1' => 'px',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {height: var(--css-var,100px);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 100,
                    'b1' => 'px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {height: var(--css-var-1,--css-var-2,--css-var-3,100px);}
'
            ],
            'inherit_with_var' => [
                'submittedValues' => [
                    'b1' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {height: var(--css-var,inherit);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {height: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {height: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}