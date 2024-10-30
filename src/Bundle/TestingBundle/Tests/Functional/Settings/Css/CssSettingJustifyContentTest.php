<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingJustifyContentTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'justifyContent';

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
                'expectedCss' => '.uni-node-abcd {-ms-flex-pack: start;justify-content: flex-start;}
'
            ],
            'flex_end_no_var' => [
                'submittedValues' => [
                    'a1' => 'flex-end'
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-pack: end;justify-content: flex-end;}
'
            ],
            'flex_start_with_var' => [
                'submittedValues' => [
                    'a1' => 'flex-start',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-pack: var(--css-var,start);justify-content: var(--css-var,flex-start);}
'
            ],
            'flex_start_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'flex-start',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-pack: var(--css-var-1,--css-var-2,--css-var-3,start);justify-content: var(--css-var-1,--css-var-2,--css-var-3,flex-start);}
'
            ],
            'flex_end_with_var' => [
                'submittedValues' => [
                    'a1' => 'flex-end',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-pack: var(--css-var,end);justify-content: var(--css-var,flex-end);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-pack: var(--css-var);justify-content: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', '--css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {-ms-flex-pack: var(--css-var-1,--css-var-2,--css-var-3);justify-content: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}