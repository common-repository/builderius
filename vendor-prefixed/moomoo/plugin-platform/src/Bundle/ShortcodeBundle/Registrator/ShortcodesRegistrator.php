<?php

namespace Builderius\MooMoo\Platform\Bundle\ShortcodeBundle\Registrator;

use Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface;
use Builderius\MooMoo\Platform\Bundle\ShortcodeBundle\Model\ShortcodeInterface;
class ShortcodesRegistrator implements \Builderius\MooMoo\Platform\Bundle\ShortcodeBundle\Registrator\ShortcodesRegistratorInterface
{
    /**
     * @var ShortcodeInterface[]
     */
    private $shortcodes = [];
    /**
     * @param ShortcodeInterface $shortcode
     * @return $this
     */
    public function addShortcode(\Builderius\MooMoo\Platform\Bundle\ShortcodeBundle\Model\ShortcodeInterface $shortcode)
    {
        $this->shortcodes[$shortcode->getTag()] = $shortcode;
        return $this;
    }
    /**
     * @inheritDoc
     */
    public function registerShortcodes()
    {
        $shortcodes = $this->shortcodes;
        add_action('init', function () use($shortcodes) {
            foreach ($shortcodes as $shortcode) {
                if ($shortcode instanceof \Builderius\MooMoo\Platform\Bundle\ConditionBundle\Model\ConditionAwareInterface && $shortcode->hasConditions()) {
                    $evaluated = \true;
                    foreach ($shortcode->getConditions() as $condition) {
                        if ($condition->evaluate() === \false) {
                            $evaluated = \false;
                            break;
                        }
                    }
                    if (!$evaluated) {
                        continue;
                    }
                    $this->registerShortcode($shortcode);
                } else {
                    $this->registerShortcode($shortcode);
                }
            }
        });
    }
    /**
     * @param ShortcodeInterface $shortcode
     */
    private function registerShortcode(\Builderius\MooMoo\Platform\Bundle\ShortcodeBundle\Model\ShortcodeInterface $shortcode)
    {
        add_shortcode($shortcode->getTag(), [$shortcode, 'callback']);
    }
}
