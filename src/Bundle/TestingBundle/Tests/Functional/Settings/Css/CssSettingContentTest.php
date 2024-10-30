<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingContentTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'content';

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
                    'a1' => 'open-quote'
                ],
                'expectedCss' => '.uni-node-abcd {content: open-quote;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 'open-quote',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {content: var(--css-var,open-quote);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'open-quote',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {content: var(--css-var-1,--css-var-2,--css-var-3,open-quote);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {content: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {content: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}