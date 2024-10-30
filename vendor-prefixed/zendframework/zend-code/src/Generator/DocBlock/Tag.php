<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Builderius\Zend\Code\Generator\DocBlock;

use Builderius\Zend\Code\Generator\DocBlock\Tag\GenericTag;
use Builderius\Zend\Code\Reflection\DocBlock\Tag\TagInterface as ReflectionTagInterface;
/**
 * @deprecated Deprecated in 2.3. Use GenericTag instead
 */
class Tag extends \Builderius\Zend\Code\Generator\DocBlock\Tag\GenericTag
{
    /**
     * @param  ReflectionTagInterface $reflectionTag
     * @return Tag
     * @deprecated Deprecated in 2.3. Use TagManager::createTagFromReflection() instead
     */
    public static function fromReflection(\Builderius\Zend\Code\Reflection\DocBlock\Tag\TagInterface $reflectionTag)
    {
        $tagManager = new \Builderius\Zend\Code\Generator\DocBlock\TagManager();
        $tagManager->initializeDefaultTags();
        return $tagManager->createTagFromReflection($reflectionTag);
    }
    /**
     * @param  string $description
     * @return Tag
     * @deprecated Deprecated in 2.3. Use GenericTag::setContent() instead
     */
    public function setDescription($description)
    {
        return $this->setContent($description);
    }
    /**
     * @return string
     * @deprecated Deprecated in 2.3. Use GenericTag::getContent() instead
     */
    public function getDescription()
    {
        return $this->getContent();
    }
}
