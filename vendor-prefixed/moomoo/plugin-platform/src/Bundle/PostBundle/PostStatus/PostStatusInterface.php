<?php

namespace Builderius\MooMoo\Platform\Bundle\PostBundle\PostStatus;

interface PostStatusInterface
{
    /**
     * @return string
     */
    public function getStatus();
    /**
     * @return array
     */
    public function getArguments();
}
