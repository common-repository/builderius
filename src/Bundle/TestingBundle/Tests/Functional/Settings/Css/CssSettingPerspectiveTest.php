<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingPerspectiveTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'perspective';

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
                    'a1' => 10,
                    'b1' => 'px'
                ],
                'expectedCss' => '.uni-node-abcd {perspective: 10px;}
'
            ],
            'inherit_no_var' => [
                'submittedValues' => [
                    'b1' => 'inherit'
                ],
                'expectedCss' => '.uni-node-abcd {perspective: inherit;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {perspective: var(--css-var,10px);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 10,
                    'b1' => 'px',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {perspective: var(--css-var-1,--css-var-2,--css-var-3,10px);}
'
            ],
            'inherit_with_var' => [
                'submittedValues' => [
                    'b1' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {perspective: var(--css-var,inherit);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {perspective: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {perspective: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}