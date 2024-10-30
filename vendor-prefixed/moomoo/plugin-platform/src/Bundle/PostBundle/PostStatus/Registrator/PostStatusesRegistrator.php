<?php

namespace Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus\Registrator;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus\PostStatusInterface;
class PostStatusesRegistrator implements \Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus\Registrator\PostStatusesRegistratorInterface
{
    /**
     * @inheritDoc
     */
    public function registerPostStatuses(array $postStatuses)
    {
        if (!is_blog_installed()) {
            return;
        }
        add_action('init', function () use($postStatuses) {
            foreach ($postStatuses as $postStatus) {
                if ($postStatus instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface && $postStatus->hasConditions()) {
                    $evaluated = \true;
                    foreach ($postStatus->getConditions() as $condition) {
                        if ($condition->evaluate() === \false) {
                            $evaluated = \false;
                            break;
                        }
                    }
                    if (!$evaluated) {
                        continue;
                    }
                    $this->registerPostStatus($postStatus);
                } else {
                    $this->registerPostStatus($postStatus);
                }
            }
        });
    }
    /**
     * @param PostStatusInterface $postStatus
     */
    private function registerPostStatus(\Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus\PostStatusInterface $postStatus)
    {
        register_post_status($postStatus->getStatus(), $postStatus->getArguments());
    }
}
