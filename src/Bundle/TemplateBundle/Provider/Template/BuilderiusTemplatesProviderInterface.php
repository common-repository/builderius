<?php

namespace Builderius\Bundle\TemplateBundle\Provider\Template;

use Builderius\Bundle\TemplateBundle\Model\BuilderiusTemplateInterface;

interface BuilderiusTemplatesProviderInterface
{
    /**
     * @return \WP_Post[]
     * @throws \Exception
     */
    public function getTemplatePosts();

    /**
     * @return BuilderiusTemplateInterface[]
     * @throws \Exception
     */
    public function getTemplates();
}
