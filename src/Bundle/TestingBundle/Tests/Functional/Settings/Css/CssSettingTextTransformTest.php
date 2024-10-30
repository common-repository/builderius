<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingTextTransformTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'textTransform';

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
                    'a1' => 'uppercase'
                ],
                'expectedCss' => '.uni-node-abcd {text-transform: uppercase;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 'uppercase',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {text-transform: var(--css-var,uppercase);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'uppercase',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {text-transform: var(--css-var-1,--css-var-2,--css-var-3,uppercase);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {text-transform: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {text-transform: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}