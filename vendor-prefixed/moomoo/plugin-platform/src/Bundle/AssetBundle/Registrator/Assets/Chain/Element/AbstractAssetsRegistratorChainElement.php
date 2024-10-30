<?php

namespace Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\Assets\Chain\Element;

use Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\AssetsContainingEvent;
use Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Path\AssetPathProviderInterface;
use Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\Assets\AssetsRegistratorInterface;
use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
abstract class AbstractAssetsRegistratorChainElement implements \Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\Assets\AssetsRegistratorInterface, \Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\Assets\Chain\Element\AssetsRegistratorChainElementInterface
{
    /**
     * @var AssetPathProviderInterface
     */
    protected $pathProvider;
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var string
     */
    protected $registrationFunction = 'wp_enqueue_scripts';
    /**
     * @var AssetsRegistratorChainElementInterface|null
     */
    private $successor;
    /**
     * @param AssetPathProviderInterface $pathProvider
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Path\AssetPathProviderInterface $pathProvider, \Builderius\Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher)
    {
        $this->pathProvider = $pathProvider;
        $this->eventDispatcher = $eventDispatcher;
    }
    /**
     * @param string $registrationFunction
     */
    public function setRegistrationFunction($registrationFunction)
    {
        $this->registrationFunction = $registrationFunction;
    }
    /**
     * @inheritDoc
     */
    public function registerAssets(array $assets)
    {
        add_action($this->registrationFunction, function () use($assets) {
            $event = new \Builderius\MooMoo\Platform\Bundle\AssetBundle\Event\AssetsContainingEvent($assets);
            $this->eventDispatcher->dispatch($event, 'moomoo_assets_before_registration');
            foreach ($event->getAssets() as $asset) {
                if ($asset instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface && $asset->hasConditions()) {
                    $evaluated = \true;
                    foreach ($asset->getConditions() as $condition) {
                        if ($condition->evaluate() === \false) {
                            $evaluated = \false;
                            break;
                        }
                    }
                    if (!$evaluated) {
                        continue;
                    }
                    $this->registerAsset($asset);
                } else {
                    $this->registerAsset($asset);
                }
            }
        }, 20);
    }
    /**
     * @param AssetInterface $asset
     */
    public function registerAsset(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Model\AssetInterface $asset)
    {
        if ($this->isApplicable($asset)) {
            $this->register($asset);
        } elseif ($this->getSuccessor() && $this->getSuccessor()->isApplicable($asset)) {
            $this->getSuccessor()->register($asset);
        }
    }
    /**
     * @param AssetsRegistratorChainElementInterface $assetRegistrator
     */
    public function setSuccessor(\Builderius\MooMoo\Platform\Bundle\AssetBundle\Registrator\Assets\Chain\Element\AssetsRegistratorChainElementInterface $assetRegistrator)
    {
        $this->successor = $assetRegistrator;
    }
    /**
     * @return AssetsRegistratorChainElementInterface|null
     */
    protected function getSuccessor()
    {
        return $this->successor;
    }
}
