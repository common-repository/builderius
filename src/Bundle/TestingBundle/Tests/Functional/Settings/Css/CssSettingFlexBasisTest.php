<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingFlexBasisTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'flexBasis';

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
                    'a1' => '10px'
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-preferred-size: 10px;flex-basis: 10px;}
'
            ],
            'global_value_no_var' => [
                'submittedValues' => [
                    'a3' => 'inherit',
                    'v1' => []
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-preferred-size: inherit;flex-basis: inherit;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => '10px',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-preferred-size: var(--css-var,10px);flex-basis: var(--css-var,10px);}
'
            ],
            'global_value_with_var' => [
                'submittedValues' => [
                    'a3' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-preferred-size: var(--css-var,inherit);flex-basis: var(--css-var,inherit);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => '10px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-preferred-size: var(--css-var-1,--css-var-2,--css-var-3,10px);flex-basis: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'global_value_with_multiple_vars' => [
                'submittedValues' => [
                    'a3' => 'initial',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-preferred-size: var(--css-var-1,--css-var-2,--css-var-3,initial);flex-basis: var(--css-var-1,--css-var-2,--css-var-3,initial);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-preferred-size: var(--css-var);flex-basis: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-preferred-size: var(--css-var-1,--css-var-2,--css-var-3);flex-basis: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}