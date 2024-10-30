<?php

namespace Builderius\Bundle\ExpressionLanguageBundle;

class SafeCallable
{
    protected $callback;

    /**
     * Constructor.
     *
     * @param callable $callback The target callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Call the callback with the provided arguments and returns result.
     *
     * @return mixed
     */
    public function call()
    {
        return $this->callArray(func_get_args());
    }

    /**
     * Call the callback with the provided arguments and returns result.
     *
     * @param array $arguments
     *
     * @return mixed
     */
    public function callArray(array $arguments)
    {
        $callback = $this->getCallback();

        return count($arguments)
            ? call_user_func_array($callback, $arguments)
            : $callback();
    }

    /**
     * @throws \Exception
     */
    public function __invoke()
    {
        throw new \Exception('Callback wrapper cannot be invoked, use $wrapper->getCallback() instead.');
    }
}