<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Builderius\Zend\Code\Reflection\DocBlock;

use Builderius\Zend\Code\Generic\Prototype\PrototypeClassFactory;
use Builderius\Zend\Code\Reflection\DocBlock\Tag\TagInterface;
class TagManager extends \Builderius\Zend\Code\Generic\Prototype\PrototypeClassFactory
{
    /**
     * @return void
     */
    public function initializeDefaultTags()
    {
        $this->addPrototype(new \Builderius\Zend\Code\Reflection\DocBlock\Tag\ParamTag());
        $this->addPrototype(new \Builderius\Zend\Code\Reflection\DocBlock\Tag\ReturnTag());
        $this->addPrototype(new \Builderius\Zend\Code\Reflection\DocBlock\Tag\MethodTag());
        $this->addPrototype(new \Builderius\Zend\Code\Reflection\DocBlock\Tag\PropertyTag());
        $this->addPrototype(new \Builderius\Zend\Code\Reflection\DocBlock\Tag\AuthorTag());
        $this->addPrototype(new \Builderius\Zend\Code\Reflection\DocBlock\Tag\LicenseTag());
        $this->addPrototype(new \Builderius\Zend\Code\Reflection\DocBlock\Tag\ThrowsTag());
        $this->addPrototype(new \Builderius\Zend\Code\Reflection\DocBlock\Tag\VarTag());
        $this->setGenericPrototype(new \Builderius\Zend\Code\Reflection\DocBlock\Tag\GenericTag());
    }
    /**
     * @param string $tagName
     * @param string $content
     * @return TagInterface
     */
    public function createTag($tagName, $content = null)
    {
        /* @var TagInterface $newTag */
        $newTag = $this->getClonedPrototype($tagName);
        if ($content) {
            $newTag->initialize($content);
        }
        return $newTag;
    }
}
