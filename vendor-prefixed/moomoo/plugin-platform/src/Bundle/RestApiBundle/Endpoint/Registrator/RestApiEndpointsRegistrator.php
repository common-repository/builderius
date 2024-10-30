<?php

namespace Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\Registrator;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
class RestApiEndpointsRegistrator implements \Builderius\MooMoo\Platform\Bundle\RestApiBundle\Endpoint\Registrator\RestApiEndpointsRegistratorInterface
{
    /**
     * @inheritDoc
     */
    public function registerRestEndpoints(array $endpoints)
    {
        add_action('rest_api_init', function () use($endpoints) {
            foreach ($endpoints as $endpoint) {
                if ($endpoint instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface && $endpoint->hasConditions()) {
                    $evaluated = \true;
                    foreach ($endpoint->getConditions() as $condition) {
                        if ($condition->evaluate() === \false) {
                            $evaluated = \false;
                            break;
                        }
                    }
                    if (!$evaluated) {
                        continue;
                    }
                    $endpoint->registerRoutes();
                } else {
                    $endpoint->registerRoutes();
                }
            }
        });
    }
}
