<?php

namespace Wallace;

use Wallace\Models\CssAnalyzer\CssAnalyzer;
use Wallace\Models\CssAnalyzer\CssSupportsRuleAnalyzer;
use PHPUnit\Framework\TestCase;

class CssSupportsRuleAnalyzerTest extends TestCase
{
    protected function setUp()
    {
        $css = file_get_contents('./test/fixtures/analyzer/supports.css');
        $analyzer = new CssAnalyzer($css, './temp');
        $this->analyzer = new CssSupportsRuleAnalyzer($analyzer->supports_rules);
    }

    protected function tearDown()
    {
        unset($this->analyzer);
    }

    public function testTotalSupportsRules()
    {
        $this->assertEquals(4, $this->analyzer->getTotalRules());
    }

    public function testUniqueSupportsRules()
    {
        $expected = [
            '@supports (filter: blur(5px))',
            '@supports (display: table-cell) and (display: list-item)',
            '@supports (-webkit-appearance:none)',
        ];
        $this->assertEquals($expected, $this->analyzer->getUniqueRules());
    }

    public function testTotalBrowserHacks()
    {
        $this->assertEquals(2, $this->analyzer->getTotalBrowserHacks());
    }

    public function testBrowserHacks()
    {
        $expected = [
            '@supports (-webkit-appearance:none)',
        ];
        $actual = $this->analyzer->getUniqueBrowserHacks();

        $this->assertEquals($expected, $actual);
    }
}
