<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingOrderTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'order';

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
                'expectedCss' => '.uni-node-abcd {-ms-flex-order: 10;order: 10;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-order: var(--css-var,10);order: var(--css-var,10);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-order: var(--css-var-1,--css-var-2,--css-var-3,10);order: var(--css-var-1,--css-var-2,--css-var-3,10);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-order: var(--css-var);order: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-order: var(--css-var-1,--css-var-2,--css-var-3);order: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}