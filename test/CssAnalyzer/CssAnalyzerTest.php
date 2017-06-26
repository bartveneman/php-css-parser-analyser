<?php

namespace Wallace;

use Wallace\Models\CssAnalyzer\CssAnalyzer;
use PHPUnit\Framework\TestCase;

class CssAnalyzerTest extends TestCase
{
    public function testConstructor()
    {
        new CssAnalyzer('.some-css {}');
        $this->addToAssertionCount(1);
    }
}
