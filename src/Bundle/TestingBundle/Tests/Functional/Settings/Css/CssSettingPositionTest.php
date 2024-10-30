<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingPositionTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'position';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'fixed_no_var' => [
                'submittedValues' => [
                    'a1' => 'fixed'
                ],
                'expectedCss' => '.uni-node-abcd {position: fixed;}
'
            ],
            'sticky_no_var' => [
                'submittedValues' => [
                    'a1' => 'sticky'
                ],
                'expectedCss' => '.uni-node-abcd {position: -webkit-sticky;position: sticky;}
'
            ],
            'fixed_with_var' => [
                'submittedValues' => [
                    'a1' => 'fixed',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {position: var(--css-var,fixed);}
'
            ],
            'fixed_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'fixed',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {position: var(--css-var-1,--css-var-2,--css-var-3,fixed);}
'
            ],
            'sticky_with_var' => [
                'submittedValues' => [
                    'a1' => 'sticky',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {position: var(--css-var,-webkit-sticky);position: var(--css-var,sticky);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {position: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {position: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}