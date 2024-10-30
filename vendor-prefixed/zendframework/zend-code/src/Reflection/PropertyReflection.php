<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Builderius\Zend\Code\Reflection;

use ReflectionProperty as PhpReflectionProperty;
use Builderius\Zend\Code\Annotation\AnnotationManager;
use Builderius\Zend\Code\Scanner\AnnotationScanner;
use Builderius\Zend\Code\Scanner\CachingFileScanner;
/**
 * @todo       implement line numbers
 */
class PropertyReflection extends \ReflectionProperty implements \Builderius\Zend\Code\Reflection\ReflectionInterface
{
    /**
     * @var AnnotationScanner
     */
    protected $annotations;
    /**
     * Get declaring class reflection object
     *
     * @return ClassReflection
     */
    public function getDeclaringClass()
    {
        $phpReflection = parent::getDeclaringClass();
        $zendReflection = new \Builderius\Zend\Code\Reflection\ClassReflection($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
    /**
     * Get DocBlock comment
     *
     * @return string|false False if no DocBlock defined
     */
    public function getDocComment()
    {
        return parent::getDocComment();
    }
    /**
     * @return false|DocBlockReflection
     */
    public function getDocBlock()
    {
        if (!($docComment = $this->getDocComment())) {
            return \false;
        }
        $docBlockReflection = new \Builderius\Zend\Code\Reflection\DocBlockReflection($docComment);
        return $docBlockReflection;
    }
    /**
     * @param  AnnotationManager $annotationManager
     * @return AnnotationScanner|false
     */
    public function getAnnotations(\Builderius\Zend\Code\Annotation\AnnotationManager $annotationManager)
    {
        if (null !== $this->annotations) {
            return $this->annotations;
        }
        if (($docComment = $this->getDocComment()) == '') {
            return \false;
        }
        $class = $this->getDeclaringClass();
        $cachingFileScanner = $this->createFileScanner($class->getFileName());
        $nameInformation = $cachingFileScanner->getClassNameInformation($class->getName());
        if (!$nameInformation) {
            return \false;
        }
        $this->annotations = new \Builderius\Zend\Code\Scanner\AnnotationScanner($annotationManager, $docComment, $nameInformation);
        return $this->annotations;
    }
    /**
     * @return string
     */
    public function toString()
    {
        return $this->__toString();
    }
    /**
     * Creates a new FileScanner instance.
     *
     * By having this as a separate method it allows the method to be overridden
     * if a different FileScanner is needed.
     *
     * @param  string $filename
     *
     * @return CachingFileScanner
     */
    protected function createFileScanner($filename)
    {
        return new \Builderius\Zend\Code\Scanner\CachingFileScanner($filename);
    }
}
