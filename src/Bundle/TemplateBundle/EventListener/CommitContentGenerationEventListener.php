<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateContentProviderInterface;
use Builderius\Bundle\VCSBundle\Model\BuilderiusCommit;

class CommitContentGenerationEventListener
{
    /**
     * @var BuilderiusTemplateContentProviderInterface
     */
    private $contentProvider;

    /**
     * @param BuilderiusTemplateContentProviderInterface $contentProvider
     */
    public function __construct(BuilderiusTemplateContentProviderInterface $contentProvider)
    {
        $this->contentProvider = $contentProvider;
    }

    /**
     * @param PostContainingEvent $event
     */
    public function onCommitCreation(PostContainingEvent $event)
    {
        $commitPost = $event->getPost();
        if ($commitPost && $contentConfig = $commitPost->__get(BuilderiusCommit::CONTENT_CONFIG_FIELD)) {
            $contentConfig = json_decode($contentConfig, true);
            if (isset($contentConfig['template']) && isset($contentConfig['template']['technology'])) {
                $commitPost->post_content = json_encode($this->contentProvider->getContent(
                    $contentConfig['template']['technology'],
                    $contentConfig
                ), JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
                remove_all_filters('content_save_pre');
                wp_update_post($commitPost);
            }
        }
    }
}