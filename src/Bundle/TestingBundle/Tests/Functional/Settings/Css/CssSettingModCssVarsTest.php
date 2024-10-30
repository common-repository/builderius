<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingModCssVarsTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'modCssVars';

    public function dataProvider()
    {
        return [
            'no_value' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'number_value' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'number',
                            'a2' => '--var-number',
                            'b2' => 10
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {--var-number: 10;}
'
            ],
            'percentage_value' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'percentage',
                            'a2' => '--var-number',
                            'b2' => 10,
                            'c2' => '%'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {--var-number: 10%;}
'
            ],
            'any_value' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'any-value',
                            'a2' => '--var-calc',
                            'b2' => 'calc(calc( 10px + 20px - var(--test-3-var) - attr(data-attr)) - 10px + var(--test2-var) - calc(20px - 10px) + 20px)',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {--var-calc: calc(calc( 10px + 20px - var(--test-3-var) - attr(data-attr)) - 10px + var(--test2-var) - calc(20px - 10px) + 20px);}
'
            ],
            'two_values' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'number',
                            'a2' => '--var-number',
                            'b2' => 10
                        ],
                        [
                            'a1' => 'length',
                            'a2' => '--var-length',
                            'b2' => 10,
                            'c2' => 'px'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {--var-number: 10;--var-length: 10px;}
'
            ],
        ];
    }
}