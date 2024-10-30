<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateModulesWithInlineAssetsContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\TemplateBundle\Twig\TemplateDataVarsExtension;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\InlineAssetsContainingEvent;
use Builderius\Symfony\Component\Templating\EngineInterface;

class ModulesInlineAssetsRegistrationInPreviewModeEventListener extends AbstractModulesInlineAssetsRegistrationEventListener
{
    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $builderiusTemplateProvider;

    /**
     * @var BuilderiusTemplateFromPostFactory
     */
    private $builderiusTemplateFromPostFactory;

    /**
     * @var string
     */
    private $assetType;

    /**
     * @param BuilderiusTemplateProviderInterface $builderiusTemplateProvider
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @param BuilderiusTemplateFromPostFactory $builderiusTemplateFromPostFactory
     * @param TemplateDataVarsExtension $twigExtension
     * @param ExpressionLanguage $expressionLanguage
     * @param EngineInterface $templatingEngine
     * @param string $assetType
     */
    public function __construct(
        BuilderiusTemplateProviderInterface $builderiusTemplateProvider,
        BuilderiusModulesProviderInterface $modulesProvider,
        BuilderiusTemplateFromPostFactory $builderiusTemplateFromPostFactory,
        TemplateDataVarsExtension $twigExtension,
        ExpressionLanguage $expressionLanguage,
        EngineInterface $templatingEngine,
        $assetType
    ) {
        $this->builderiusTemplateProvider = $builderiusTemplateProvider;
        $this->modulesProvider = $modulesProvider;
        $this->builderiusTemplateFromPostFactory = $builderiusTemplateFromPostFactory;
        $this->twigExtension = $twigExtension;
        $this->expressionLanguage = $expressionLanguage;
        $this->templatingEngine = $templatingEngine;
        $this->assetType = $assetType;
    }

    /**
     * @param InlineAssetsContainingEvent $event
     * @throws \Exception
     */
    public function beforeAssetsRegistration(InlineAssetsContainingEvent $event)
    {
        $assets = $event->getAssets();
        /** @var \WP_Post $post */
        $post = get_post();
        if ($post && $post->post_type === BuilderiusTemplatePostType::POST_TYPE) {
            $template = $this->builderiusTemplateFromPostFactory->createTemplate($post);
        } else {
            $template = $this->builderiusTemplateProvider->getTemplate();
        }
        if ($template) {
            $typeName = $template->getType();
            $technologyName = $template->getTechnology();
            if ($branch = $template->getActiveBranch()) {
                if ($modulesAssetsContent = $branch->getContent(BuilderiusTemplateModulesWithInlineAssetsContentProvider::CONTENT_TYPE)) {
                    $config = $branch->getNotCommittedConfig();
                    $this->setConfigVersion($config['version']);
                    if (is_array($modulesAssetsContent) && !empty($modulesAssetsContent)) {
                        foreach ($modulesAssetsContent as $moduleName) {
                            $assets = $this->processModuleAssets($moduleName, $typeName, $technologyName, $this->assetType, $assets);
                        }
                    }
                } elseif ($commit = $branch->getActiveCommit()) {
                    if ($modulesAssetsContent = $commit->getContent(BuilderiusTemplateModulesWithInlineAssetsContentProvider::CONTENT_TYPE)) {
                        $config = $commit->getContentConfig();
                        $this->setConfigVersion($config['version']);
                        if (is_array($modulesAssetsContent) && !empty($modulesAssetsContent)) {
                            foreach ($modulesAssetsContent as $moduleName) {
                                $assets = $this->processModuleAssets($moduleName, $typeName, $technologyName, $this->assetType, $assets);
                            }
                        }
                    }
                }
            }
        }
        $event->setAssets($assets);
    }
}