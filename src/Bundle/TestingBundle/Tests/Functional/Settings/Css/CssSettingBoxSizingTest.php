<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingBoxSizingTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'boxSizing';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'border_box_no_var' => [
                'submittedValues' => [
                    'a1' => 'border-box'
                ],
                'expectedCss' => '.uni-node-abcd {box-sizing: border-box;}
'
            ],
            'content_box_no_var' => [
                'submittedValues' => [
                    'a1' => 'content-box'
                ],
                'expectedCss' => '.uni-node-abcd {box-sizing: content-box;}
'
            ],
            'border_box_with_var' => [
                'submittedValues' => [
                    'a1' => 'border-box',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {box-sizing: var(--css-var,border-box);}
'
            ],
            'border_box_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'border-box',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {box-sizing: var(--css-var-1,--css-var-2,--css-var-3,border-box);}
'
            ],
            'content_box_with_var' => [
                'submittedValues' => [
                    'a1' => 'content-box',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {box-sizing: var(--css-var,content-box);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {box-sizing: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {box-sizing: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}