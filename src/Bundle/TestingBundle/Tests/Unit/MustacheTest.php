<?php

namespace Builderius\Bundle\TestingBundle\Tests\Unit;

use Builderius\Mustache\Engine;
use PHPUnit\Framework\TestCase;

class MustacheTest extends TestCase
{
    public function testMustahe()
    {
        $m = new Engine();
        $result = $m->render('Hello, {{planet}}!', ['planet' => 'World']); // "Hello, World!"
        $r = $result;
    }
}