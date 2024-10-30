<?php

namespace Builderius\Bundle\SettingBundle\Command;

use Builderius\MooMoo\Platform\Bundle\WpCliBundle\Model\WpCliCommandInterface;
use Builderius\Symfony\Component\Yaml\Yaml;

class BuilderiusFontsGenerationCommand implements WpCliCommandInterface
{
    const DEFAULT_GENERIC_FAMILIES = [
        'sans-serif',
        'serif',
        'monospace',
        'fantasy',
        'script'
    ];
    const DEFAULT_FONT_STYLES = [
        'normal',
        'italic',
        'oblique',
        'inherit',
        'initial',
        'unset',
    ];
    const DEFAULT_FONT_WEIGHTS = [
        '100',
        '200',
        '300',
        '400',
        '500',
        '600',
        '700',
        '800',
        '900',
        'lighter',
        'bolder',
        'inherit',
        'initial',
        'unset',
    ];

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'builderius:settings:generate-fonts';
    }

    /**
     * @inheritDoc
     */
    public function execute($arguments = [], $assoc_arguments = [])
    {
        if (!isset($assoc_arguments['google-api-key'])) {
            \WP_CLI::line('Missed required argument "--google-api-key"');
            return;
        }
        $value = Yaml::parseFile(__DIR__.'/../Resources/config/setting_font.yml');

        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['fontType']['values'] =
            [];
        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['genericFamily']['values'] =
            [];
        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['fontFamily']['values'] =
            [];
        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['fontStyle']['values'] =
            [];
        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['fontWeight']['values'] =
            [];

        try {
            $value = $this->parseWebSafeFonts($value);
            $value = $this->parseGoogleFonts($value, $assoc_arguments['google-api-key']);

            $yaml = Yaml::dump($value, 10, 2);
            file_put_contents(__DIR__ . '/../Resources/config/setting_font.yml', $yaml);
            \WP_CLI::line('Fonts were successfully generated');
        } catch(\Exception $e) {
            \WP_CLI::line($e->getMessage());
        }
    }

    /**
     * @param array $value
     * @return array
     */
    private function parseWebSafeFonts(array $value)
    {
        $fonts = json_decode(file_get_contents('https://raw.githubusercontent.com/shtrihstr/node-web-safe-fonts/master/list.json'), true);
        $options = $value['services']['builderius_setting.setting.font']['arguments'][0]['options'];
        $genericFamily = $options['genericFamily']['values'];
        $fontFamily = $options['fontFamily']['values'];
        $fontStyle = $options['fontStyle']['values'];
        $fontWeight = $options['fontWeight']['values'];

        foreach ($fonts as $item) {
            if ($item['type'] === 'monospaced') {
                $item['type'] = 'monospace';
            }
            if (empty($genericFamily) || !isset($genericFamily['common']) || !in_array($item['type'], $genericFamily['common'])) {
                $genericFamily['common'][] = $item['type'];
            }
            if (empty($fontFamily) || !isset($fontFamily['common.' . $item['type']]) || !in_array($item['family'], $fontFamily['common.' . $item['type']]) &&
                $item['family'] !== strtolower($item['family'])) {
                $fontFamily['common.' . $item['type']][] = $item['family'];
                $fontStyle[$item['family']] = self::DEFAULT_FONT_STYLES;
                $fontWeight[$item['family']] = self::DEFAULT_FONT_WEIGHTS;
                /*foreach ($item['generic'] as $generic) {
                    if ($generic !== $item['type'] && !in_array($generic, self::DEFAULT_GENERIC_FAMILIES)) {
                        if (!in_array($generic, $fontFamily['common.' . $item['type']]) &&
                            $generic !== strtolower($generic)) {
                            $fontFamily['common.' . $item['type']][] = $generic;
                            $fontStyle[$generic] = self::DEFAULT_FONT_STYLES;
                            $fontWeight[$generic] = self::DEFAULT_FONT_WEIGHTS;
                        }
                    }
                }*/
            }
        }
        foreach ($fontFamily as $gf => $families) {
            sort($fontFamily[$gf]);
        }

        foreach (self::DEFAULT_GENERIC_FAMILIES as $defaultGenericFamily) {
            if (!in_array($defaultGenericFamily, $genericFamily['common'])) {
                $genericFamily['common'][] = $defaultGenericFamily;
            }
        }

        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['fontType']['values'][] = 'common';
        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['genericFamily']['values'] = $genericFamily;
        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['fontFamily']['values'] = $fontFamily;
        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['fontStyle']['values'] = $fontStyle;
        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['fontWeight']['values'] = $fontWeight;

        return $value;
    }

    /**
     * @param array $value
     * @return array
     */
    private function parseGoogleFonts(array $value, $key)
    {
        $fonts = json_decode(file_get_contents(sprintf('https://www.googleapis.com/webfonts/v1/webfonts?key=%s', $key)), true);
        $options = $value['services']['builderius_setting.setting.font']['arguments'][0]['options'];
        $genericFamily = $options['genericFamily']['values'];
        $fontFamily = $options['fontFamily']['values'];
        $fontStyle = $options['fontStyle']['values'];
        $fontWeight = $options['fontWeight']['values'];
        foreach ($fonts['items'] as $item) {
            if (empty($genericFamily) || !isset($genericFamily['google']) || !in_array($item['category'], $genericFamily['google'])) {
                $genericFamily['google'][] = $item['category'];
            }
            $fontFamily['google.' . $item['category']][] = $item['family'];
            foreach ($item['variants'] as $variant) {
                if (!preg_match('/[0-9]/', $variant)) {
                    if ($variant === 'regular') {
                        $variant = 'normal';
                    }
                    $fontStyle[$item['family']][] = $variant;
                } elseif (!preg_match('/[a-z]/', $variant)) {
                    $fontWeight[$item['family']][] = (int)$variant;
                }
            }
            if (!isset($fontWeight[$item['family']])) {
                $fontWeight[$item['family']][] = 400;
            }
            if (!in_array(400, $fontWeight[$item['family']])) {
                $fontWeight[$item['family']][] = 400;
            }
            sort($fontWeight[$item['family']]);
        }

        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['fontType']['values'][] = 'google';
        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['genericFamily']['values'] = $genericFamily;
        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['fontFamily']['values'] = $fontFamily;
        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['fontStyle']['values'] = $fontStyle;
        $value['services']['builderius_setting.setting.font']['arguments'][0]['options']['fontWeight']['values'] = $fontWeight;

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalRegistrationParameters()
    {
        return [
            'shortdesc' => 'Generates font setting options'
        ];
    }
}
