<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingWhiteSpaceTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'whiteSpace';

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
                    'a1' => 'nowrap'
                ],
                'expectedCss' => '.uni-node-abcd {white-space: nowrap;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 'nowrap',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {white-space: var(--css-var,nowrap);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'nowrap',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {white-space: var(--css-var-1,--css-var-2,--css-var-3,nowrap);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {white-space: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {white-space: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}