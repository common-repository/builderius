<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Builderius\Zend\Code\Generator\DocBlock\Tag;

use Builderius\Zend\Code\Generator\AbstractGenerator;
use Builderius\Zend\Code\Generator\DocBlock\TagManager;
use Builderius\Zend\Code\Reflection\DocBlock\Tag\TagInterface as ReflectionTagInterface;
class AuthorTag extends \Builderius\Zend\Code\Generator\AbstractGenerator implements \Builderius\Zend\Code\Generator\DocBlock\Tag\TagInterface
{
    /**
     * @var string
     */
    protected $authorName;
    /**
     * @var string
     */
    protected $authorEmail;
    /**
     * @param string $authorName
     * @param string $authorEmail
     */
    public function __construct($authorName = null, $authorEmail = null)
    {
        if (!empty($authorName)) {
            $this->setAuthorName($authorName);
        }
        if (!empty($authorEmail)) {
            $this->setAuthorEmail($authorEmail);
        }
    }
    /**
     * @param ReflectionTagInterface $reflectionTag
     * @return AuthorTag
     * @deprecated Deprecated in 2.3. Use TagManager::createTagFromReflection() instead
     */
    public static function fromReflection(\Builderius\Zend\Code\Reflection\DocBlock\Tag\TagInterface $reflectionTag)
    {
        $tagManager = new \Builderius\Zend\Code\Generator\DocBlock\TagManager();
        $tagManager->initializeDefaultTags();
        return $tagManager->createTagFromReflection($reflectionTag);
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'author';
    }
    /**
     * @param string $authorEmail
     * @return AuthorTag
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
        return $this;
    }
    /**
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }
    /**
     * @param string $authorName
     * @return AuthorTag
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
        return $this;
    }
    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }
    /**
     * @return string
     */
    public function generate()
    {
        $output = '@author' . (!empty($this->authorName) ? ' ' . $this->authorName : '') . (!empty($this->authorEmail) ? ' <' . $this->authorEmail . '>' : '');
        return $output;
    }
}
