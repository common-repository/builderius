<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\Settings\Html;

use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateContentProviderInterface;
use Builderius\Bundle\TestingBundle\Tests\Functional\AbstractContainerAwareTestCase;
use Builderius\Symfony\Component\Templating\EngineInterface;

abstract class AbstractHtmlSettingTest extends AbstractContainerAwareTestCase
{
    const SETTING_NAME = null;
    const MODULE_NAME = 'BlockElement';

    /**
     * @var BuilderiusTemplateContentProviderInterface
     */
    protected $htmlProvider;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->htmlProvider = $this->container->get('builderius_template.provider.template_content.html');
        $this->templatingEngine = $this->container->get('templating');
    }


    /**
     * @dataProvider dataProvider
     * @param array $moduleSettings
     * @param array $templateSettings
     * @param string $expectedHtml
     */
    public function testSetting(array $moduleSettings, array $templateSettings, string $expectedHtml)
    {
        $config = [
            'template' => [
                'type' => 'template',
                'technology' => 'html',
                'settings' => $templateSettings
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
                    'settings' => $moduleSettings
                ]
            ]
        ];

        $actualHtml = $this->htmlProvider->getContent('html', $config);
        $finalHtml = do_blocks(do_shortcode($this->templatingEngine->render(
            'BuilderiusTemplateBundle:templateDynamicContent.twig',
            [
                'dynamicContent' => $actualHtml
            ]
        )));
        self::assertEquals($expectedHtml, $finalHtml);
    }

    abstract public function dataProvider();
}