<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingOverflowTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'overflow';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'clip_no_var' => [
                'submittedValues' => [
                    'a1' => 'clip'
                ],
                'expectedCss' => '.uni-node-abcd {overflow: clip;}
'
            ],
            'scroll_no_var' => [
                'submittedValues' => [
                    'a1' => 'scroll'
                ],
                'expectedCss' => '.uni-node-abcd {overflow: scroll;}
'
            ],
            'clip_with_var' => [
                'submittedValues' => [
                    'a1' => 'clip',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {overflow: var(--css-var,clip);}
'
            ],
            'clip_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'clip',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {overflow: var(--css-var-1,--css-var-2,--css-var-3,clip);}
'
            ],
            'scroll_with_var' => [
                'submittedValues' => [
                    'a1' => 'scroll',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {overflow: var(--css-var,scroll);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {overflow: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {overflow: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}