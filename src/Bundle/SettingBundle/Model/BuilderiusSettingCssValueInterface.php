<?php

namespace Builderius\Bundle\SettingBundle\Model;

interface BuilderiusSettingCssValueInterface
{
    /**
     * @param string $pseudoClass
     * @return $this
     */
    public function setPseudoClass($pseudoClass);
    
    /**
     * @return string
     */
    public function getPseudoClass();
    
    /**
     * @param string $mediaQuery
     * @return $this
     */
    public function setMediaQuery($mediaQuery);
    
    /**
     * @return string
     */
    public function getMediaQuery();
}
