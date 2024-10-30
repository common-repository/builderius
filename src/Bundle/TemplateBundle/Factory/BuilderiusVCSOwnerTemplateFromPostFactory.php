<?php

namespace Builderius\Bundle\TemplateBundle\Factory;

use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\VCSBundle\Factory\BuilderiusVCSOwner\Chain\Element\AbstractBuilderiusVCSOwnerFromPostFactoryChainElement;

class BuilderiusVCSOwnerTemplateFromPostFactory extends AbstractBuilderiusVCSOwnerFromPostFactoryChainElement
{
    /**
     * @var BuilderiusTemplateFromPostFactory
     */
    private $templateFromPostFactory;

    /**
     * @param BuilderiusTemplateFromPostFactory $templateFromPostFactory
     */
    public function __construct(BuilderiusTemplateFromPostFactory $templateFromPostFactory)
    {
        $this->templateFromPostFactory = $templateFromPostFactory;
    }

    /**
     * @inheritDoc
     */
    public function isApplicable(\WP_Post $post)
    {
        return $post->post_type === BuilderiusTemplatePostType::POST_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function create(\WP_Post $post)
    {
        return $this->templateFromPostFactory->createTemplate($post);
    }
}