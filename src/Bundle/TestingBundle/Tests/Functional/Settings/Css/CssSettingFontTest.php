<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingFontTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'font';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'google_font_value_no_var' => [
                'submittedValues' => [
                    'a1' => 'google',
                    'b1' => 'sans-serif',
                    'c1' => 'ABeeZee',
                    'a2' => 'normal',
                    'a3' => 400,
                    'a4' => 'normal',
                ],
                'expectedCss' => '.builderiusContent .uni-node-abcd {font-family: ABeeZee, sans-serif;font-style: normal;font-weight: 400;}
'
            ],
            'common_font_value_no_var' => [
                'submittedValues' => [
                    'a1' => 'common',
                    'b1' => 'sans-serif',
                    'c1' => 'ABeeZee',
                    'a2' => 'normal',
                    'a3' => 400,
                    'a4' => 'normal',
                ],
                'expectedCss' => '.builderiusContent .uni-node-abcd {font-family: ABeeZee, sans-serif;font-style: normal;font-weight: 400;}
'
            ],
            'just_var' => [
                'submittedValues' => [
                    'a1' => null,
                    'a2' => null,
                    'a3' => null,
                    'a4' => null,
                    'a5' => null,
                    'a6' => null,
                    'b1' => null,
                    'b5' => null,
                    'b6' => null,
                    'c1' => null,
                    'v1' => null,
                    'v2' => null,
                    'v3' => null,
                    'v4' => null,
                    'v5' => ['--size-l'],
                    'v6' => null,
                ],
                'expectedCss' => '.builderiusContent .uni-node-abcd {font-size: var(--size-l);}
'
            ],
        ];
    }
}