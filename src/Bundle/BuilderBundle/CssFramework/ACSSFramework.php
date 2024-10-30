<?php

namespace Builderius\Bundle\BuilderBundle\CssFramework;

use Automatic_CSS\Model\Config\Classes;
use Automatic_CSS\Model\Config\Variables;

class ACSSFramework implements CssFrameworkInterface
{
    private $trustedPrefixes = [
        'action',
        'primary',
        'secondary',
        'accent',
        'base',
        'shade',
        'white',
        'black',
        'success',
        'danger',
        'warning',
        'info',
        'text',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'space',
        'section',
        'width',
        'content',
        'radius',
        'grid',
        'fr',
        'btn',
        'outline',
        'heading',
        'header',
        'paragraph',
        'focus',
        'box'


    ];
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'Automatic CSS';
    }

    /**
     * @inheritDoc
     */
    public function getClasses()
    {
        if (class_exists('Automatic_CSS\Model\Config\Classes')) {
            return (new Classes())->load();
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function getVariables()
    {
        /*if (class_exists('Automatic_CSS\Model\Config\Variables')) {
            $varsRaw = (new Variables())->load();
            $vars = [];
            foreach (array_keys($varsRaw) as $var) {
                if (in_array(explode('-', $var)[0], $this->trustedPrefixes)) {
                    $vars[] = '--' . $var;
                }
            }
            return $vars;
        }*/

        return [];
    }
}