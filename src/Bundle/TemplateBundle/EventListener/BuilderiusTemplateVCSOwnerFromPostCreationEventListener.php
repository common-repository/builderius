<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Factory\BuilderiusTemplateFromPostFactory;
use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateInterface;
use Builderius\Bundle\TemplateBundle\Registration\BuilderiusTemplatePostType;
use Builderius\Bundle\VCSBundle\Event\BuilderiusVCSOwnerFromPostCreationEvent;

class BuilderiusTemplateVCSOwnerFromPostCreationEventListener
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

    public function createOwner(BuilderiusVCSOwnerFromPostCreationEvent $event)
    {
        $post = $event->getPost();
        if ($post instanceof \WP_Post && $post->post_type === BuilderiusTemplatePostType::POST_TYPE) {
            $template = $this->templateFromPostFactory->createTemplate($post);
            if ($template instanceof BuilderiusTemplateInterface) {
                $event->setOwner($template);
            }
        }
    }
}