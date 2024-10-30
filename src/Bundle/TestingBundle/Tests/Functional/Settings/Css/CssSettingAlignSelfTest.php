<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingAlignSelfTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'alignSelf';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'flex_start_no_var' => [
                'submittedValues' => [
                    'a1' => 'flex-start'
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-item-align: start;align-self: flex-start;}
'
            ],
            'flex_end_no_var' => [
                'submittedValues' => [
                    'a1' => 'flex-end'
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-item-align: end;align-self: flex-end;}
'
            ],
            'flex_start_with_var' => [
                'submittedValues' => [
                    'a1' => 'flex-start',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-item-align: var(--css-var,start);align-self: var(--css-var,flex-start);}
'
            ],
            'flex_start_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'flex-start',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-item-align: var(--css-var-1,--css-var-2,--css-var-3,start);align-self: var(--css-var-1,--css-var-2,--css-var-3,flex-start);}
'
            ],
            'flex_end_with_var' => [
                'submittedValues' => [
                    'a1' => 'flex-end',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-item-align: var(--css-var,end);align-self: var(--css-var,flex-end);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-item-align: var(--css-var);align-self: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-item-align: var(--css-var-1,--css-var-2,--css-var-3);align-self: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}