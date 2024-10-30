<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper;

use Builderius\ProxyManager\Generator\ClassGenerator;
use Builderius\ProxyManager\GeneratorStrategy\BaseGeneratorStrategy;
use Builderius\ProxyManager\Version;
use Builderius\Symfony\Component\DependencyInjection\Definition;
use Builderius\Symfony\Component\DependencyInjection\LazyProxy\PhpDumper\DumperInterface;
/**
 * Generates dumped PHP code of proxies via reflection.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @final
 */
class ProxyDumper implements \Builderius\Symfony\Component\DependencyInjection\LazyProxy\PhpDumper\DumperInterface
{
    private $salt;
    private $proxyGenerator;
    private $classGenerator;
    public function __construct(string $salt = '')
    {
        $this->salt = $salt;
        $this->proxyGenerator = new \Builderius\Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\LazyLoadingValueHolderGenerator();
        $this->classGenerator = new \Builderius\ProxyManager\GeneratorStrategy\BaseGeneratorStrategy();
    }
    /**
     * {@inheritdoc}
     */
    public function isProxyCandidate(\Builderius\Symfony\Component\DependencyInjection\Definition $definition) : bool
    {
        return ($definition->isLazy() || $definition->hasTag('proxy')) && $this->proxyGenerator->getProxifiedClass($definition);
    }
    /**
     * {@inheritdoc}
     */
    public function getProxyFactoryCode(\Builderius\Symfony\Component\DependencyInjection\Definition $definition, string $id, string $factoryCode) : string
    {
        $instantiation = 'return';
        if ($definition->isShared()) {
            $instantiation .= \sprintf(' $this->%s[%s] =', $definition->isPublic() && !$definition->isPrivate() ? 'services' : 'privates', \var_export($id, \true));
        }
        $proxyClass = $this->getProxyClassName($definition);
        return <<<EOF
        if (\$lazyLoad) {
            {$instantiation} \$this->createProxy('{$proxyClass}', function () {
                return \\{$proxyClass}::staticProxyConstructor(function (&\$wrappedInstance, \\Builderius\\ProxyManager\\Proxy\\LazyLoadingInterface \$proxy) {
                    \$wrappedInstance = {$factoryCode};

                    \$proxy->setProxyInitializer(null);

                    return true;
                });
            });
        }


EOF;
    }
    /**
     * {@inheritdoc}
     */
    public function getProxyCode(\Builderius\Symfony\Component\DependencyInjection\Definition $definition) : string
    {
        $code = $this->classGenerator->generate($this->generateProxyClass($definition));
        $code = \preg_replace('/^(class [^ ]++ extends )([^\\\\])/', '$1\\\\$2', $code);
        if (\version_compare(self::getProxyManagerVersion(), '2.2', '<')) {
            $code = \preg_replace('/((?:\\$(?:this|initializer|instance)->)?(?:publicProperties|initializer|valueHolder))[0-9a-f]++/', '${1}' . $this->getIdentifierSuffix($definition), $code);
        }
        if (\version_compare(self::getProxyManagerVersion(), '2.5', '<')) {
            $code = \preg_replace('/ \\\\Closure::bind\\(function ((?:& )?\\(\\$instance(?:, \\$value)?\\))/', ' \\Closure::bind(static function \\1', $code);
        }
        return $code;
    }
    private static function getProxyManagerVersion() : string
    {
        if (!\class_exists(\Builderius\ProxyManager\Version::class)) {
            return '0.0.1';
        }
        return \defined(\Builderius\ProxyManager\Version::class . '::VERSION') ? \Builderius\ProxyManager\Version::VERSION : \Builderius\ProxyManager\Version::getVersion();
    }
    /**
     * Produces the proxy class name for the given definition.
     */
    private function getProxyClassName(\Builderius\Symfony\Component\DependencyInjection\Definition $definition) : string
    {
        $class = $this->proxyGenerator->getProxifiedClass($definition);
        return \preg_replace('/^.*\\\\/', '', $class) . '_' . $this->getIdentifierSuffix($definition);
    }
    private function generateProxyClass(\Builderius\Symfony\Component\DependencyInjection\Definition $definition) : \Builderius\ProxyManager\Generator\ClassGenerator
    {
        $generatedClass = new \Builderius\ProxyManager\Generator\ClassGenerator($this->getProxyClassName($definition));
        $class = $this->proxyGenerator->getProxifiedClass($definition);
        $this->proxyGenerator->setFluentSafe($definition->hasTag('proxy'));
        $this->proxyGenerator->generate(new \ReflectionClass($class), $generatedClass);
        return $generatedClass;
    }
    private function getIdentifierSuffix(\Builderius\Symfony\Component\DependencyInjection\Definition $definition) : string
    {
        $class = $this->proxyGenerator->getProxifiedClass($definition);
        return \substr(\hash('sha256', $class . $this->salt), -7);
    }
}
