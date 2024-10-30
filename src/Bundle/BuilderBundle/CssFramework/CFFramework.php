<?php

namespace Builderius\Bundle\BuilderBundle\CssFramework;

class CFFramework implements CssFrameworkInterface
{
    /**
     * @var array|null
     */
    private $data;

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'Core Framework';
    }

    /**
     * @return array|null
     */
    private function getData()
    {
        if (null === $this->data) {
            $settings = get_option('core_framework_main', false);
            if (is_array($settings) && isset($settings['selected_id'])) {
                $id = $settings['selected_id'];
                global $wpdb;
                $table_name = $wpdb->prefix . 'core_framework_presets';
                $row = $wpdb->get_row("SELECT * FROM $table_name WHERE id = '$id'");
                $this->data = json_decode($row->data, true);
            }
        }

        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function getClasses()
    {
        if ( is_plugin_active( 'core-framework/core-framework.php' ) ) {
            $data = $this->getData();
            $classes = [];
            foreach ($data['styleSheetData'] as $group) {
                foreach ($group as $item) {
                    if (isset($item['cssObjects'])) {
                        foreach ($item['cssObjects'] as $cssObject) {
                            if (strpos($cssObject['selector'], '.') === 0) {
                                $classes = $this->processSelector($cssObject['selector'], $classes);
                            }
                        }
                    }
                }
            }
            if (isset($data['modulesData']['COMPONENTS']['components'])) {
                foreach ($data['modulesData']['COMPONENTS']['components'] as $component) {
                    if (strpos($component['selector'], '.') === 0) {
                        $classes = $this->processSelector($component['selector'], $classes);
                        if(isset($component['variants'])) {
                            foreach ($component['variants'] as $variant) {
                                if (isset($variant['variantSelector']) && strpos($variant['variantSelector'], '.') === 0) {
                                    $classes = $this->processSelector($variant['variantSelector'], $classes);
                                }
                            }
                        }
                    }
                }
            }
            sort($classes);

            return $classes;
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function getVariables()
    {
        if ( is_plugin_active( 'core-framework/core-framework.php' ) ) {
            $data = $this->getData();
            $variables = [];
            foreach ($data['styleSheetData'] as $group) {
                foreach ($group as $item) {
                    if (isset($item['cssObjects'])) {
                        foreach ($item['cssObjects'] as $cssObject) {
                            foreach ($cssObject['declarations'] as $declaration) {
                                if (strpos($declaration['property'], '--') === 0) {
                                    $variables[] = $declaration['property'];
                                }
                            }
                        }
                    }
                }
            }
            if (isset($data['modulesData']['FLUID_TYPOGRAPHY']['manualSizes'])) {
                foreach ($data['modulesData']['FLUID_TYPOGRAPHY']['manualSizes'] as $component) {
                    $variables[] = '--' . $component['name'];
                }
            }
            if (isset($data['modulesData']['FLUID_SPACING']['manualSizes'])) {
                foreach ($data['modulesData']['FLUID_SPACING']['manualSizes'] as $component) {
                    $variables[] = '--' . $component['name'];
                }
            }
            if (isset($data['modulesData']['COLOR_SYSTEM']['groups'])) {
                foreach ($data['modulesData']['COLOR_SYSTEM']['groups'] as $component) {
                    if (isset($component['colors'])) {
                        foreach ($component['colors'] as $color) {
                            $variables[] = '--' . $color['name'];
                            if (isset($color['transparentVariables'])) {
                                foreach ($color['transparentVariables'] as $tv) {
                                    $variables[] = '--' . $color['name'] . '-' . $tv;
                                }
                            }
                            if (isset($color['shades'])) {
                                foreach ($color['shades'] as $shade) {
                                    $variables[] = '--' . $shade['name'];
                                }
                            }
                            if (isset($color['tints'])) {
                                foreach ($color['tints'] as $tint) {
                                    $variables[] = '--' . $tint['name'];
                                }
                            }
                        }
                    }
                }
            }
            sort($variables);

            return $variables;
        }

        return [];
    }

    /**
     * @param $selector
     * @param $classes
     * @return mixed
     */
    private function processSelector($selector, $classes)
    {
        $arr = explode('.', $selector);
        foreach ($arr as $class) {
            $class = trim($class);
            if (!empty($class) && !in_array($class, $classes)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }
}