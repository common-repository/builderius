<?php

namespace Builderius\MooMoo\Platform\Bundle\RestApiBundle\Controller\Registrator;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
class RestApiControllersRegistrator implements \Builderius\MooMoo\Platform\Bundle\RestApiBundle\Controller\Registrator\RestApiControllersRegistratorInterface
{
    /**
     * @inheritDoc
     */
    public function registerRestControllers(array $restControllers)
    {
        add_action('rest_api_init', function () use($restControllers) {
            foreach ($restControllers as $restController) {
                if ($restController instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface && $restController->hasConditions()) {
                    $evaluated = \true;
                    foreach ($restController->getConditions() as $condition) {
                        if ($condition->evaluate() === \false) {
                            $evaluated = \false;
                            break;
                        }
                    }
                    if (!$evaluated) {
                        continue;
                    }
                    $restController->register_routes();
                } else {
                    $restController->register_routes();
                }
            }
        });
    }
}
