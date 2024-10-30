<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\Twig\Extension;

use Builderius\Symfony\Component\Security\Acl\Voter\FieldVote;
use Builderius\Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Builderius\Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFunction;
/**
 * SecurityExtension exposes security context features.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class SecurityExtension extends \Builderius\Twig\Extension\AbstractExtension
{
    private $securityChecker;
    public function __construct(\Builderius\Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $securityChecker = null)
    {
        $this->securityChecker = $securityChecker;
    }
    /**
     * @param mixed $object
     */
    public function isGranted($role, $object = null, string $field = null) : bool
    {
        if (null === $this->securityChecker) {
            return \false;
        }
        if (null !== $field) {
            $object = new \Builderius\Symfony\Component\Security\Acl\Voter\FieldVote($object, $field);
        }
        try {
            return $this->securityChecker->isGranted($role, $object);
        } catch (\Builderius\Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException $e) {
            return \false;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return [new \Builderius\Twig\TwigFunction('is_granted', [$this, 'isGranted'])];
    }
}
