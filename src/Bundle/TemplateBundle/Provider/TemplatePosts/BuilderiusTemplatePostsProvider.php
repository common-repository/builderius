<?php

namespace Builderius\Bundle\TemplateBundle\Provider\TemplatePosts;

use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplate;
use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateSubTypeTaxonomy;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTechnologyTaxonomy;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplateTypeTaxonomy;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\Provider\PluginsVersionsProvider;

class BuilderiusTemplatePostsProvider implements BuilderiusTemplatePostsProviderInterface
{
    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @var BuilderiusTemplateTypesProviderInterface
     */
    private $templateTypesProvider;

    /**
     * @var PluginsVersionsProvider
     */
    private $pluginsVersionsProvider;

    /**
     * @param \WP_Query $wpQuery
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     * @param PluginsVersionsProvider $pluginsVersionsProvider
     */
    public function __construct(
        \WP_Query $wpQuery,
        BuilderiusTemplateTypesProviderInterface $templateTypesProvider,
        PluginsVersionsProvider $pluginsVersionsProvider
    ) {
        $this->wpQuery = $wpQuery;
        $this->templateTypesProvider = $templateTypesProvider;
        $this->pluginsVersionsProvider = $pluginsVersionsProvider;
    }
    /**
     * @inheritDoc
     */
    public function getTemplatePosts($subType = null, $publishedOnly = false)
    {
        $technologies = [];
        foreach ($this->templateTypesProvider->getTechnologies() as $technology) {
            $technologies[] = $technology->getName();
        }
        $queryParams = [
            'post_type' => BuilderiusTemplatePostType::POST_TYPE,
            'post_status' => $publishedOnly ? ['publish', 'future'] : get_post_stati(),
            'posts_per_page' => -1,
            'no_found_rows' => true,
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => BuilderiusTemplateTypeTaxonomy::NAME,
                    'field' => 'slug',
                    'terms' => ['template'],
                    'operator' => 'IN'
                ],
                [
                    'taxonomy' => BuilderiusTemplateTechnologyTaxonomy::NAME,
                    'field' => 'slug',
                    'terms' => $technologies,
                    'operator' => 'IN'
                ]
            ]
        ];
        if (null !== $subType) {
            $queryParams['tax_query'][] = [
                'taxonomy' => BuilderiusTemplateSubTypeTaxonomy::NAME,
                'field' => 'slug',
                'terms' => [$subType],
                'operator' => 'IN'
            ];
        }
        $templatePosts = $this->wpQuery->query($queryParams);
        $data = [];
        foreach ($templatePosts as $templatePost) {
            $applyRulesConfig = json_decode(
                get_post_meta($templatePost->ID, BuilderiusTemplate::APPLY_RULES_CONFIG_FIELD, true),
                true
            );
            if (isset($applyRulesConfig['version'])) {
                $pluginsVersions = $this->getPluginsVersions();
                if ($applyRulesConfig['version'] === $pluginsVersions) {
                    $data[] = $templatePost;
                } else {
                    $allMatched = true;
                    foreach ($applyRulesConfig['version'] as $name => $version) {
                        if (!isset($pluginsVersions[$name]) || version_compare($pluginsVersions[$name], $version) === -1) {
                            $allMatched = false;
                            break;
                        }
                    }
                    if ($allMatched === true) {
                        $data[] = $templatePost;
                    }
                }
            } else {
                $data[] = $templatePost;
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    private function getPluginsVersions()
    {
        $versions = [];
        foreach ($this->pluginsVersionsProvider->getPluginsVersions() as $name => $version) {
            if (strpos($name, '.php') === false) {
                $versions[$name] = $version;
            }
        }

        return $versions;
    }
}