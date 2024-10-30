<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Provider;

class AvailablePostStatusesProvider extends AbstractApplyRuleArgumentsProvider
{
    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        $results = [];

        foreach (get_post_stati([], 'objects') as $status) {
            if ($status->name !== 'auto-draft') {
                $results[] = ['value' => $status->name, 'title' => $status->label];
            }
        }

        return $this->sort($results);
    }
}
