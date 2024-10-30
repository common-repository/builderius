<?php

namespace Builderius\Bundle\VCSBundle\Factory\BuilderiusVCSOwner\Chain\Element;

use Builderius\Bundle\VCSBundle\Factory\BuilderiusVCSOwner\BuilderiusVCSOwnerFromPostFactoryInterface;

abstract class AbstractBuilderiusVCSOwnerFromPostFactoryChainElement implements
    BuilderiusVCSOwnerFromPostFactoryInterface,
    BuilderiusVCSOwnerFromPostFactoryChainElementInterface
{
    /**
     * @var BuilderiusVCSOwnerFromPostFactoryChainElementInterface|null
     */
    private $successor;

    /**
     * @inheritDoc
     */
    public function getSuccessor()
    {
        return $this->successor;
    }

    /**
     * @inheritDoc
     */
    public function setSuccessor(BuilderiusVCSOwnerFromPostFactoryChainElementInterface $successor)
    {
        $this->successor = $successor;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function createOwner(\WP_Post $post)
    {
        if ($this->isApplicable($post)) {
            return $this->create($post);
        } elseif ($this->getSuccessor()) {
            return $this->getSuccessor()->createOwner($post);
        }

        return null;
    }
}