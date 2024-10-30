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
class LicenseTag extends \Builderius\Zend\Code\Generator\AbstractGenerator implements \Builderius\Zend\Code\Generator\DocBlock\Tag\TagInterface
{
    /**
     * @var string
     */
    protected $url;
    /**
     * @var string
     */
    protected $licenseName;
    /**
     * @param string $url
     * @param string $licenseName
     */
    public function __construct($url = null, $licenseName = null)
    {
        if (!empty($url)) {
            $this->setUrl($url);
        }
        if (!empty($licenseName)) {
            $this->setLicenseName($licenseName);
        }
    }
    /**
     * @param ReflectionTagInterface $reflectionTag
     * @return ReturnTag
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
        return 'license';
    }
    /**
     * @param string $url
     * @return LicenseTag
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * @param  string $name
     * @return LicenseTag
     */
    public function setLicenseName($name)
    {
        $this->licenseName = $name;
        return $this;
    }
    /**
     * @return string
     */
    public function getLicenseName()
    {
        return $this->licenseName;
    }
    /**
     * @return string
     */
    public function generate()
    {
        $output = '@license' . (!empty($this->url) ? ' ' . $this->url : '') . (!empty($this->licenseName) ? ' ' . $this->licenseName : '');
        return $output;
    }
}
