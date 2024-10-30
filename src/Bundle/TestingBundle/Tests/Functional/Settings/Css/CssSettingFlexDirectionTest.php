<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingFlexDirectionTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'flexDirection';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'row_no_var' => [
                'submittedValues' => [
                    'a1' => 'row'
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-direction: row;flex-direction: row;}
'
            ],
            'column_no_var' => [
                'submittedValues' => [
                    'a1' => 'column'
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-direction: column;flex-direction: column;}
'
            ],
            'row_with_var' => [
                'submittedValues' => [
                    'a1' => 'row',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-direction: var(--css-var,row);flex-direction: var(--css-var,row);}
'
            ],
            'row_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'row',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-direction: var(--css-var-1,--css-var-2,--css-var-3,row);flex-direction: var(--css-var-1,--css-var-2,--css-var-3,row);}
'
            ],
            'column_with_var' => [
                'submittedValues' => [
                    'a1' => 'column',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-direction: var(--css-var,column);flex-direction: var(--css-var,column);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-direction: var(--css-var);flex-direction: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-direction: var(--css-var-1,--css-var-2,--css-var-3);flex-direction: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}