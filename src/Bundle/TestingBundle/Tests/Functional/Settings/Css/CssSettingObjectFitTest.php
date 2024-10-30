<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingObjectFitTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'objectFit';
    const MODULE_NAME = 'Image';

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
                    'a1' => 'fill'
                ],
                'expectedCss' => '.uni-node-abcd {object-fit: fill;}
'
            ],
            'value_with_var' => [
                'submittedValues' => [
                    'a1' => 'fill',
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {object-fit: var(--css-var,fill);}
'
            ],
            'value_with_multiple_vars' => [
                'submittedValues' => [
                    'a1' => 'fill',
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {object-fit: var(--css-var-1,--css-var-2,--css-var-3,fill);}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {object-fit: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {object-fit: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}