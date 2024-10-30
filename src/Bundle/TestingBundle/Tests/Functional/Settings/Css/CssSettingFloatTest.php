<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingFloatTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'float';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'left_no_var' => [
                'submittedValues' => [
                    'a1' => 'left'
                ],
                'expectedCss' => '.uni-node-abcd {float: left;}
'
            ],
            'inherit_no_var' => [
                'submittedValues' => [
                    'a1' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {float: inherit;}
'
            ],
            'left_with_var' => [
                'submittedValues' => [
                    'a1' => 'left',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {float: var(--css-var,left);}
'
            ],
            'left_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'left',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {float: var(--css-var-1,--css-var-2,--css-var-3,left);}
'
            ],
            'inherit_with_var' => [
                'submittedValues' => [
                    'a1' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {float: var(--css-var,inherit);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {float: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {float: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}