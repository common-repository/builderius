<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingAppearanceTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'appearance';

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
                    'a1' => 'meter'
                ],
                'expectedCss' => '.uni-node-abcd {appearance: meter;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 'meter',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {appearance: var(--css-var,meter);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'meter',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {appearance: var(--css-var-1,--css-var-2,--css-var-3,meter);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {appearance: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {appearance: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}