<?php

namespace Builderius\Bundle\ThemeBundle\Twig;

use Builderius\Twig\Extension\AbstractExtension;
use Builderius\Twig\TwigFunction;

class ThemeExtension extends AbstractExtension
{
    const NAME = 'builderius_theme';

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'builderius_wp_function',
                [$this, 'wpFunction']
            )
        ];
    }

    /**
     * @param string $functionName
     * @param array|null $arguments
     * @return string
     * @throws \Exception
     */
    public function wpFunction($functionName, $arguments = null)
    {
        if (!defined( 'BUILDERIUS_DEVELOPMENT_MODE') || !BUILDERIUS_DEVELOPMENT_MODE) {
            error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_STRICT & ~E_DEPRECATED);
        }
        if ($arguments !== null) {
            return call_user_func_array($functionName, $arguments);
        }

        return call_user_func_array($functionName, []);
    }
}