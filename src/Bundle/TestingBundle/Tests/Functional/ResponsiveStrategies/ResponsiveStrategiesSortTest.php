<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional\ResponsiveStrategies;

use Builderius\Bundle\ResponsiveBundle\Strategy\DesktopFirstBuilderiusResponsiveStrategy;
use Builderius\Bundle\ResponsiveBundle\Strategy\MobileFirstBuilderiusResponsiveStrategy;
use Builderius\Bundle\ResponsiveBundle\Provider\BuilderiusResponsiveStrategiesProviderInterface;
use Builderius\Bundle\TestingBundle\Tests\Functional\AbstractContainerAwareTestCase;

class ResponsiveStrategiesSortTest extends AbstractContainerAwareTestCase
{
    /**
     * @var BuilderiusResponsiveStrategiesProviderInterface
     */
    private $responsiveStrategiesProvider;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->responsiveStrategiesProvider =
            $this->container->get('builderius_responsive.provider.responsive_strategies');
    }

    /**
     * @dataProvider dataProvider
     * @param string $strategyName
     * @param array $notSortedQueries
     * @param array $expectedSortedQueries
     */
    public function testSort($strategyName, array $notSortedQueries, array $expectedSortedQueries)
    {
        $strategy = $this->responsiveStrategiesProvider->getResponsiveStrategy($strategyName);
        $actualSortedQueries = $strategy->sort($notSortedQueries);
        self::assertEquals($expectedSortedQueries, $actualSortedQueries);
    }

    public function dataProvider()
    {
        return [
            [
                'strategyName' => DesktopFirstBuilderiusResponsiveStrategy::NAME,
                'notSortedQueries' => [
                    'screen and (min-width: 320px) and (max-width: 767px)',
                    'screen and (min-height: 480px)',
                    'screen and (min-height: 480px) and (min-width: 320px)',
                    'screen and (min-width: 640px)',
                    'screen and (min-width: 1024px)',
                    'screen and (min-width: 1280px)',
                    'screen and (min-device-width: 320px) and (max-device-width: 767px)',
                    'screen and (max-width: 1023px)',
                    'screen and (max-height: 767px) and (min-height: 320px)',
                    'screen and (max-width: 767px) and (min-width: 320px)',
                    'screen and (max-width: 639px)',
                    'screen and (orientation: portrait)',
                    'screen and (orientation: landscape)',
                    'print',
                    'tv'
                ],
                'expectedSortedQueries' => [
                    'screen and (max-width: 1023px)',
                    'screen and (max-width: 767px) and (min-width: 320px)',
                    'screen and (max-height: 767px) and (min-height: 320px)',
                    'screen and (max-width: 639px)',
                    'screen and (min-device-width: 320px) and (max-device-width: 767px)',
                    'screen and (min-width: 320px) and (max-width: 767px)',
                    'screen and (min-height: 480px) and (min-width: 320px)',
                    'screen and (min-height: 480px)',
                    'screen and (min-width: 640px)',
                    'screen and (min-width: 1024px)',
                    'screen and (min-width: 1280px)',
                    'screen and (orientation: landscape)',
                    'screen and (orientation: portrait)',
                    'tv',
                    'print'
                ]
            ],
            [
                'strategyName' => MobileFirstBuilderiusResponsiveStrategy::NAME,
                'notSortedQueries' => [
                    'screen and (min-width: 320px) and (max-width: 767px)',
                    'screen and (min-height: 480px)',
                    'screen and (min-height: 480px) and (min-width: 320px)',
                    'screen and (min-width: 640px)',
                    'screen and (min-width: 1024px)',
                    'screen and (min-width: 1280px)',
                    'screen and (min-device-width: 320px) and (max-device-width: 767px)',
                    'screen and (max-width: 1023px)',
                    'screen and (max-height: 767px) and (min-height: 320px)',
                    'screen and (max-width: 767px) and (min-width: 320px)',
                    'screen and (max-width: 639px)',
                    'screen and (orientation: landscape)',
                    'screen and (orientation: portrait)',
                    'print',
                    'tv'
                ],
                'expectedSortedQueries' => [
                    'screen and (min-width: 320px) and (max-width: 767px)',
                    'screen and (min-height: 480px)',
                    'screen and (min-height: 480px) and (min-width: 320px)',
                    'screen and (min-width: 640px)',
                    'screen and (min-width: 1024px)',
                    'screen and (min-width: 1280px)',
                    'screen and (min-device-width: 320px) and (max-device-width: 767px)',
                    'screen and (max-width: 1023px)',
                    'screen and (max-height: 767px) and (min-height: 320px)',
                    'screen and (max-width: 767px) and (min-width: 320px)',
                    'screen and (max-width: 639px)',
                    'screen and (orientation: landscape)',
                    'screen and (orientation: portrait)',
                    'tv',
                    'print'
                ]
            ],
            [
                'strategyName' => DesktopFirstBuilderiusResponsiveStrategy::NAME,
                'notSortedQueries' => [
                    'screen and (min-width: 1800px)',
                    'screen and (max-width: 414px) and (max-height: 896px)',
                    'screen and (min-width: 1200px)',
                    'screen and (orientation: portrait)',
                    'screen and (max-width: 1800px)',
                    'print',
                    'screen and (min-width: 900px)',
                    'screen and (max-width: 375px) and (max-height: 670px)',
                    'screen and (max-width: 1200px)',
                    'tv',
                    'screen and (max-height: 768px) and (max-width: 1024px)',
                    'screen and (max-width: 900px)',
                    'screen and (orientation: landscape)',
                    'screen and (max-width: 768px) and (max-height: 1024px)',
                    'screen and (max-width: 600px)',
                    'screen and (max-width: 412px) and (max-height: 730px)',
                    'screen and (max-width: 360px) and (max-height: 640px)',
                ],
                'expectedSortedQueries' => [
                    'screen and (max-width: 1800px)',
                    'screen and (max-width: 1200px)',
                    'screen and (max-width: 900px)',
                    'screen and (max-width: 768px) and (max-height: 1024px)',
                    'screen and (max-height: 768px) and (max-width: 1024px)',
                    'screen and (max-width: 600px)',
                    'screen and (max-width: 414px) and (max-height: 896px)',
                    'screen and (max-width: 412px) and (max-height: 730px)',
                    'screen and (max-width: 375px) and (max-height: 670px)',
                    'screen and (max-width: 360px) and (max-height: 640px)',
                    'screen and (min-width: 900px)',
                    'screen and (min-width: 1200px)',
                    'screen and (min-width: 1800px)',
                    'screen and (orientation: landscape)',
                    'screen and (orientation: portrait)',
                    'tv',
                    'print'
                ]
            ],
            [
                'strategyName' => MobileFirstBuilderiusResponsiveStrategy::NAME,
                'notSortedQueries' => [
                    'screen and (min-width: 1800px)',
                    'screen and (max-width: 414px) and (max-height: 896px)',
                    'screen and (min-width: 1200px)',
                    'screen and (orientation: portrait)',
                    'screen and (max-width: 1800px)',
                    'print',
                    'screen and (min-width: 900px)',
                    'screen and (max-width: 375px) and (max-height: 670px)',
                    'screen and (max-width: 1200px)',
                    'tv',
                    'screen and (max-height: 768px) and (max-width: 1024px)',
                    'screen and (max-width: 900px)',
                    'screen and (orientation: landscape)',
                    'screen and (max-width: 768px) and (max-height: 1024px)',
                    'screen and (max-width: 600px)',
                    'screen and (max-width: 412px) and (max-height: 730px)',
                    'screen and (max-width: 360px) and (max-height: 640px)',
                ],
                'expectedSortedQueries' => [
                    'screen and (min-width: 900px)',
                    'screen and (min-width: 1200px)',
                    'screen and (min-width: 1800px)',
                    'screen and (max-width: 1800px)',
                    'screen and (max-width: 1200px)',
                    'screen and (max-width: 900px)',
                    'screen and (max-height: 768px) and (max-width: 1024px)',
                    'screen and (max-width: 768px) and (max-height: 1024px)',
                    'screen and (max-width: 600px)',
                    'screen and (max-width: 414px) and (max-height: 896px)',
                    'screen and (max-width: 412px) and (max-height: 730px)',
                    'screen and (max-width: 375px) and (max-height: 670px)',
                    'screen and (max-width: 360px) and (max-height: 640px)',
                    'screen and (orientation: landscape)',
                    'screen and (orientation: portrait)',
                    'tv',
                    'print'
                ]
            ],
        ];
    }
}