<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingUserSelectTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'userSelect';

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
                    'a1' => 'contain'
                ],
                'expectedCss' => '.uni-node-abcd {user-select: contain;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 'contain',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {user-select: var(--css-var,contain);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'contain',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {user-select: var(--css-var-1,--css-var-2,--css-var-3,contain);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {user-select: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {user-select: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}