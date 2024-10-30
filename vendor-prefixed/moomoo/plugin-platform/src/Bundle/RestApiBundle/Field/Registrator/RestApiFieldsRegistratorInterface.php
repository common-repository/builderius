<?php

namespace Builderius\MooMoo\Platform\Bundle\RestApiBundle\Field\Registrator;

use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Field\RestApiFieldProviderInterface;
interface RestApiFieldsRegistratorInterface
{
    /**
     * @param RestApiFieldProviderInterface[] $fieldProviders
     * @throws \InvalidArgumentException
     */
    public function registerFields(array $fieldProviders);
}
