<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Content\Template;

use Builderius\Bundle\TemplateBundle\Provider\TemplateType\BuilderiusTemplateTypesProviderInterface;

class BuilderiusTemplateCompositeContentProvider extends AbstractBuilderiusTemplateContentProvider
{
    /**
     * @var BuilderiusTemplateContentProviderInterface[]
     */
    private $providers = [];

    /**
     * @param BuilderiusTemplateTypesProviderInterface $templateTypesProvider
     */
    public function __construct(BuilderiusTemplateTypesProviderInterface $templateTypesProvider)
    {
        foreach ($templateTypesProvider->getTechnologies() as $technology) {
            $technologyName = $technology->getName();
            $this->technologies[$technologyName] = $technologyName;
        }
    }

    /**
     * @param BuilderiusTemplateContentProviderInterface $provider
     * @return $this
     */
    public function addProvider(BuilderiusTemplateContentProviderInterface $provider)
    {
        foreach ($provider->getTechnologies() as $technology) {
            $this->providers[$technology][$provider->getContentType()] = $provider;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContentType()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getContent($technology, array $contentConfig)
    {
        $content = [];
        if (in_array($technology, $this->technologies) && isset($this->providers[$technology])) {
            foreach ($this->providers[$technology] as $provider) {
                $content[$provider->getContentType()] = $provider->getContent($technology, $contentConfig);
            }
        }

        return $content;
    }
}