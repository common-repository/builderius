<?php

namespace Builderius\Bundle\SettingBundle\Model;

class BuilderiusSettingCssValue extends BuilderiusSettingValue implements BuilderiusSettingCssValueInterface
{
    const PSEUDO_CLASS_FIELD = 'pseudo_class';
    const MEDIA_QUERY_FIELD = 'media_query';
    const DEFAULT_PSEUDO_CLASS = 'original';
    const DEFAULT_MEDIA_QUERY = 'all';

    /**
     * @inheritDoc
     */
    public function setPseudoClass($pseudoClass)
    {
        $this->set(self::PSEUDO_CLASS_FIELD, $pseudoClass);
        
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPseudoClass()
    {
        return $this->get(self::PSEUDO_CLASS_FIELD, self::DEFAULT_PSEUDO_CLASS);
    }

    /**
     * @inheritDoc
     */
    public function setMediaQuery($mediaQuery)
    {
        $this->set(self::MEDIA_QUERY_FIELD, $mediaQuery);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMediaQuery()
    {
        return $this->get(self::MEDIA_QUERY_FIELD, self::DEFAULT_MEDIA_QUERY);
    }
}
