<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingCursorTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'cursor';

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
                    'a1' => 'pointer'
                ],
                'expectedCss' => '.uni-node-abcd {cursor: pointer;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 'pointer',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {cursor: var(--css-var,pointer);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'pointer',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {cursor: var(--css-var-1,--css-var-2,--css-var-3,pointer);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {cursor: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {cursor: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}