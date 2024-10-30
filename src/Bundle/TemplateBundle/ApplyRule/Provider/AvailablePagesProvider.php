<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Provider;

class AvailablePagesProvider extends AbstractApplyRuleArgumentsProvider
{
    const CACHE_KEY = 'builderius_available_pages_for_apply_rules';

    /**
     * @var string
     */
    private $field;

    /**
     * @param string $field
     */
    public function __construct(string $field = 'ID')
    {
        $this->field = $field;
    }

    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        $stat = get_post_stati();
        unset($stat['auto-draft']);

        $queryArguments = [
            'posts_per_page' => -1,
            'post_type' => 'page',
            'lang' => '',
            'post_status' => $stat
        ];
        $results = [];
        $postsPageId = get_option('page_for_posts');
        $showOnFront = get_option('show_on_front');
        $cachedPages = wp_cache_get(self::CACHE_KEY);
        if (false === $cachedPages) {
            $pages = get_pages($queryArguments);
        } else {
            $pages = $cachedPages;
        }
        $pagesByKeys = [];
        foreach ($pages as $page) {
            if (false === $cachedPages) {
                $pagesByKeys[$page->ID] = $page;
            }
            if ($showOnFront !== 'posts' && $postsPageId && $page->ID === (int)$postsPageId) {
                continue;
            } else {
                if ($this->field === 'post_title') {
                    $title = $page->post_title;
                } else {
                    $title = sprintf(
                        '%s%s (%s)',
                        $this->field === 'ID' ? '#' : '',
                        $page->{$this->field},
                        $page->post_title
                    );
                }
                $results[] = [
                    'value' => $page->{$this->field},
                    'title' => $title
                ];
            }
        }
        if (!empty($pagesByKeys)) {
            wp_cache_set(self::CACHE_KEY, $pagesByKeys);
        }
        return $this->sort($results);
    }
}
