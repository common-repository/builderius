<?php

namespace Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\Registrator;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\PostTypeInterface;
class PostTypesRegistrator implements \Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\Registrator\PostTypesRegistratorInterface
{
    /**
     * @inheritDoc
     */
    public function registerPostTypes(array $postTypes)
    {
        if (!is_blog_installed()) {
            return;
        }
        add_action('init', function () use($postTypes) {
            foreach ($postTypes as $postType) {
                if ($postType instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface && $postType->hasConditions()) {
                    $evaluated = \true;
                    foreach ($postType->getConditions() as $condition) {
                        if ($condition->evaluate() === \false) {
                            $evaluated = \false;
                            break;
                        }
                    }
                    if (!$evaluated) {
                        continue;
                    }
                    $this->registerPostType($postType);
                } else {
                    $this->registerPostType($postType);
                }
            }
        });
    }
    /**
     * @param PostTypeInterface $postType
     */
    private function registerPostType(\Builderius\MooMoo\Platform\Bundle\PostBundle\PostType\PostTypeInterface $postType)
    {
        if (!post_type_exists($postType->getType())) {
            register_post_type($postType->getType(), $postType->getArguments());
        }
    }
}
