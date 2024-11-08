<?php

namespace Builderius\MooMoo\Platform\Bundle\RestApiBundle\Field\Registrator;

use Builderius\MooMoo\Platform\Bundle\RestApiBundle\Field\RestApiFieldProviderInterface;
class RestApiFieldsRegistrator implements \Builderius\MooMoo\Platform\Bundle\RestApiBundle\Field\Registrator\RestApiFieldsRegistratorInterface
{
    /**
     * @inheritDoc
     */
    public function registerFields(array $fieldProviders)
    {
        add_action('rest_api_init', function () use($fieldProviders) {
            foreach ($fieldProviders as $provider) {
                $this->registerRestApiField($provider);
            }
        });
    }
    /**
     * @param RestApiFieldProviderInterface $provider
     */
    private function registerRestApiField(\Builderius\MooMoo\Platform\Bundle\RestApiBundle\Field\RestApiFieldProviderInterface $provider)
    {
        register_rest_field($provider->getObjectType(), $provider->getAttribute(), ['get_callback' => [$provider, 'getGetCallback'], 'update_callback' => [$provider, 'getUpdateCallback'], 'schema' => [$provider, 'getSchema']]);
    }
}
