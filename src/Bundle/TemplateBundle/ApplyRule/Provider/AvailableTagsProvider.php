<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Provider;

class AvailableTagsProvider extends AbstractApplyRuleArgumentsProvider
{
    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        $results = [];
        /** @var \WP_Term $tag */
        foreach (get_tags(['hide_empty' => false]) as $tag) {
            $results[] = ['value' => $tag->term_id, 'title' => $tag->name];
        }

        return $this->sort($results);
    }
}
