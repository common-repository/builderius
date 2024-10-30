<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Builderius\Zend\Code\Reflection;

use ReflectionClass;
use Builderius\Zend\Code\Annotation\AnnotationCollection;
use Builderius\Zend\Code\Annotation\AnnotationManager;
use Builderius\Zend\Code\Scanner\AnnotationScanner;
use Builderius\Zend\Code\Scanner\FileScanner;
use function array_shift;
use function array_slice;
use function array_unshift;
use function file;
use function file_exists;
use function implode;
use function strstr;
class ClassReflection extends \ReflectionClass implements \Builderius\Zend\Code\Reflection\ReflectionInterface
{
    /**
     * @var AnnotationScanner
     */
    protected $annotations;
    /**
     * @var DocBlockReflection
     */
    protected $docBlock;
    /**
     * Return the reflection file of the declaring file.
     *
     * @return FileReflection
     */
    public function getDeclaringFile()
    {
        $instance = new \Builderius\Zend\Code\Reflection\FileReflection($this->getFileName());
        return $instance;
    }
    /**
     * Return the classes DocBlock reflection object
     *
     * @return DocBlockReflection|false
     * @throws Exception\ExceptionInterface for missing DocBock or invalid reflection class
     */
    public function getDocBlock()
    {
        if (isset($this->docBlock)) {
            return $this->docBlock;
        }
        if ('' == $this->getDocComment()) {
            return \false;
        }
        $this->docBlock = new \Builderius\Zend\Code\Reflection\DocBlockReflection($this);
        return $this->docBlock;
    }
    /**
     * @param  AnnotationManager $annotationManager
     * @return AnnotationCollection|false
     */
    public function getAnnotations(\Builderius\Zend\Code\Annotation\AnnotationManager $annotationManager)
    {
        $docComment = $this->getDocComment();
        if ($docComment == '') {
            return \false;
        }
        if ($this->annotations) {
            return $this->annotations;
        }
        $fileScanner = $this->createFileScanner($this->getFileName());
        $nameInformation = $fileScanner->getClassNameInformation($this->getName());
        if (!$nameInformation) {
            return \false;
        }
        $this->annotations = new \Builderius\Zend\Code\Scanner\AnnotationScanner($annotationManager, $docComment, $nameInformation);
        return $this->annotations;
    }
    /**
     * Return the start line of the class
     *
     * @param  bool $includeDocComment
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function getStartLine($includeDocComment = \false)
    {
        if ($includeDocComment && $this->getDocComment() != '') {
            return $this->getDocBlock()->getStartLine();
        }
        return parent::getStartLine();
    }
    /**
     * Return the contents of the class
     *
     * @param  bool $includeDocBlock
     * @return string
     */
    public function getContents($includeDocBlock = \true)
    {
        $fileName = $this->getFileName();
        if (\false === $fileName || !\file_exists($fileName)) {
            return '';
        }
        $filelines = \file($fileName);
        $startnum = $this->getStartLine($includeDocBlock);
        $endnum = $this->getEndLine() - $this->getStartLine();
        // Ensure we get between the open and close braces
        $lines = \array_slice($filelines, $startnum, $endnum);
        \array_unshift($lines, $filelines[$startnum - 1]);
        return \strstr(\implode('', $lines), '{');
    }
    /**
     * Get all reflection objects of implemented interfaces
     *
     * @return ClassReflection[]
     */
    #[\ReturnTypeWillChange]
    public function getInterfaces()
    {
        $phpReflections = parent::getInterfaces();
        $zendReflections = [];
        while ($phpReflections && ($phpReflection = \array_shift($phpReflections))) {
            $instance = new \Builderius\Zend\Code\Reflection\ClassReflection($phpReflection->getName());
            $zendReflections[] = $instance;
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
    /**
     * Return method reflection by name
     *
     * @param  string $name
     * @return MethodReflection
     */
    #[\ReturnTypeWillChange]
    public function getMethod($name)
    {
        $method = new \Builderius\Zend\Code\Reflection\MethodReflection($this->getName(), parent::getMethod($name)->getName());
        return $method;
    }
    /**
     * Get reflection objects of all methods
     *
     * @param  int $filter
     * @return MethodReflection[]
     */
    #[\ReturnTypeWillChange]
    public function getMethods($filter = -1)
    {
        $methods = [];
        foreach (parent::getMethods($filter) as $method) {
            $instance = new \Builderius\Zend\Code\Reflection\MethodReflection($this->getName(), $method->getName());
            $methods[] = $instance;
        }
        return $methods;
    }
    /**
     * Returns an array of reflection classes of traits used by this class.
     *
     * @return null|array
     */
    #[\ReturnTypeWillChange]
    public function getTraits()
    {
        $vals = [];
        $traits = parent::getTraits();
        if ($traits === null) {
            return;
        }
        foreach ($traits as $trait) {
            $vals[] = new \Builderius\Zend\Code\Reflection\ClassReflection($trait->getName());
        }
        return $vals;
    }
    /**
     * Get parent reflection class of reflected class
     *
     * @return ClassReflection|bool
     */
    #[\ReturnTypeWillChange]
    public function getParentClass()
    {
        $phpReflection = parent::getParentClass();
        if ($phpReflection) {
            $zendReflection = new \Builderius\Zend\Code\Reflection\ClassReflection($phpReflection->getName());
            unset($phpReflection);
            return $zendReflection;
        }
        return \false;
    }
    /**
     * Return reflection property of this class by name
     *
     * @param  string $name
     * @return PropertyReflection
     */
    #[\ReturnTypeWillChange]
    public function getProperty($name)
    {
        $phpReflection = parent::getProperty($name);
        $zendReflection = new \Builderius\Zend\Code\Reflection\PropertyReflection($this->getName(), $phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
    /**
     * Return reflection properties of this class
     *
     * @param  int $filter
     * @return PropertyReflection[]
     */
    #[\ReturnTypeWillChange]
    public function getProperties($filter = -1)
    {
        $phpReflections = parent::getProperties($filter);
        $zendReflections = [];
        while ($phpReflections && ($phpReflection = \array_shift($phpReflections))) {
            $instance = new \Builderius\Zend\Code\Reflection\PropertyReflection($this->getName(), $phpReflection->getName());
            $zendReflections[] = $instance;
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
    /**
     * @return string
     */
    public function toString()
    {
        return parent::__toString();
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return parent::__toString();
    }
    /**
     * Creates a new FileScanner instance.
     *
     * By having this as a separate method it allows the method to be overridden
     * if a different FileScanner is needed.
     *
     * @param  string $filename
     *
     * @return FileScanner
     */
    protected function createFileScanner($filename)
    {
        return new \Builderius\Zend\Code\Scanner\FileScanner($filename);
    }
}
