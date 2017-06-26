<?php

namespace Wallace;

use Wallace\Models\CssAnalyzer\CssAnalyzer;
use Wallace\Models\CssAnalyzer\CssRuleAnalyzer;
use PHPUnit\Framework\TestCase;

class CssRuleAnalyzerTest extends TestCase
{

    protected function setUp()
    {
        $css = file_get_contents('./test/fixtures/analyzer/rules.css');
        $analyzer = new CssAnalyzer($css, './temp');
        $this->analyzer = new CssRuleAnalyzer($analyzer->rules);
    }

    protected function tearDown()
    {
        unset($this->analyzer);
    }

    public function testTotalRules()
    {
        $expected = 3;
        $actual = $this->analyzer->getTotalRules();

        $this->assertEquals($expected, $actual);
    }
}
