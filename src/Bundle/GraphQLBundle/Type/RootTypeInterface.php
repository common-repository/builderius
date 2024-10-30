<?php

namespace Builderius\Bundle\GraphQLBundle\Type;

use Builderius\Bundle\GraphQLBundle\Provider\RootData\BuilderiusGraphQLRootDataProviderInterface;

interface RootTypeInterface
{
    /**
     * @return bool
     */
    public function isAppliedToAllTemplateTypes();

    /**
     * @param bool $appliedToAll
     * @return $this
     */
    public function setAppliedToAllTemplateTypes($appliedToAll = false);

    /**
     * @return string
     */
    public function getTemplateType();

    /**
     * @param string $templateTypeName
     * @return $this
     */
    public function setTemplateType($templateTypeName);

    /**
     * @return BuilderiusGraphQLRootDataProviderInterface
     */
    public function getRootDataProvider();

    /**
     * @param BuilderiusGraphQLRootDataProviderInterface $rootDataProvider
     * @return $this;
     */
    public function setRootDataProvider(BuilderiusGraphQLRootDataProviderInterface $rootDataProvider);
}