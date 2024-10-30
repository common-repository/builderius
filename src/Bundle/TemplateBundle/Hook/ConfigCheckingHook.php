<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Checker\ContentConfig\BuilderiusTemplateContentConfigCheckerInterface;
use Builderius\Bundle\TemplateBundle\Helper\ContentConfigHelper;
use Builderius\MooMoo\Platform\Bundle\HookBundle\Model\AbstractFilter;

class ConfigCheckingHook extends AbstractFilter
{
    const HOOK = 'builderius_template.check_config';

    /**
     * @var BuilderiusTemplateContentConfigCheckerInterface
     */
    private $contentConfigChecker;

    /**
     * @param BuilderiusTemplateContentConfigCheckerInterface $contentConfigChecker
     * @return $this
     */
    public function setContentConfigChecker(BuilderiusTemplateContentConfigCheckerInterface $contentConfigChecker)
    {
        $this->contentConfigChecker = $contentConfigChecker;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $templateConfig = ContentConfigHelper::formatConfig(json_decode(func_get_arg(0), true));
        try {
            $this->contentConfigChecker->check($templateConfig);
        } catch (\Exception $e) {
            return new \WP_Error(
                'builderius_template_config_invalid',
                __(method_exists ($e, 'getFullMessage') ? $e->getFullMessage() : $e->getMessage()),
                ['status' => 400]
            );
        }

        return $templateConfig;
    }
}
