<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Css;

use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateContentProviderInterface;
use Builderius\Bundle\TestingBundle\Tests\Functional\AbstractContainerAwareTestCase;

abstract class AbstractCssSettingTest extends AbstractContainerAwareTestCase
{
    const SETTING_NAME = null;
    const MODULE_NAME = 'BlockElement';

    /**
     * @dataProvider dataProvider
     * @param array $submittedValues
     * @param string $expectedCss
     */
    public function testSetting(array $submittedValues, string $expectedCss)
    {
        $config = [
            'template' => [
                'type' => 'template',
                'technology' => 'html'
            ],
            'indexes' => [
                'root' => ['uni-node-class']
            ],
            'modules' => [
                'uni-node-class' => [
                    'id' => 'abcd',
                    'name' => static::MODULE_NAME,
                    'label' => static::MODULE_NAME,
                    'parent' => '',
                    'settings' => [
                        [
                            'name' => static::SETTING_NAME,
                            'value' => [
                                'all' => [
                                    'original' => $submittedValues
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        /** @var BuilderiusTemplateContentProviderInterface $cssProvider */
        $cssProvider = $this->container->get('builderius_template.provider.template_content.css');
        $actualCss = $cssProvider->getContent('html', $config);
        self::assertEquals($expectedCss, $actualCss);
    }

    abstract public function dataProvider();
}