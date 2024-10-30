<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingColorTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'color';

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
                    'a1' => 'rgba(157, 81, 81, 1.00)'
                ],
                'expectedCss' => '.uni-node-abcd {color: rgba(157, 81, 81, 1.00);}
'
            ],
            'currentcolor_no_var' => [
                'submittedValues' => [
                    'a1' => 'currentcolor'
                ],
                'expectedCss' => '.uni-node-abcd {color: currentcolor;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 'rgba(157, 81, 81, 1.00)',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {color: var(--css-var,rgba(157, 81, 81, 1.00));}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'rgba(157, 81, 81, 1.00)',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {color: var(--css-var-1,--css-var-2,--css-var-3,rgba(157, 81, 81, 1.00));}
'
            ],
            'currentcolor_with_var' => [
                'submittedValues' => [
                    'a1' => 'currentcolor',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {color: var(--css-var,currentcolor);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {color: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {color: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}