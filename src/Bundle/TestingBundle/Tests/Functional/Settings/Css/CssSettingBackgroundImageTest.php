<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

class CssSettingBackgroundImageTest extends AbstractCssSettingTest
{
    const SETTING_NAME = 'backgroundImage';

    public function dataProvider()
    {
        return [
            'no_value_no_var' => [
                'submittedValues' => [
                ],
                'expectedCss' => ''
            ],
            'none_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'none'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: none;}
'
            ],
            'none_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'none',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: var(--css-var,none);}
'
            ],
            'image_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'http://test.com/img/shape.png',
                            'b1' => 'image'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: url("http://test.com/img/shape.png");}
'
            ],
            'image_no_var_missing_argument' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'image'
                        ]
                    ]
                ],
                'expectedCss' => ''
            ],
            'image_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'http://test.com/img/shape.png',
                            'b1' => 'image',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: var(--css-var,url("http://test.com/img/shape.png"));}
'
            ],
            'not_valid_image_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'image',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: var(--css-var);}
'
            ],
            'linear_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'linear-gradient',
                            'c1' => 0,
                            'd1' => 'rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: linear-gradient(0deg, rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%);}
'
            ],
            'linear_no_var_missing_argument' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'linear-gradient',
                            'd1' => 'rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%'
                        ]
                    ]
                ],
                'expectedCss' => ''
            ],
            'linear_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'linear-gradient',
                            'c1' => 0,
                            'd1' => 'rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: var(--css-var,linear-gradient(0deg, rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%));}
'
            ],
            'radial_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'radial-gradient',
                            'd1' => 'rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%',
                            'e1' => 'circle'
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: radial-gradient(circle, rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%);}
'
            ],
            'radial_no_var_missing_argument' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'radial-gradient',
                            'e1' => 'circle'
                        ]
                    ]
                ],
                'expectedCss' => ''
            ],
            'radial_with_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'radial-gradient',
                            'd1' => 'rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%',
                            'e1' => 'circle',
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: var(--css-var,radial-gradient(circle, rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%));}
'
            ],
            'global_value_with_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'i1' => [
                        [
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: inherit;}
'
            ],
            'one_single_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'v1' => ['css-var']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: var(--css-var);}
'
            ],
            'one_multiple_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: var(--css-var-1,--css-var-2,--css-var-3);}
'
            ],
            'two_values_no_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'http://test.com/img/shape.png',
                            'b1' => 'image',
                        ],
                        [
                            'b1' => 'radial-gradient',
                            'd1' => 'rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%',
                            'e1' => 'circle',
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: url("http://test.com/img/shape.png"),radial-gradient(circle, rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%);}
'
            ],
            'two_values_one_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'b1' => 'radial-gradient',
                            'd1' => 'rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%',
                            'e1' => 'circle',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'b1' => 'linear-gradient',
                            'c1' => 0,
                            'd1' => 'rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%',
                        ],
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: var(--css-var-1,radial-gradient(circle, rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%)),linear-gradient(0deg, rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%);}
'
            ],
            'two_values_two_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'a1' => 'http://test.com/img/shape.png',
                            'b1' => 'image',
                            'v1' => ['css-var-1']
                        ],
                        [
                            'b1' => 'linear-gradient',
                            'c1' => 0,
                            'd1' => 'rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%',
                            'v1' => ['css-var-2']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: var(--css-var-1,url("http://test.com/img/shape.png")),var(--css-var-2,linear-gradient(0deg, rgb(8, 80, 120) 1%, rgb(133, 216, 206) 99%));}
'
            ],
            'two_single_var' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'v1' => ['css-var-1']
                        ],
                        [
                            'v1' => ['css-var-2']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: var(--css-var-1),var(--css-var-2);}
'
            ],
            'global_with_two_single_var' => [
                'submittedValues' => [
                    'g1' => 'inherit',
                    'i1' => [
                        [
                            'v1' => ['css-var-1']
                        ],
                        [
                            'v1' => ['css-var-2']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: inherit;}
'
            ],
            'two_multiple_vars' => [
                'submittedValues' => [
                    'i1' => [
                        [
                            'v1' => ['css-var-1', 'css-var-2', 'css-var-3']
                        ],
                        [
                            'v1' => ['css-var-4', 'css-var-5', 'css-var-6']
                        ]
                    ]
                ],
                'expectedCss' => '.uni-node-abcd {background-image: var(--css-var-1,--css-var-2,--css-var-3),var(--css-var-4,--css-var-5,--css-var-6);}
'
            ],
        ];
    }
}