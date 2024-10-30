<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\ExpressionLanguageBundle\ExpressionLanguage;
use Builderius\Bundle\ModuleBundle\Provider\BuilderiusModulesProviderInterface;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateModulesWithAssetsContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\TemplateBundle\Twig\TemplateDataVarsExtension;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\AssetsContainingEvent;

class ModulesAssetsRegistrationInPreviewModeEventListener extends AbstractModulesAssetsRegistrationEventListener
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
     * @param BuilderiusTemplateProviderInterface $builderiusTemplateProvider
     * @param BuilderiusModulesProviderInterface $modulesProvider
     * @param BuilderiusTemplateFromPostFactory $builderiusTemplateFromPostFactory
     * @param TemplateDataVarsExtension $twigExtension
     * @param ExpressionLanguage $expressionLanguage
     */
    public function __construct(
        BuilderiusTemplateProviderInterface $builderiusTemplateProvider,
        BuilderiusModulesProviderInterface $modulesProvider,
        BuilderiusTemplateFromPostFactory $builderiusTemplateFromPostFactory,
        TemplateDataVarsExtension $twigExtension,
        ExpressionLanguage $expressionLanguage
    ) {
        $this->builderiusTemplateProvider = $builderiusTemplateProvider;
        $this->modulesProvider = $modulesProvider;
        $this->builderiusTemplateFromPostFactory = $builderiusTemplateFromPostFactory;
        $this->twigExtension = $twigExtension;
        $this->expressionLanguage = $expressionLanguage;
    }


    public function beforeAssetsRegistration(AssetsContainingEvent $event)
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
                if ($modulesAssetsContent = $branch->getContent(BuilderiusTemplateModulesWithAssetsContentProvider::CONTENT_TYPE)) {
                    $config = $branch->getNotCommittedConfig();
                    $this->setConfigVersion($config['version']);
                    if (is_array($modulesAssetsContent) && !empty($modulesAssetsContent)) {
                        foreach ($modulesAssetsContent as $moduleName) {
                            $assets = $this->processModuleAssets($moduleName, $typeName, $technologyName, $assets);
                        }
                    }
                } elseif ($commit = $branch->getActiveCommit()) {
                    if ($modulesAssetsContent = $commit->getContent(BuilderiusTemplateModulesWithAssetsContentProvider::CONTENT_TYPE)) {
                        $config = $commit->getContentConfig();
                        $this->setConfigVersion($config['version']);
                        if (is_array($modulesAssetsContent) && !empty($modulesAssetsContent)) {
                            foreach ($modulesAssetsContent as $moduleName) {
                                $assets = $this->processModuleAssets($moduleName, $typeName, $technologyName, $assets);
                            }
                        }
                    }
                }
            }
        }
        $event->setAssets($this->processSsr($assets));
    }
}