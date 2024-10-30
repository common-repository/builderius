<?php

namespace Builderius\Bundle\TemplateBundle\Hook;

use Builderius\Bundle\TemplateBundle\Applicant\DataProvider\BuilderiusTemplateApplicantDataProviderInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\Symfony\Component\Cache\CacheItem;

class ApplicantDataProvidingHook extends AbstractApplicantDataAction
{
    /**
     * @var BuilderiusTemplateApplicantDataProviderInterface
     */
    private $applicantDataProvider;

    /**
     * @var BuilderiusPersistentObjectCache
     */
    private $persistentCache;

    /**
     * @param BuilderiusTemplateApplicantDataProviderInterface $applicantDataProvider
     * @return $this
     */
    public function setApplicantDataProvider(BuilderiusTemplateApplicantDataProviderInterface $applicantDataProvider)
    {
        $this->applicantDataProvider = $applicantDataProvider;

        return $this;
    }

    /**
     * @param BuilderiusPersistentObjectCache $persistentCache
     * @return $this
     */
    public function setPersistentCache(BuilderiusPersistentObjectCache $persistentCache)
    {
        $this->persistentCache = $persistentCache;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $user = apply_filters('builderius_get_current_user', $this->getUser());
        if (isset($_POST['builderius-applicant-data']) && isset($_POST['uniqid']) && user_can($user, 'builderius-development')) {
            /** @var CacheItem $dataCacheItem */
            $dataCacheItem = $this->persistentCache->getItem(sprintf('applicant_data_%s', $_POST['uniqid']));
            $dataCacheItem->set($this->applicantDataProvider->getData());
            $this->persistentCache->save($dataCacheItem);
        }
    }
}