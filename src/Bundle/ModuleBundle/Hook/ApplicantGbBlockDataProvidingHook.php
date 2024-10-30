<?php

namespace Builderius\Bundle\ModuleBundle\Hook;

use Builderius\Bundle\TemplateBundle\Applicant\DataProvider\BuilderiusTemplateApplicantDataProviderInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusPersistentObjectCache;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;
use Builderius\Bundle\TemplateBundle\Hook\AbstractApplicantDataAction;
use Builderius\Symfony\Component\Cache\CacheItem;

class ApplicantGbBlockDataProvidingHook extends AbstractApplicantDataAction
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
     * @var BuilderiusRuntimeObjectCache
     */
    private $runtimeCache;

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
     * @param BuilderiusRuntimeObjectCache $runtimeCache
     * @return $this
     */
    public function setRuntimeCache(BuilderiusRuntimeObjectCache $runtimeCache)
    {
        $this->runtimeCache = $runtimeCache;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFunction()
    {
        $user = apply_filters('builderius_get_current_user', $this->getUser());
        if (isset($_POST['builderius-applicant-gbblock-data']) && isset($_POST['uniqid']) && user_can($user, 'builderius-development')) {
            $applicantData = $this->applicantDataProvider->getData();
            unset($applicantData['l10n']);
            unset($applicantData['body_classes']);
            $blockContent = $this->runtimeCache->get(sprintf('applicant_gbblock_content_%s', $_POST['uniqid']));
            $applicantData['content'] = $blockContent;

            /** @var CacheItem $dataCacheItem */
            $dataCacheItem = $this->persistentCache->getItem(sprintf('applicant_gbblock_data_%s', $_POST['uniqid']));
            $dataCacheItem->set($applicantData);
            $this->persistentCache->save($dataCacheItem);
        }
    }
}