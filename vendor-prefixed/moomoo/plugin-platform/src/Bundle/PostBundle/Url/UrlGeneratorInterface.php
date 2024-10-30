<?php

namespace Builderius\MooMoo\Platform\Bundle\PostBundle\Url;

interface UrlGeneratorInterface
{
    /**
     * @return string
     */
    public function generate();
}
