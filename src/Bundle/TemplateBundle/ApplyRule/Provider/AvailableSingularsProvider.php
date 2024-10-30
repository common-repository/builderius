<?php

namespace Builderius\Bundle\TemplateBundle\ApplyRule\Provider;

class AvailableSingularsProvider extends AbstractApplyRuleArgumentsProvider
{
    /**
     * @var ApplyRuleArgumentsProviderInterface
     */
    private $providers;

    public function addProvider(ApplyRuleArgumentsProviderInterface $provider) {
        $this->providers[] = $provider;
    }

    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        $result = [];
        foreach ($this->providers as $provider) {
            $result = array_merge($result, $provider->getArguments());
        }

        return $this->sort($result);
    }
}
