<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingAlignItemsTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'alignItems';

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
                'expectedCss' => '.uni-node-abcd {-ms-flex-align: start;align-items: flex-start;}
'
            ],
            'flex_end_no_var' => [
                'submittedValues' => [
                    'a1' => 'flex-end'
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-align: end;align-items: flex-end;}
'
            ],
            'flex_start_with_var' => [
                'submittedValues' => [
                    'a1' => 'flex-start',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-align: var(--css-var,start);align-items: var(--css-var,flex-start);}
'
            ],
            'flex_start_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'flex-start',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-align: var(--css-var-1,--css-var-2,--css-var-3,start);align-items: var(--css-var-1,--css-var-2,--css-var-3,flex-start);}
'
            ],
            'flex_end_with_var' => [
                'submittedValues' => [
                    'a1' => 'flex-end',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-align: var(--css-var,end);align-items: var(--css-var,flex-end);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-align: var(--css-var);align-items: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-align: var(--css-var-1,--css-var-2,--css-var-3);align-items: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}