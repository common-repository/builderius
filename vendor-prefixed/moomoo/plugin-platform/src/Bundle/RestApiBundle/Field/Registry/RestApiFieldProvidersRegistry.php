<?php

namespace Builderius\MooMoo\Platform\Bundle\RestApiBundle\Field\Registry;

use Builderius\MooMoo\Platform\Bundle\KernelBundle\Boot\BootServiceInterface;
use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Field\RestApiFieldProviderInterface;
use Builderius\Symfony\Component\DependencyInjection\ContainerInterface;
class RestApiFieldProvidersRegistry implements \Builderius\MooMoo\Platform\Bundle\RestApiBundle\Field\Registry\RestApiFieldProvidersRegistryInterface
{
    /**
     * @var RestApiFieldProviderInterface[]
     */
    private $providers = [];
    /**
     * @param RestApiFieldProviderInterface $provider
     */
    public function addProvider(\Builderius\MooMoo\Platform\Bundle\RestApiBundle\Field\RestApiFieldProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }
    /**
     * @inheritDoc
     */
    public function getRestApiFieldProviders()
    {
        return $this->providers;
    }
}
