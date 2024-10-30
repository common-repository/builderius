<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Provider;

abstract class AbstractApplyRuleArgumentsProvider implements ApplyRuleArgumentsProviderInterface
{
    /**
     * @param array $results
     */
    protected function sort (array $results)
    {
        usort ($results, function ($a, $b) {
            if ($a['value'] < $b['value']) {
                return -1;
            } elseif ($a['value'] > $b['value']) {
                return 1;
            }

            return 0;
        });

        return $results;
    }
}