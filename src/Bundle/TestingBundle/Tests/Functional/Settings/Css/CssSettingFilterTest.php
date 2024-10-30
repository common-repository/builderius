<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingFilterTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'filter';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'one_itemvalue_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'blur',
                            'a2' => '5px'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {filter: blur(5px);}
'
            ],
            'globalvalue_no_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'i1' => [
                        [
                            'a1' => 'blur',
                            'a2' => '5px'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {filter: inherit;}
'
            ],
            'one_itemvalue_with_var' => [
                'submittedValues' => [
                    'v1' => ['css-var'],
                    'i1' => [
                        [
                            'a1' => 'blur',
                            'a2' => '5px' ,
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {filter: var(--css-var,blur(5px));}
'
            ],
            'multiple_itemvalues_with_var' => [
                'submittedValues' => [
                    'v1' => ['css-var'],
                    'i1' => [
                        [
                            'a1' => 'blur',
                            'a2' => '5px' ,
                        ],
                        [
                            'a1' => 'grayscale',
                            'a2' => '80%' ,
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {filter: var(--css-var,blur(5px) grayscale(80%));}
'
            ],
            'one_itemvalue_with_multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3'],
                    'i1' => [
                        [
                            'a1' => 'blur',
                            'a2' => '5px' ,
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {filter: var(--css-var-1,--css-var-2,--css-var-3,blur(5px));}
'
            ],
            'single_var' => [
                'submittedValues' => [
                    'v1' => ['css-var']
                ],
                'expectedCss' => '.uni-node-abcd {filter: var(--css-var);}
'
            ],
            'multiple_vars' => [
                'submittedValues' => [
                    'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                ],
                'expectedCss' => '.uni-node-abcd {filter: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
        ];
    }
}