<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingDisplayTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'display';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'flex_no_var' => [
                'submittedValues' => [
                    'a1' => 'flex'
                ],
                'expectedCss' => '.uni-node-abcd {display: -webkit-box;display: -ms-flexbox;display: -webkit-flex;display: flex;}
'
            ],
            'inline_flex_no_var' => [
                'submittedValues' => [
                    'a1' => 'inline-flex'
                ],
                'expectedCss' => '.uni-node-abcd {display: -webkit-inline-box;display: -ms-inline-flexbox;display: -webkit-inline-flex;display: inline-flex;}
'
            ],
            'flex_with_var' => [
                'submittedValues' => [
                    'a1' => 'flex',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {display: var(--css-var,-webkit-box);display: var(--css-var,-ms-flexbox);display: var(--css-var,-webkit-flex);display: var(--css-var,flex);}
'
            ],
            'flex_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'flex',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {display: var(--css-var-1,--css-var-2,--css-var-3,-webkit-box);display: var(--css-var-1,--css-var-2,--css-var-3,-ms-flexbox);display: var(--css-var-1,--css-var-2,--css-var-3,-webkit-flex);display: var(--css-var-1,--css-var-2,--css-var-3,flex);}
'
            ],
            'inline_flex_with_var' => [
                'submittedValues' => [
                    'a1' => 'inline-flex',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {display: var(--css-var,-webkit-inline-box);display: var(--css-var,-ms-inline-flexbox);display: var(--css-var,-webkit-inline-flex);display: var(--css-var,inline-flex);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {display: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {display: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}