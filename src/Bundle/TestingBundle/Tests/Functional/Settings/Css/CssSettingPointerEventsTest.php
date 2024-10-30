<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingPointerEventsTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'pointerEvents';

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
                    'a1' => 'visiblePainted'
                ],
                'expectedCss' => '.uni-node-abcd {pointer-events: visiblePainted;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 'visiblePainted',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {pointer-events: var(--css-var,visiblePainted);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'visiblePainted',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {pointer-events: var(--css-var-1,--css-var-2,--css-var-3,visiblePainted);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {pointer-events: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {pointer-events: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}