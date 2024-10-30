<?php

namespace Builderius\Bundle\TemplateBundle\EventListener;

use Builderius\Bundle\TemplateBundle\Event\PostContainingEvent;
use Builderius\Bundle\TemplateBundle\Provider\Content\Template\BuilderiusTemplateContentProviderInterface;
use Builderius\Bundle\VCSBundle\Model\BuilderiusBranch;

class BranchHeadCommitContentGenerationEventListener
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
    public function onBranchHeadCommitSave(PostContainingEvent $event)
    {
        $branchHeadCommitPost = $event->getPost();
        if ($branchHeadCommitPost) {
            $notCommittedConfig = json_decode(
                get_post_meta(
                $branchHeadCommitPost->ID,
                BuilderiusBranch::NOT_COMMITTED_CONFIG_FIELD,
                true
            ),
            true
            );
            if (isset($notCommittedConfig['template']) && isset($notCommittedConfig['template']['technology'])) {
                $branchHeadCommitPost->post_content = json_encode($this->contentProvider->getContent(
                    $notCommittedConfig['template']['technology'],
                    $notCommittedConfig
                ), JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
                remove_all_filters('content_save_pre');
                wp_update_post($branchHeadCommitPost);
            }
        }
    }
}