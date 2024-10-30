<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Provider;

class AvailableCategoriesProvider extends AbstractApplyRuleArgumentsProvider
{
    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        $results = [];
        foreach (get_categories(['hide_empty' => false]) as $category) {
            $results[] = ['value' => $category->term_id, 'title' => $category->name];
        }

        return  $this->sort($results);
    }
}
