<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingVisibilityTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'visibility';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'visible_no_var' => [
                'submittedValues' => [
                    'a1' => 'visible'
                ],
                'expectedCss' => '.uni-node-abcd {visibility: visible;}
'
            ],
            'hidden_no_var' => [
                'submittedValues' => [
                    'a1' => 'hidden'
                ],
                'expectedCss' => '.uni-node-abcd {visibility: hidden;}
'
            ],
            'visible_with_var' => [
                'submittedValues' => [
                    'a1' => 'visible',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {visibility: var(--css-var,visible);}
'
            ],
            'visible_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'visible',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {visibility: var(--css-var-1,--css-var-2,--css-var-3,visible);}
'
            ],
            'hidden_with_var' => [
                'submittedValues' => [
                    'a1' => 'hidden',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {visibility: var(--css-var,hidden);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {visibility: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {visibility: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}