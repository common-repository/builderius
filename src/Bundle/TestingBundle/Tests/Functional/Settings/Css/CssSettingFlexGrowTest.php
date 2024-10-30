<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingFlexGrowTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'flexGrow';

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
                'expectedCss' => '.uni-node-abcd {-ms-flex-positive: 10;flex-grow: 10;}
'
            ],
            'global_value_no_var' => [
                'submittedValues' => [
                    'a3' => 'inherit',
                    'v1' => []
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-positive: inherit;flex-grow: inherit;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-positive: var(--css-var,10);flex-grow: var(--css-var,10);}
'
            ],
            'global_value_with_var' => [
                'submittedValues' => [
                    'a3' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-positive: var(--css-var,inherit);flex-grow: var(--css-var,inherit);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-positive: var(--css-var-1,--css-var-2,--css-var-3,10);flex-grow: var(--css-var-1,--css-var-2,--css-var-3,10);}
'
            ],
            'global_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a3' => 'initial',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-positive: var(--css-var-1,--css-var-2,--css-var-3,initial);flex-grow: var(--css-var-1,--css-var-2,--css-var-3,initial);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-positive: var(--css-var);flex-grow: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-positive: var(--css-var-1,--css-var-2,--css-var-3);flex-grow: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}