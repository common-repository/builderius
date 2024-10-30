<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

use Builderius\MooMoo\Platform\Bundle\PostBundle\Url\UrlGeneratorInterface;

class BuilderiusEditUrlScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'editUrl';

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
    
    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        return $this->urlGenerator->generate();
    }
}
