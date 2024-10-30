<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Applicant\BuilderiusTemplateApplicant;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Provider\Applicant\BuilderiusTemplateChangeSetApplicantsProviderInterface;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTypeTaxonomy;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractAction;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Provider\PluginsVersionsProvider;

abstract class AbstractTemplateApplicantsCacheUpdateHook extends AbstractAction
{
    /**
     * @var array|null
     */
    private $applyRules;

    /**
     * @var \WP_Query
     */
    protected $wpQuery;

    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    protected $templateTypesProvider;

    /**
     * @var PluginsVersionsProvider
     */
    protected $pluginsVersionsProvider;

    /**
     * @var BuilderiusTemplateChangeSetApplicantsProviderInterface
     */
    protected $changeSetApplicantsProvider;

    /**
     * @param \WP_Query $wpQuery
     * @return $this
     */
    public function setWpQuery(\WP_Query $wpQuery)
    {
        $this->wpQuery = $wpQuery;

        return $this;
    }

    /**
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     * @return $this
     */
    public function setTemplateTypesProvider(BuilderiusTemplateTypesProviderInterface $templateTypesProvider)
    {
        $this->templateTypesProvider = $templateTypesProvider;

        return $this;
    }

    /**
     * @param PluginsVersionsProvider $pluginsVersionsProvider
     * @return $this
     */
    public function setPluginsVersionsProvider(PluginsVersionsProvider $pluginsVersionsProvider)
    {
        $this->pluginsVersionsProvider = $pluginsVersionsProvider;

        return $this;
    }

    /**
     * @param BuilderiusTemplateChangeSetApplicantsProviderInterface $changeSetApplicantsProvider
     * @return $this
     */
    public function setChangeSetApplicantsProvider(
        BuilderiusTemplateChangeSetApplicantsProviderInterface $changeSetApplicantsProvider
    ) {
        $this->changeSetApplicantsProvider = $changeSetApplicantsProvider;

        return $this;
    }

    /**
     * @return array
     */
    protected function getApplyRules()
    {
        if ($this->applyRules === null) {
            $types = [];
            foreach ($this->templateTypesProvider->getTypes() as $type) {
                $types[] = $type->getName();
            }
            $technologies = [];
            foreach ($this->templateTypesProvider->getTechnologies() as $technology) {
                $technologies[] = $technology->getName();
            }
            $templatePosts = $this->wpQuery->query([
                'post_type' => BuilderiusTemplatePostType::POST_TYPE,
                'post_status' => get_post_stati(),
                'posts_per_page' => -1,
                'no_found_rows' => true,
                'tax_query' => [
                    'relation' => 'AND',
                    [
                        'taxonomy' => BuilderiusTemplateTypeTaxonomy::NAME,
                        'field' => 'slug',
                        'terms' => $types,
                        'operator' => 'IN'
                    ],
                    [
                        'taxonomy' => BuilderiusTemplateTechnologyTaxonomy::NAME,
                        'field' => 'slug',
                        'terms' => $technologies,
                        'operator' => 'IN'
                    ]
                ]
            ]);
            $data = [];
            foreach ($templatePosts as $templatePost) {
                $applyRulesConfig = json_decode(get_post_meta($templatePost->ID, BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD, true), true);
                if (isset($applyRulesConfig['version'])) {
                    $pluginsVersions = $this->getPluginsVersions();
                    if ($applyRulesConfig['version'] === $pluginsVersions) {
                        $data[md5(serialize($applyRulesConfig))] = $applyRulesConfig;
                    } else {
                        $allMatched = true;
                        foreach ($applyRulesConfig['version'] as $name => $version) {
                            if (!isset($pluginsVersions[$name]) || $pluginsVersions[$name] !== $version) {
                                $allMatched = false;
                                break;
                            }
                        }
                        if ($allMatched === true) {
                            $data[md5(serialize($applyRulesConfig))] = $applyRulesConfig;
                        }
                    }
                } else {
                    $data[md5(serialize($applyRulesConfig))] = $applyRulesConfig;
                }
            }

            $this->applyRules = $data;
        }

        return $this->applyRules;
    }

    /**
     * @return array
     */
    protected function getPluginsVersions()
    {
        $versions = [];
        foreach ($this->pluginsVersionsProvider->getPluginsVersions() as $name => $version) {
            if (strpos($name, '.php') === false) {
                $versions[$name] = $version;
            }
        }

        return $versions;
    }

    /**
     * @param BuilderiusTemplateApplicant $a
     * @param BuilderiusTemplateApplicant $b
     * @return int
     */
    public static function sortApplicants(BuilderiusTemplateApplicant $a, BuilderiusTemplateApplicant $b)
    {
        $aLabel = $a->getLabel() !== null ? $a->getLabel() : $a->getGroupLabel();
        $bLabel = $b->getLabel() !== null ? $b->getLabel() : $b->getGroupLabel();
        if ($aLabel == $bLabel) {
            return 0;
        }

        return ($aLabel < $bLabel) ? -1 : 1;
    }
}