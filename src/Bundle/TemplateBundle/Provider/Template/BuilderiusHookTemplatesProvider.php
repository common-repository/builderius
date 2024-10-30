<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Template;

use Builderius\Bundle\TemplateBundle\ApplyRule\Checker\BuilderiusTemplateApplyRulesChecker;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Provider\TemplatePosts\BuilderiusTemplatePostsProviderInterface;

class BuilderiusHookTemplatesProvider implements BuilderiusTemplatesProviderInterface
{
    /**
     * @var BuilderiusTemplateFromPostFactory
     */
    protected $builderiusTemplateFromPostFactory;

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    protected $cache;

    /**
     * @var BuilderiusTemplateApplyRulesChecker
     */
    private $applyRuleChecker;

    /**
     * @var BuilderiusTemplatePostsProviderInterface
     */
    private $templatePostsProvider;

    /**
     * @param BuilderiusTemplateFromPostFactory $builderiusTemplateFromPostFactory
     * @param BuilderiusTemplateApplyRulesChecker $applyRuleChecker
     * @param BuilderiusTemplatePostsProviderInterface $templatePostsProvider
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(
        BuilderiusTemplateFromPostFactory $builderiusTemplateFromPostFactory,
        BuilderiusTemplateApplyRulesChecker $applyRuleChecker,
        BuilderiusTemplatePostsProviderInterface $templatePostsProvider,
        BuilderiusRuntimeObjectCache $cache
    ) {
        $this->builderiusTemplateFromPostFactory = $builderiusTemplateFromPostFactory;
        $this->cache = $cache;
        $this->applyRuleChecker = $applyRuleChecker;
        $this->templatePostsProvider = $templatePostsProvider;
    }

    /**
     * @inheritDoc
     */
    public function getTemplatePosts()
    {
        $applicableTemplatePosts = $this->cache->get( 'builderius_hook_template_posts' );
        if ( false === $applicableTemplatePosts ) {
            $applicableTemplatePosts = [];
            $templatePosts = $this->templatePostsProvider->getTemplatePosts('hook', true);
            foreach ($templatePosts as $templPost) {
                $applyRuleConfig = json_decode(
                    $templPost->__get(BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD),
                    true
                ) ? : [];
                if ($this->applyRuleChecker->checkApplyRule($applyRuleConfig)) {
                    $applicableTemplatePosts[] = $templPost;
                }
            }
            if (count($applicableTemplatePosts) > 1) {
                usort($applicableTemplatePosts, function (\WP_Post $a, \WP_Post $b) {
                    $aSortOrder = (int)$a->__get(BuilderiusTemplate::SORT_ORDER_FIELD);
                    $bSortOrder = (int)$b->__get(BuilderiusTemplate::SORT_ORDER_FIELD);
                    if ($aSortOrder < $bSortOrder) {
                        return -1;
                    } elseif ($aSortOrder > $bSortOrder) {
                        return 1;
                    } elseif ($a->ID < $b->ID) {
                        return -1;
                    } elseif ($a->ID > $b->ID) {
                        return 1;
                    } else {
                        return 0;
                    }
                });
            }

            $this->cache->set('builderius_hook_template_posts', $applicableTemplatePosts);
        }

        return $applicableTemplatePosts;
    }

    /**
     * @inheritDoc
     */
    public function getTemplates()
    {
        $templates = $this->cache->get('builderius_hook_templates');
        if (false === $templates) {
            $templates = [];
            $templatePosts = $this->getTemplatePosts();
            if (!empty($templatePosts)) {
                foreach ($templatePosts as $templatePost) {
                    $templates[] = $this->builderiusTemplateFromPostFactory
                        ->createTemplate($templatePost);
                }
            }
            $this->cache->set('builderius_hook_templates', $templates);
        }

        return $templates;
    }
}
