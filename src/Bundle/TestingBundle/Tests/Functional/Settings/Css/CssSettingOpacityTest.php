<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingOpacityTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'opacity';

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
                    'a1' => 0
                ],
                'expectedCss' => '.uni-node-abcd {opacity: 0;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 0.5,
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {opacity: var(--css-var,0.5);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 0.5,
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {opacity: var(--css-var-1,--css-var-2,--css-var-3,0.5);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {opacity: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {opacity: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}