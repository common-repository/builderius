<?php

namespace Builderius\MooMoo\Platform\Bundle\MenuBundle\Model;

interface AdminMenuPageInterface extends \Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\MenuElementInterface
{
    /**
     * @return string
     */
    public function getMenuSlug();
    /**
     * @return string
     */
    public function getPageTitle();
    /**
     * @return array
     */
    public function getCapability();
    /**
     * @return string
     */
    public function getPage();
    /**
     * @return string
     */
    public function getIconUrl();
    /**
     * @return int
     */
    public function getPosition();
    /**
     * @return string
     */
    public function getTranslationDomain();
}
