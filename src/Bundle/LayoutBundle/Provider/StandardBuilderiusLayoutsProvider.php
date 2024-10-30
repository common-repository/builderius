<?php

namespace Builderius\Bundle\LayoutBundle\Provider;

use Builderius\Bundle\LayoutBundle\Model\BuilderiusLayoutInterface;
use Builderius\Bundle\TemplateBundle\Cache\BuilderiusRuntimeObjectCache;

class StandardBuilderiusLayoutsProvider implements BuilderiusLayoutsProviderInterface
{
    /**
     * @var array
     */
    private $layoutsByTechs = [];

    /**
     * @var BuilderiusRuntimeObjectCache
     */
    private $cache;

    /**
     * @param BuilderiusRuntimeObjectCache $cache
     */
    public function __construct(BuilderiusRuntimeObjectCache $cache)
    {
        $this->cache = $cache;
    }
    
    /**
     * @param BuilderiusLayoutInterface $layout
     */
    public function addLayout(BuilderiusLayoutInterface $layout)
    {
        $originalImage = $layout->getImage();
        if (!empty($originalImage) && strpos($originalImage, 'base64,') === false) {
            if (false === file_get_contents($originalImage, 0, null, 0, 1)) {
                $array = explode('wp-content', $originalImage);
                if (isset($array[1])) {
                    $image = sprintf('%s/wp-content%s', get_site_url(), $array[1]);
                    $layout->setImage($image);
                }
            }
        }
        foreach ($layout->getTechnologies() as $technology) {
            if (!isset($this->layoutsByTechs[$technology])) {
                $this->layoutsByTechs[$technology] = [];
            }
            if (!in_array($layout, $this->layoutsByTechs[$technology])) {
                $this->layoutsByTechs[$technology][$layout->getName()] = $layout;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLayouts($technology)
    {
        $layouts = $this->cache->get(sprintf('builderius_%s_standard_layouts', $technology));
        if (false === $layouts) {
            if (!isset($this->layoutsByTechs[$technology])) {
                return [];
            }

            $layouts = $this->layoutsByTechs[$technology];
            $this->cache->set(sprintf('builderius_%s_standard_layouts', $technology), $layouts);
        }

        return $layouts;
    }

    /**
     * {@inheritdoc}
     */
    public function getLayout($name, $technology)
    {
        if ($this->hasLayout($name, $technology)) {
            return $this->getLayouts($technology)[$name];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasLayout($name, $technology)
    {
        return array_key_exists($name, $this->getLayouts($technology));
    }
}
