<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingFlexWrapTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'flexWrap';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'wrap_no_var' => [
                'submittedValues' => [
                    'a1' => 'wrap'
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-wrap: wrap;flex-wrap: wrap;}
'
            ],
            'nowrap_no_var' => [
                'submittedValues' => [
                    'a1' => 'nowrap'
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-wrap: nowrap;flex-wrap: nowrap;}
'
            ],
            'wrap_with_var' => [
                'submittedValues' => [
                    'a1' => 'wrap',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-wrap: var(--css-var,wrap);flex-wrap: var(--css-var,wrap);}
'
            ],
            'wrap_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'wrap',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-wrap: var(--css-var-1,--css-var-2,--css-var-3,wrap);flex-wrap: var(--css-var-1,--css-var-2,--css-var-3,wrap);}
'
            ],
            'nowrap_with_var' => [
                'submittedValues' => [
                    'a1' => 'nowrap',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-wrap: var(--css-var,nowrap);flex-wrap: var(--css-var,nowrap);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-wrap: var(--css-var);flex-wrap: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-wrap: var(--css-var-1,--css-var-2,--css-var-3);flex-wrap: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}