<?php

namespace Builderius\Bundle\TemplateBundle\Applicant\DataProvider;

class L10nBuilderiusTemplateApplicantDataProvider implements BuilderiusTemplateApplicantDataProviderInterface
{
    /**
     * @var array
     */
    private $l10n = [];

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'l10n';
    }

    /**
     * @inheritDoc
     */
    public function getData(array $applicantQueryVars = [])
    {
        global $l10n;

        if (is_array($l10n)) {
            /** @var \MO|\NOOP_Translations $mo */
            foreach ($l10n as $domain => $mo) {
                if ($mo instanceof \MO) {
                    $fileName = $mo->get_filename();
                    if ($fileName && (!isset($_POST['disable_theme']) || $_POST['disable_theme'] === "true") && strpos($fileName, 'languages/themes/') !== false) {
                        continue;
                    }
                }
                foreach ($mo->entries as $string => $entry) {
                    $this->l10n[$domain][$string] = $entry->translations[0];
                }
            }
        }

        return [
            'locale' => get_locale(),
            'translations' => $this->l10n
        ];
    }
}