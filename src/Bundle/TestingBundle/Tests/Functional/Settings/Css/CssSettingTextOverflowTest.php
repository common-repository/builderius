<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingTextOverflowTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'textOverflow';

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
                'expectedCss' => '.uni-node-abcd {text-overflow: clip;}
'
            ],
            'ellipsis_no_var' => [
                'submittedValues' => [
                    'a1' => 'ellipsis'
                ],
                'expectedCss' => '.uni-node-abcd {text-overflow: ellipsis;}
'
            ],
            'clip_with_var' => [
                'submittedValues' => [
                    'a1' => 'clip',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {text-overflow: var(--css-var,clip);}
'
            ],
            'clip_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'clip',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {text-overflow: var(--css-var-1,--css-var-2,--css-var-3,clip);}
'
            ],
            'ellipsis_with_var' => [
                'submittedValues' => [
                    'a1' => 'ellipsis',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {text-overflow: var(--css-var,ellipsis);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {text-overflow: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {text-overflow: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}