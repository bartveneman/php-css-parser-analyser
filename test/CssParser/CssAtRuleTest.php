<?php

namespace Wallace;

use PHPUnit\Framework\TestCase;

class CssAtRuleTest extends TestCase
{
    public function testInheritenceApi()
    {
        $this->assertTrue(method_exists('Wallace\Models\CssParser\CssAtRule', 'getRules'));
    }
}
