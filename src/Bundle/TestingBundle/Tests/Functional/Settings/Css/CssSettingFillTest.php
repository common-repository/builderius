<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingFillTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'fill';

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
                'expectedCss' => '.uni-node-abcd {fill: rgba(157, 81, 81, 1.00);}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 'rgba(157, 81, 81, 1.00)',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {fill: var(--css-var,rgba(157, 81, 81, 1.00));}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'rgba(157, 81, 81, 1.00)',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {fill: var(--css-var-1,--css-var-2,--css-var-3,rgba(157, 81, 81, 1.00));}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {fill: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {fill: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}