<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingTextAlignTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'textAlign';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'center_no_var' => [
                'submittedValues' => [
                    'a1' => 'center'
                ],
                'expectedCss' => '.uni-node-abcd {text-align: center;}
'
            ],
            'inherit_with_var' => [
                'submittedValues' => [
                    'a1' => 'inherit',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {text-align: var(--css-var,inherit);}
'
            ],
            'center_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'center',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {text-align: var(--css-var-1,--css-var-2,--css-var-3,center);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {text-align: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {text-align: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}