<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingClipPathTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'clipPath';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'geometry_box_no_var' => [
                'submittedValues' => [
                    'a1' => 'border-box'
                ],
                'expectedCss' => '.uni-node-abcd {clip-path: border-box;}
'
            ],
            'shape_no_var' => [
                'submittedValues' => [
                    'a1' => 'polygon',
                    'a2' => '50% 0, 100% 50%, 50% 100%, 0 50%'
                ],
                'expectedCss' => '.uni-node-abcd {clip-path: polygon(50% 0, 100% 50%, 50% 100%, 0 50%);}
'
            ],
            'geometry_box_with_var' => [
                'submittedValues' => [
                    'a1' => 'border-box',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {clip-path: var(--css-var,border-box);}
'
            ],
            'geometry_box_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'border-box',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {clip-path: var(--css-var-1,--css-var-2,--css-var-3,border-box);}
'
            ],
            'shape_with_var' => [
                'submittedValues' => [
                    'a1' => 'polygon',
                    'a2' => '50% 0, 100% 50%, 50% 100%, 0 50%',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {clip-path: var(--css-var,polygon(50% 0, 100% 50%, 50% 100%, 0 50%));}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {clip-path: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {clip-path: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}