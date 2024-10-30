<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Template;

use Builderius\Bundle\TemplateBundle\ApplyRule\Checker\BuilderiusTemplateApplyRulesChecker;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Provider\TemplatePosts\BuilderiusTemplatePostsProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTypeTaxonomy;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Registry\ConditionsRegistryInterface;

class BuilderiusRegularTemplateProvider implements BuilderiusTemplateProviderInterface
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
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @var BuilderiusTemplatePostsProviderInterface
     */
    private $templatePostsProvider;

    /**
     * @var ConditionsRegistryInterface
     */
    private $conditionsRegistry;

    /**
     * @param BuilderiusTemplateFromPostFactory $builderiusTemplateFromPostFactory
     * @param BuilderiusTemplateApplyRulesChecker $applyRuleChecker
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     * @param BuilderiusTemplatePostsProviderInterface $templatePostsProvider
     * @param BuilderiusRuntimeObjectCache $cache
     * @param ConditionsRegistryInterface $conditionsRegistry
     */
    public function __construct(
        BuilderiusTemplateFromPostFactory $builderiusTemplateFromPostFactory,
        BuilderiusTemplateApplyRulesChecker $applyRuleChecker,
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider,
        BuilderiusTemplatePostsProviderInterface $templatePostsProvider,
        BuilderiusRuntimeObjectCache $cache,
        ConditionsRegistryInterface $conditionsRegistry
    ) {
        $this->builderiusTemplateFromPostFactory = $builderiusTemplateFromPostFactory;
        $this->cache = $cache;
        $this->applyRuleChecker = $applyRuleChecker;
        $this->templateTypesProvider = $templateTypesProvider;
        $this->templatePostsProvider = $templatePostsProvider;
        $this->conditionsRegistry = $conditionsRegistry;
    }

    /**
     * @inheritDoc
     */
    public function getTemplatePost()
    {
        $templatePost = $this->cache->get( 'builderius_template_post' );
        if ( false === $templatePost ) {
            $types = [];
            foreach ($this->templateTypesProvider->getTypes() as $type) {
                if ($type->isStandalone()) {
                    $types[] = $type->getName();
                }
            }
            $technologies = [];
            foreach ($this->templateTypesProvider->getTechnologies() as $technology) {
                $technologies[] = $technology->getName();
            }
            /** @var \WP_Post $post */
            $post = get_post();
            if (null === $post && isset($_GET['builderius'])) {
                wp();
                $post = get_post();
            }
            if ($post && $post->post_type === BuilderiusTemplatePostType::POST_TYPE && !is_404()) {
                $typeTerms = get_the_terms($post->ID, BuilderiusTemplateTypeTaxonomy::NAME);
                $type = !empty($typeTerms) ? reset($typeTerms)->slug : null;
                $technologyTerms = get_the_terms($post->ID, BuilderiusTemplateTechnologyTaxonomy::NAME);
                $technology = !empty($technologyTerms) ? reset($technologyTerms)->slug : null;
                $isBuilderMode = $this->conditionsRegistry->getCondition('is_builderius_builder_mode')->evaluate();

                if (in_array($type, $types) && in_array($technology, $technologies) && $isBuilderMode) {
                    $templatePost = $post;
                }
            } else {
                $applicableTemplatePosts = [];
                $templatePosts = $this->templatePostsProvider->getTemplatePosts('regular', true);
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
                $templatePost = reset($applicableTemplatePosts);
            }
            if ( false !== $templatePost ) {
                $this->cache->set('builderius_template_post', $templatePost);
                $this->cache->set(sprintf('builderius_template_post_%s', $templatePost->ID), $templatePost);
            } else {
                $this->cache->set('builderius_template_post', null);
            }
        }

        return $templatePost;
    }

    /**
     * @inheritDoc
     */
    public function getTemplate()
    {
        $template = $this->cache->get('builderius_template');
        if (false === $template) {
            $template = null;
            if ($templatePost = $this->getTemplatePost()) {
                $template = $this->builderiusTemplateFromPostFactory
                    ->createTemplate($templatePost);
            }
            $this->cache->set('builderius_template', $template);
        }

        return $template;
    }
}
