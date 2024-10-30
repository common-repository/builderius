<?php

namespace Builderius\Bundle\TemplateBundle\Provider\TemplatePosts;

interface BuilderiusTemplatePostsProviderInterface
{
    /**
     * @param string|null $subType
     * @param bool $publishedOnly
     * @return \WP_Post[]
     */
    public function getTemplatePosts($subType = null, $publishedOnly = false);
}