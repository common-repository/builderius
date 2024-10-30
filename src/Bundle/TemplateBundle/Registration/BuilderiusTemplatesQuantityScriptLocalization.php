<?php

namespace Builderius\Bundle\TemplateBundle\Registration;

use Builderius\Bundle\BuilderBundle\Registration\AbstractBuilderiusBuilderScriptLocalization;
use Builderius\Bundle\TemplateBundle\Provider\TemplatePosts\BuilderiusTemplatePostsProviderInterface;

class BuilderiusTemplatesQuantityScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'templatesQuantity';

    /**
     * @var BuilderiusTemplatePostsProviderInterface
     */
    private $templatePostsProvider;

    /**
     * @param BuilderiusTemplatePostsProviderInterface $templatePostsProvider
     */
    public function __construct(
        BuilderiusTemplatePostsProviderInterface $templatePostsProvider
    ) {
        $this->templatePostsProvider = $templatePostsProvider;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        return count($this->templatePostsProvider->getTemplatePosts());
    }
}
