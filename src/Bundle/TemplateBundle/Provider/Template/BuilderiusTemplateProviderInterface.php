<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Template;

use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateInterface;

interface BuilderiusTemplateProviderInterface
{
    /**
     * @return \WP_Post
     * @throws \Exception
     */
    public function getTemplatePost();

    /**
     * @return BuilderiusTemplateInterface
     * @throws \Exception
     */
    public function getTemplate();
}
