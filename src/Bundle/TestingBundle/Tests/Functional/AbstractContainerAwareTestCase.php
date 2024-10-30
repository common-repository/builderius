<?php

namespace Builderius\Bundle\TestingBundle\Tests\Functional;

use Builderius\MooMoo\Platform\Bundle\TestingBundle\Kernel\TestKernel;
use Builderius\Symfony\Component\DependencyInjection\ContainerInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    /**
     * @return ContainerInterface
     */
    protected $container;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        require __DIR__ . '/../../../../../../../../wp-load.php';
        $kernel = new TestKernel('builderius/builderius.php', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
    }
}