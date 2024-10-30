<?php

declare (strict_types=1);
namespace Builderius\GraphQL\Experimental\Executor;

use Builderius\GraphQL\Type\Definition\ObjectType;
use Builderius\GraphQL\Type\Definition\ResolveInfo;
/**
 * @internal
 */
class CoroutineContext
{
    /** @var CoroutineContextShared */
    public $shared;
    /** @var ObjectType */
    public $type;
    /** @var mixed */
    public $value;
    /** @var object */
    public $result;
    /** @var string[] */
    public $path;
    /** @var ResolveInfo */
    public $resolveInfo;
    /** @var string[]|null */
    public $nullFence;
    /**
     * @param mixed         $value
     * @param object        $result
     * @param string[]      $path
     * @param string[]|null $nullFence
     */
    public function __construct(\Builderius\GraphQL\Experimental\Executor\CoroutineContextShared $shared, \Builderius\GraphQL\Type\Definition\ObjectType $type, $value, $result, array $path, ?array $nullFence = null)
    {
        $this->shared = $shared;
        $this->type = $type;
        $this->value = $value;
        $this->result = $result;
        $this->path = $path;
        $this->nullFence = $nullFence;
    }
}
