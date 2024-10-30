<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingTransformOriginTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'transformOrigin';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'custom_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => '%',
                    'a2' => 10,
                    'b2' => '%'
                ],
                'expectedCss' => '.uni-node-abcd {transform-origin: 10% 10%;}
'
            ],
            'global_no_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => '%',
                    'a2' => 10,
                    'b2' => '%',
                    'g1' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {transform-origin: inherit;}
'
            ],
            'custom_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => '%',
                    'a2' => 10,
                    'b2' => '%',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {transform-origin: var(--css-var,10% 10%);}
'
            ],
            'global_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => '%',
                    'a2' => 10,
                    'b2' => '%',
                    'g1' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {transform-origin: var(--css-var,inherit);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {transform-origin: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {transform-origin: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}