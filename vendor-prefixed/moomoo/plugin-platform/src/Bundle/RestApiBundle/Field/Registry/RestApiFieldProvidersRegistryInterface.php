<?php

namespace Builderius\MooMoo\Platform\Bundle\RestApiBundle\Field\Registry;

use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Field\RestApiFieldProviderInterface;
interface RestApiFieldProvidersRegistryInterface
{
    /**
     * @return RestApiFieldProviderInterface[]
     */
    public function getRestApiFieldProviders();
}
