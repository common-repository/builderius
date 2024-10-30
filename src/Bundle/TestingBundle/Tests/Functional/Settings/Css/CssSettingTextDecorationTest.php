<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingTextDecorationTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'textDecoration';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'none_no_var' => [
                'submittedValues' => [
                    'a1' => 'none'
                ],
                'expectedCss' => '.uni-node-abcd {text-decoration: none;}
'
            ],
            'underline_with_var' => [
                'submittedValues' => [
                    'a1' => 'underline',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {text-decoration: var(--css-var,underline);}
'
            ],
            'underline_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'underline',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {text-decoration: var(--css-var-1,--css-var-2,--css-var-3,underline);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {text-decoration: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {text-decoration: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}