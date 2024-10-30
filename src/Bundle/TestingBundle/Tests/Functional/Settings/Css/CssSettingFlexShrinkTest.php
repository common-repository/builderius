<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingFlexShrinkTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'flexShrink';

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
                'expectedCss' => '.uni-node-abcd {-ms-flex-negative: 10;flex-shrink: 10;}
'
            ],
            'global_value_no_var' => [
                'submittedValues' => [
                    'a3' => 'inherit',
                    'v1' => []
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-negative: inherit;flex-shrink: inherit;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-negative: var(--css-var,10);flex-shrink: var(--css-var,10);}
'
            ],
            'global_value_with_var' => [
                'submittedValues' => [
                    'a3' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-negative: var(--css-var,inherit);flex-shrink: var(--css-var,inherit);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-negative: var(--css-var-1,--css-var-2,--css-var-3,10);flex-shrink: var(--css-var-1,--css-var-2,--css-var-3,10);}
'
            ],
            'global_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a3' => 'initial',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-negative: var(--css-var-1,--css-var-2,--css-var-3,initial);flex-shrink: var(--css-var-1,--css-var-2,--css-var-3,initial);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-negative: var(--css-var);flex-shrink: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-negative: var(--css-var-1,--css-var-2,--css-var-3);flex-shrink: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}