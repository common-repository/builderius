<?php

namespace Builderius\Bundle\BuilderBundle\Registration;

use Builderius\MooMoo\Platform\Bundle\MediaBundle\Registry\MimeTypesRegistryInterface;

class BuilderiusBuilderAllowedMimeTypesScriptLocalization extends AbstractBuilderiusBuilderScriptLocalization
{
    const PROPERTY_NAME = 'allowedMimeTypes';

    /**
     * @var MimeTypesRegistryInterface
     */
    private $mimeTypesRegistry;

    /**
     * @param MimeTypesRegistryInterface $mimeTypesRegistry
     */
    public function __construct(MimeTypesRegistryInterface $mimeTypesRegistry)
    {
        $this->mimeTypesRegistry = $mimeTypesRegistry;
    }

    /**
     * @inheritDoc
     */
    public function getPropertyData()
    {
        $data = [];
        foreach ($this->mimeTypesRegistry->getMimeTypes() as $mimeType) {
            $data[$mimeType->getExtension()] = $mimeType->getMimeType();
        }
        
        return $data;
    }
}
