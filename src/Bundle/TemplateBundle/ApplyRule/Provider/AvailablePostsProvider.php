<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Provider;

class AvailablePostsProvider extends AbstractApplyRuleArgumentsProvider
{
    const CACHE_KEY = 'builderius_available_posts_for_apply_rules';

    /**
     * @var string
     */
    private $field;

    /**
     * @var array
     */
    private $queryArguments = [
        'posts_per_page' => -1,
        'lang' => '',
    ];

    /**
     * @var \WP_Query
     */
    private $wpQuery;

    /**
     * @param array $queryArguments
     * @param string $field
     */
    public function __construct(array $queryArguments = [], $field = 'ID')
    {
        foreach ($queryArguments as $key => $value) {
            $this->queryArguments[$key] = $value;
        }
        $this->field = $field;
    }

    /**
     * @param array $queryArguments
     * @return $this
     */
    public function setQueryArguments(array $queryArguments = [])
    {
        foreach ($queryArguments as $key => $value) {
            $this->queryArguments[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function setField(string $field)
    {
        $this->field = $field;

        return $this;
    }

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
     * @inheritDoc
     */
    public function getArguments()
    {
        if (!isset($this->queryArguments['post_type'])) {
            throw new \Exception('Missing required "post_type" query argument');
        }
        $postType = $this->queryArguments['post_type'];
        $results = wp_cache_get(self::CACHE_KEY);
        if (false === $results ||
            !isset($results[$postType]) ||
            !isset($results[$postType][$this->field]) ||
            !isset($results[$postType][$this->field]['sorted']))
        {
            if(!is_array($results)) {
                $results = [];
            }
            $stat = get_post_stati();
            unset($stat['auto-draft']);

            $this->queryArguments['post_status'] = $stat;
            if (!isset($results[$postType]['objects'])) {
                $posts = $this->wpQuery->query($this->queryArguments);
            } else {
                $posts = $results[$postType]['objects'];
            }
            $postObjects = [];
            if (!empty($posts)) {
                foreach ($posts as $post) {
                    if (!isset($results[$postType]['objects'])) {
                        $postObjects[$post->ID] = $post;
                    }
                    if ($this->field === 'post_title') {
                        $title = $post->post_title;
                    } else {
                        $title = sprintf(
                            '%s%s (%s)',
                            $this->field === 'ID' ? '#' : '',
                            $post->{$this->field},
                            $post->post_title
                        );
                    }
                    $results[$postType][$this->field]['not_sorted'][$post->ID] = [
                        'value' => $post->{$this->field},
                        'title' => $title
                    ];
                    $results[$postType][$this->field]['sorted'][$post->ID] =
                        $results[$postType][$this->field]['not_sorted'][$post->ID];
                }
            } else {
                $postObjects = [];
                $results[$postType]['objects'] = [];
                $results[$postType][$this->field]['not_sorted'] = [];
                $results[$postType][$this->field]['sorted'] = [];
            }
            if (!isset($results[$postType]['objects']) && !empty($postObjects)) {
                $results[$postType]['objects'] = $postObjects;
            }
            $results[$postType][$this->field]['sorted'] = $this->sort($results[$postType][$this->field]['sorted']);
            wp_cache_set(self::CACHE_KEY, $results);
        }
        return $results[$postType][$this->field]['sorted'];
    }
}
