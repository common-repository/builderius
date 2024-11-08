<?php

namespace Builderius\Mustache\Cache;

use Builderius\Mustache\Logger;
/**
 * Mustache Cache in-memory implementation.
 *
 * The in-memory cache is used for uncached lambda section templates. It's also useful during development, but is not
 * recommended for production use.
 */
class NoopCache extends \Builderius\Mustache\Cache\AbstractCache
{
    /**
     * Loads nothing. Move along.
     *
     * @param string $key
     *
     * @return bool
     */
    public function load($key)
    {
        return \false;
    }
    /**
     * Loads the compiled Mustache Template class without caching.
     *
     * @param string $key
     * @param string $value
     */
    public function cache($key, $value)
    {
        $this->log(\Builderius\Mustache\Logger::WARNING, 'Template cache disabled, evaluating "{className}" class at runtime', array('className' => $key));
        eval('?>' . $value);
    }
}
