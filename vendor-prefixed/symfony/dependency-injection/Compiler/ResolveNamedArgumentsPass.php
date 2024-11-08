<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Component\DependencyInjection\Compiler;

use Builderius\Symfony\Component\DependencyInjection\Definition;
use Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Builderius\Symfony\Component\DependencyInjection\LazyProxy\ProxyHelper;
use Builderius\Symfony\Component\DependencyInjection\Reference;
/**
 * Resolves named arguments to their corresponding numeric index.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ResolveNamedArgumentsPass extends \Builderius\Symfony\Component\DependencyInjection\Compiler\AbstractRecursivePass
{
    /**
     * {@inheritdoc}
     */
    protected function processValue($value, bool $isRoot = \false)
    {
        if (!$value instanceof \Builderius\Symfony\Component\DependencyInjection\Definition) {
            return parent::processValue($value, $isRoot);
        }
        $calls = $value->getMethodCalls();
        $calls[] = ['__construct', $value->getArguments()];
        foreach ($calls as $i => $call) {
            list($method, $arguments) = $call;
            $parameters = null;
            $resolvedArguments = [];
            foreach ($arguments as $key => $argument) {
                if (\is_int($key)) {
                    $resolvedArguments[$key] = $argument;
                    continue;
                }
                if (null === $parameters) {
                    $r = $this->getReflectionMethod($value, $method);
                    $class = $r instanceof \ReflectionMethod ? $r->class : $this->currentId;
                    $method = $r->getName();
                    $parameters = $r->getParameters();
                }
                if (isset($key[0]) && '$' !== $key[0] && !\class_exists($key) && !\interface_exists($key, \false)) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid service "%s": did you forget to add the "$" prefix to argument "%s"?', $this->currentId, $key));
                }
                if (isset($key[0]) && '$' === $key[0]) {
                    foreach ($parameters as $j => $p) {
                        if ($key === '$' . $p->name) {
                            if ($p->isVariadic() && \is_array($argument)) {
                                foreach ($argument as $variadicArgument) {
                                    $resolvedArguments[$j++] = $variadicArgument;
                                }
                            } else {
                                $resolvedArguments[$j] = $argument;
                            }
                            continue 2;
                        }
                    }
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid service "%s": method "%s()" has no argument named "%s". Check your service definition.', $this->currentId, $class !== $this->currentId ? $class . '::' . $method : $method, $key));
                }
                if (null !== $argument && !$argument instanceof \Builderius\Symfony\Component\DependencyInjection\Reference && !$argument instanceof \Builderius\Symfony\Component\DependencyInjection\Definition) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid service "%s": the value of argument "%s" of method "%s()" must be null, an instance of "%s" or an instance of "%s", "%s" given.', $this->currentId, $key, $class !== $this->currentId ? $class . '::' . $method : $method, \Builderius\Symfony\Component\DependencyInjection\Reference::class, \Builderius\Symfony\Component\DependencyInjection\Definition::class, \gettype($argument)));
                }
                $typeFound = \false;
                foreach ($parameters as $j => $p) {
                    if (!\array_key_exists($j, $resolvedArguments) && \Builderius\Symfony\Component\DependencyInjection\LazyProxy\ProxyHelper::getTypeHint($r, $p, \true) === $key) {
                        $resolvedArguments[$j] = $argument;
                        $typeFound = \true;
                    }
                }
                if (!$typeFound) {
                    throw new \Builderius\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid service "%s": method "%s()" has no argument type-hinted as "%s". Check your service definition.', $this->currentId, $class !== $this->currentId ? $class . '::' . $method : $method, $key));
                }
            }
            if ($resolvedArguments !== $call[1]) {
                \ksort($resolvedArguments);
                $calls[$i][1] = $resolvedArguments;
            }
        }
        list(, $arguments) = \array_pop($calls);
        if ($arguments !== $value->getArguments()) {
            $value->setArguments($arguments);
        }
        if ($calls !== $value->getMethodCalls()) {
            $value->setMethodCalls($calls);
        }
        return parent::processValue($value, $isRoot);
    }
}
