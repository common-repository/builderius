<?php

namespace Builderius\MooMoo\Platform\Bundle\MenuBundle\Model;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareTrait;
use Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag;
use Builderius\MooMoo\Platform\Bundle\PostBundle\Url\UrlGeneratorInterface;
class AdminBarNode extends \Builderius\MooMoo\Platform\Bundle\KernelBundle\ParameterBag\ParameterBag implements \Builderius\MooMoo\Platform\Bundle\MenuBundle\Model\AdminBarNodeInterface
{
    const IDENTIFIER_FIELD = 'id';
    const TITLE_FIELD = 'title';
    const PARENT_FIELD = 'parent';
    const HREF_FIELD = 'href';
    const GROUP_FIELD = 'group';
    const META_FIELD = 'meta';
    use ConditionAwareTrait;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function setUrlGenerator(\Builderius\MooMoo\Platform\Bundle\PostBundle\Url\UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->get(self::IDENTIFIER_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->get(self::TITLE_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return $this->get(self::PARENT_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getHref()
    {
        if ($this->get(self::HREF_FIELD)) {
            return $this->get(self::HREF_FIELD);
        } elseif ($this->urlGenerator) {
            return $this->urlGenerator->generate();
        }
        return \false;
    }
    /**
     * @inheritDoc
     */
    public function isGroup()
    {
        return (bool) $this->get(self::GROUP_FIELD);
    }
    /**
     * @inheritDoc
     */
    public function getMeta()
    {
        return $this->get(self::META_FIELD);
    }
}
