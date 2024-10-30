<?php

namespace Builderius\Bundle\TestingBundle\Tests\Unit;

use Builderius\Bundle\TemplateBundle\Merge\BuilderiusMerger;
use Builderius\PhpMerge\MergeConflict;
use Builderius\PhpMerge\MergeException;
use Builderius\PhpMerge\PhpMerge;
use PHPUnit\Framework\TestCase;

class BuilderiusMergerTest extends TestCase
{
    public function testMerge()
    {
        /*$a = $b = $original = [
            'person' => ['first_name' => 'Marge', 'last_name' => 'Bouvier'],
            'hobby' => ['type' => 'bowling', 'rank' => 'novice'],
        ];
        $a['person']['last_name'] = 'Simpson';
        //$b['person']['last_name'] = 'Bart';
        $b['hobby']['rank'] = 'champion';
        //$result = BuilderiusMerger::merge($original, $b, $a);*/

        $merger = new PhpMerge();
        $original = <<<'EOD'
unchanged
replaced
unchanged
normal
unchanged
unchanged
removed

EOD;
        $version2 = <<<'EOD'
unchanged
replaced
unchanged
normal??
unchanged
unchanged

EOD;
        $conflicting = <<<'EOD'
unchanged
replaced
unchanged
normal!!
unchanged
unchanged

EOD;
        try {
            $result = $merger->merge($original, $version2, $conflicting);
        } catch (MergeException $exception) {
            /** @var MergeConflict[] $conflicts */
            $conflicts = $exception->getConflicts();

            $original_lines = $conflicts[0]->getBase();
            // $original_lines === ["normal\n"];

            $version2_lines = $conflicts[0]->getRemote();
            // $version2_lines === ["normal??\n"];

            $conflicting_lines = $conflicts[0]->getLocal();
            // $conflicting_lines === ["normal!!\n"];

            $line_numer_of_conflict = $conflicts[0]->getBaseLine();
            // $line_numer_of_conflict === 3; // Count starts with 0.

            // It is also possible to get the merged version using the first version
            // to resolve conflicts.
            $merged = $exception->getMerged();
            // $merged === $version2;
            // In this case, but in general there could be non-conflicting changes.

            $line_in_merged = $conflicts[0]->getMergedLine();
            // $line_in_merged === 3; // Count starts with 0.
        }
    }
}