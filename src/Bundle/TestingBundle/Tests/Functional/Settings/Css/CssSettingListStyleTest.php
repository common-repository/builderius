<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingListStyleTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'listStyle';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'notcustom_type_no_var' => [
                'submittedValues' => [
                    'a1' => 'circle'
                ],
                'expectedCss' => '.uni-node-abcd {list-style: circle;}
'
            ],
            'notcustom_position_no_var' => [
                'submittedValues' => [
                    'b1' => 'inside'
                ],
                'expectedCss' => '.uni-node-abcd {list-style: inside;}
'
            ],
            'notcustom_image_no_var' => [
                'submittedValues' => [
                    'c1' => 'http://test.com/img/shape.png'
                ],
                'expectedCss' => '.uni-node-abcd {list-style: url("http://test.com/img/shape.png");}
'
            ],
            'notcustom_type_position_image_no_var' => [
                'submittedValues' => [
                    'a1' => 'circle',
                    'b1' => 'inside',
                    'c1' => 'http://test.com/img/shape.png'
                ],
                'expectedCss' => '.uni-node-abcd {list-style: circle inside url("http://test.com/img/shape.png");}
'
            ],
            'notcustom_type_with_var' => [
                'submittedValues' => [
                    'a1' => 'circle',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {list-style: var(--css-var,circle);}
'
            ],
            'notcustom_position_with_var' => [
                'submittedValues' => [
                    'b1' => 'inside',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {list-style: var(--css-var,inside);}
'
            ],
            'notcustom_image_with_var' => [
                'submittedValues' => [
                    'c1' => 'http://test.com/img/shape.png',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {list-style: var(--css-var,url("http://test.com/img/shape.png"));}
'
            ],
            'notcustom_with_var' => [
                'submittedValues' => [
                    'a1' => 'circle',
                    'b1' => 'inside',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {list-style: var(--css-var,circle inside);}
'
            ],
            'notcustom_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'circle',
                    'b1' => 'inside',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {list-style: var(--css-var-1,--css-var-2,--css-var-3,circle inside);}
'
            ],
            'custom_no_var' => [
                'submittedValues' => [
                    'a1' => 'custom',
                    'b1' => 'inside'
                ],
                'expectedCss' => '.uni-node-abcd {list-style: inside;}
'
            ],
            'custom_with_var' => [
                'submittedValues' => [
                    'a1' => 'custom',
                    'b1' => 'inside',
                    'c1' => 'http://test.com/img/shape.png',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {list-style: var(--css-var,inside url("http://test.com/img/shape.png"));}
'
            ],
            'custom_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'custom',
                    'b1' => 'inside',
                    'c1' => 'http://test.com/img/shape.png',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {list-style: var(--css-var-1,--css-var-2,--css-var-3,inside url("http://test.com/img/shape.png"));}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {list-style: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {list-style: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}