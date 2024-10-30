<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\Cache\Adapter;

use Builderius\Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use Builderius\Symfony\Component\Cache\Marshaller\MarshallerInterface;
use Builderius\Symfony\Component\Cache\PruneableInterface;
use Builderius\Symfony\Component\Cache\Traits\FilesystemTrait;
class FilesystemAdapter extends \Builderius\Symfony\Component\Cache\Adapter\AbstractAdapter implements \Builderius\Symfony\Component\Cache\PruneableInterface
{
    use FilesystemTrait;
    public function __construct(string $namespace = '', int $defaultLifetime = 0, string $directory = null, \Builderius\Symfony\Component\Cache\Marshaller\MarshallerInterface $marshaller = null)
    {
        $this->marshaller = $marshaller ?? new \Builderius\Symfony\Component\Cache\Marshaller\DefaultMarshaller();
        parent::__construct('', $defaultLifetime);
        $this->init($namespace, $directory);
    }
}
