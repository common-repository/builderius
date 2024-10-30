<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingClearTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'clear';

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
                'expectedCss' => '.uni-node-abcd {clear: left;}
'
            ],
            'inherit_no_var' => [
                'submittedValues' => [
                    'a1' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {clear: inherit;}
'
            ],
            'left_with_var' => [
                'submittedValues' => [
                    'a1' => 'left',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {clear: var(--css-var,left);}
'
            ],
            'left_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'left',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {clear: var(--css-var-1,--css-var-2,--css-var-3,left);}
'
            ],
            'inherit_with_var' => [
                'submittedValues' => [
                    'a1' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {clear: var(--css-var,inherit);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {clear: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {clear: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}