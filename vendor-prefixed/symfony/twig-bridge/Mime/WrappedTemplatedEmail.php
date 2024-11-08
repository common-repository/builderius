<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Builderius\Symfony\Bridge\Twig\Mime;

use Builderius\Symfony\Component\Mime\Address;
use Builderius\Twig\Environment;
/**
 * @internal
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class WrappedTemplatedEmail
{
    private $twig;
    private $message;
    public function __construct(\Builderius\Twig\Environment $twig, \Builderius\Symfony\Bridge\Twig\Mime\TemplatedEmail $message)
    {
        $this->twig = $twig;
        $this->message = $message;
    }
    public function toName() : string
    {
        return $this->message->getTo()[0]->getName();
    }
    public function image(string $image, string $contentType = null) : string
    {
        $file = $this->twig->getLoader()->getSourceContext($image);
        if ($path = $file->getPath()) {
            $this->message->embedFromPath($path, $image, $contentType);
        } else {
            $this->message->embed($file->getCode(), $image, $contentType);
        }
        return 'cid:' . $image;
    }
    public function attach(string $file, string $name = null, string $contentType = null) : void
    {
        $file = $this->twig->getLoader()->getSourceContext($file);
        if ($path = $file->getPath()) {
            $this->message->attachFromPath($path, $name, $contentType);
        } else {
            $this->message->attach($file->getCode(), $name, $contentType);
        }
    }
    /**
     * @return $this
     */
    public function setSubject(string $subject) : self
    {
        $this->message->subject($subject);
        return $this;
    }
    public function getSubject() : ?string
    {
        return $this->message->getSubject();
    }
    /**
     * @return $this
     */
    public function setReturnPath(string $address) : self
    {
        $this->message->returnPath($address);
        return $this;
    }
    public function getReturnPath() : string
    {
        return $this->message->getReturnPath();
    }
    /**
     * @return $this
     */
    public function addFrom(string $address, string $name = '') : self
    {
        $this->message->addFrom(new \Builderius\Symfony\Component\Mime\Address($address, $name));
        return $this;
    }
    /**
     * @return Address[]
     */
    public function getFrom() : array
    {
        return $this->message->getFrom();
    }
    /**
     * @return $this
     */
    public function addReplyTo(string $address) : self
    {
        $this->message->addReplyTo($address);
        return $this;
    }
    /**
     * @return Address[]
     */
    public function getReplyTo() : array
    {
        return $this->message->getReplyTo();
    }
    /**
     * @return $this
     */
    public function addTo(string $address, string $name = '') : self
    {
        $this->message->addTo(new \Builderius\Symfony\Component\Mime\Address($address, $name));
        return $this;
    }
    /**
     * @return Address[]
     */
    public function getTo() : array
    {
        return $this->message->getTo();
    }
    /**
     * @return $this
     */
    public function addCc(string $address, string $name = '') : self
    {
        $this->message->addCc(new \Builderius\Symfony\Component\Mime\Address($address, $name));
        return $this;
    }
    /**
     * @return Address[]
     */
    public function getCc() : array
    {
        return $this->message->getCc();
    }
    /**
     * @return $this
     */
    public function addBcc(string $address, string $name = '') : self
    {
        $this->message->addBcc(new \Builderius\Symfony\Component\Mime\Address($address, $name));
        return $this;
    }
    /**
     * @return Address[]
     */
    public function getBcc() : array
    {
        return $this->message->getBcc();
    }
    /**
     * @return $this
     */
    public function setPriority(int $priority) : self
    {
        $this->message->setPriority($priority);
        return $this;
    }
    public function getPriority() : int
    {
        return $this->message->getPriority();
    }
}
