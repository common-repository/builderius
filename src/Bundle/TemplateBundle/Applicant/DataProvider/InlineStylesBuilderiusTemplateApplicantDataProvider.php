<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\DataProvider;

use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Hook\ApplicantInlineAssetsDataPreProvidingEndHook;

class InlineStylesBuilderiusTemplateApplicantDataProvider implements BuilderiusTemplateApplicantDataProviderInterface
{
    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(BuilderiusRuntimeObjectCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'inline_styles';
    }

    /**
     * @inheritDoc
     */
    public function getData(array $applicantQueryVars = [])
    {
        $cachedInlineAssets = $this->cache->get(ApplicantInlineAssetsDataPreProvidingEndHook::CACHE_KEY);
        if (is_array($cachedInlineAssets) && isset($cachedInlineAssets['styles'])) {
            return $cachedInlineAssets['styles'];
        }

        return [];
    }
}