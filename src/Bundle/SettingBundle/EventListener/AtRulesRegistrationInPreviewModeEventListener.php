<?php

namespace Builderius\Bundle\SettingBundle\EventListener;

use Builderius\Bundle\SettingBundle\Factory\BuilderiusGlobalSettingsSetFromPostFactory;
use Builderius\Bundle\SettingBundle\Registration\BuilderiusGlobalSettingsSetPostType;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Event\ConfigContainingEvent;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateAtRulesContentProvider;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplateProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\Template\BuilderiusTemplatesProviderInterface;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\EventDispatcher\ConditionAwareEventListener;

class AtRulesRegistrationInPreviewModeEventListener extends ConditionAwareEventListener
{
    /**
     * @var BuilderiusTemplateProviderInterface
     */
    private $builderiusTemplateProvider;

    /**
     * @var BuilderiusTemplatesProviderInterface
     */
    private $builderiusHookTemplatesProvider;

    /**
     * @var BuilderiusGlobalSettingsSetFromPostFactory
     */
    private $globalSettingsSetFromPostFactory;

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $builderiusCache;

    /**
     * @param BuilderiusTemplateProviderInterface $builderiusTemplateProvider
     * @param BuilderiusTemplatesProviderInterface $builderiusHookTemplatesProvider
     * @param BuilderiusGlobalSettingsSetFromPostFactory $globalSettingsSetFromPostFactory
     * @param \WP_Query $wpQuery
     * @param BuilderiusRuntimeObjectCache $builderiusCache
     */
    public function __construct(
        BuilderiusTemplateProviderInterface $builderiusTemplateProvider,
        BuilderiusTemplatesProviderInterface $builderiusHookTemplatesProvider,
        BuilderiusGlobalSettingsSetFromPostFactory $globalSettingsSetFromPostFactory,
        \WP_Query $wpQuery,
        BuilderiusRuntimeObjectCache $builderiusCache
    ) {
        $this->builderiusTemplateProvider = $builderiusTemplateProvider;
        $this->builderiusHookTemplatesProvider = $builderiusHookTemplatesProvider;
        $this->globalSettingsSetFromPostFactory = $globalSettingsSetFromPostFactory;
        $this->wpQuery = $wpQuery;
        $this->builderiusCache = $builderiusCache;
    }

    /**
     * @param ConfigContainingEvent $event
     * @throws \Exception
     */
    public function beforeAtRulesRegistration(ConfigContainingEvent $event)
    {
        $atRules = $event->getConfig();
        $template = $this->builderiusTemplateProvider->getTemplate();
        if (!$template) {
            $hookTemplates = $this->builderiusHookTemplatesProvider->getTemplates();
            if (!empty($hookTemplates)) {
                $template = reset($hookTemplates);
            }
        }
        if ($template) {
            $technologyName = $template->getTechnology();
            $posts = $this->builderiusCache->get(sprintf('builderius_gss_posts_%s', $technologyName));
            if (false == $posts) {
                $queryArgs = [
                    'post_type' => BuilderiusGlobalSettingsSetPostType::POST_TYPE,
                    'post_status' => get_post_stati(),
                    'name' => $technologyName,
                    'posts_per_page' => -1,
                    'no_found_rows' => true,
                    'orderby' => 'name',
                    'order' => 'ASC'
                ];
                $posts = $this->wpQuery->query($queryArgs);
                $this->builderiusCache->set(sprintf('builderius_gss_posts_%s', $technologyName), $posts);
            }
            foreach ($posts as $post) {
                $globalSettingsSet = $this->globalSettingsSetFromPostFactory->createGlobalSettingsSet($post);
                $branch = $globalSettingsSet->getActiveBranch();
                if ($branch) {
                    if ($atRulesContent = $branch->getContent(BuilderiusTemplateAtRulesContentProvider::CONTENT_TYPE)) {
                        $atRulesContent = is_array($atRulesContent) ? $atRulesContent : [];
                        foreach($atRulesContent as $atRuleContent) {
                            if (!in_array($atRuleContent[BuilderiusTemplateAtRulesContentProvider::URL], $atRules)) {
                                $atRules[] = $atRuleContent[BuilderiusTemplateAtRulesContentProvider::URL];
                            }
                        }
                    } else {
                        $commit = $branch->getActiveCommit();
                        if ($commit) {
                            if ($atRulesContent = $commit->getContent(BuilderiusTemplateAtRulesContentProvider::CONTENT_TYPE)) {
                                $atRulesContent = is_array($atRulesContent) ? $atRulesContent : [];
                                foreach($atRulesContent as $atRuleContent) {
                                    if (!in_array($atRuleContent[BuilderiusTemplateAtRulesContentProvider::URL], $atRules)) {
                                        $atRules[] = $atRuleContent[BuilderiusTemplateAtRulesContentProvider::URL];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $event->setConfig($atRules);
    }
}