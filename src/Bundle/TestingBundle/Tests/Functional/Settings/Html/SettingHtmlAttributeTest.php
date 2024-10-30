<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Html;

class SettingHtmlAttributeTest extends AbstractHtmlSettingTest
{
    const SETTING_NAME = 'htmlAttribute';

    public function dataProvider()
    {
        return [
            'no_value' => [
                'moduleSettings' => [
                    [
                        'name' => 'htmlTagContainer',
                        'value' => [
                            'a1' => 'div'
                        ]
                    ]
                ],
                'templateSettings' => [],
                'expectedHtml' => '<div class="uni-node uni-BlockElement uni-node-abcd"></div>
',
            ],
            'name_no_value' => [
                'moduleSettings' => [
                    [
                        'name' => 'htmlTagContainer',
                        'value' => [
                            'a1' => 'div'
                        ]
                    ],
                    [
                        'name' => 'htmlAttribute',
                        'value' => [
                            'i1' => [
                                [
                                    'a1' => 'data-test',
                                ]
                            ]
                        ]
                    ]
                ],
                'templateSettings' => [],
                'expectedHtml' => '<div class="uni-node uni-BlockElement uni-node-abcd" data-test></div>
',
            ],
            'name_with_value' => [
                'moduleSettings' => [
                    [
                        'name' => 'htmlTagContainer',
                        'value' => [
                            'a1' => 'div'
                        ]
                    ],
                    [
                        'name' => 'htmlAttribute',
                        'value' => [
                            'i1' => [
                                [
                                    'a1' => 'data-test',
                                    'b1' => '123'
                                ]
                            ]
                        ]
                    ]
                ],
                'templateSettings' => [],
                'expectedHtml' => '<div class="uni-node uni-BlockElement uni-node-abcd" data-test="123"></div>
',
            ],
            'multiple_names_with_values' => [
                'moduleSettings' => [
                    [
                        'name' => 'htmlTagContainer',
                        'value' => [
                            'a1' => 'div'
                        ]
                    ],
                    [
                        'name' => 'htmlAttribute',
                        'value' => [
                            'i1' => [
                                [
                                    'a1' => 'data-test',
                                    'b1' => '123'
                                ],
                                [
                                    'a1' => 'aria-test',
                                    'b1' => 'hello'
                                ],
                                [
                                    'a1' => 'data-json',
                                    'b1' => '{ { "name": "name\'1", "title": "hel&lo1" }, { "name": "name\'2", "title": "hel&lo2" } }'
                                ]
                            ]
                        ]
                    ]
                ],
                'templateSettings' => [],
                'expectedHtml' => '<div class="uni-node uni-BlockElement uni-node-abcd" data-test="123" aria-test="hello" data-json="{ { &quot;name&quot;: &quot;name&#039;1&quot;, &quot;title&quot;: &quot;hel&amp;lo1&quot; }, { &quot;name&quot;: &quot;name&#039;2&quot;, &quot;title&quot;: &quot;hel&amp;lo2&quot; } }"></div>
',
            ],
        ];
    }
}