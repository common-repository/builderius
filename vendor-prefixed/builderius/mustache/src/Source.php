<?php

namespace Builderius\Mustache;

use Builderius\Mustache\Exception\RuntimeException;
/**
 * Mustache template Source interface.
 */
interface Source
{
    /**
     * Get the Source key (used to generate the compiled class name).
     *
     * This must return a distinct key for each template source. For example, an
     * MD5 hash of the template contents would probably do the trick. The
     * ProductionFilesystemLoader uses mtime and file path. If your production
     * source directory is under version control, you could use the current Git
     * rev and the file path...
     *
     * @throws RuntimeException when a source file cannot be read
     *
     * @return string
     */
    public function getKey();
    /**
     * Get the template Source.
     *
     * @throws RuntimeException when a source file cannot be read
     *
     * @return string
     */
    public function getSource();
}
