<?php

namespace Wallace;

use Wallace\Models\CssAnalyzer\CssAnalyzer;
use Wallace\Models\CssAnalyzer\CssImportAnalyzer;
use PHPUnit\Framework\TestCase;

class CssImportAnalyzerTest extends TestCase
{
    protected function setUp()
    {
        $css = file_get_contents('./test/fixtures/analyzer/imports.css');
        $analyzer = new CssAnalyzer($css, './temp');
        $this->analyzer = new CssImportAnalyzer($analyzer->imports);
    }

    protected function tearDown()
    {
        unset($this->analyzer);
    }

    public function testTotalImports()
    {
        $this->assertEquals(5, $this->analyzer->getTotalImports());
    }

    public function testUniqueImports()
    {
        $expected = [
            "@import 'duplicate.css';",
            "@import url(url-without-quotes.css);",
            "@import url('url-with-quotes.css');",
            "@import url('some-file.css') screen, print;",
        ];
        $actual = $this->analyzer->getUniqueImports();

        $this->assertEquals($expected, $actual);
        $this->assertCount(4, $actual);
    }

    public function testTotalUniqueImports()
    {
        $this->assertEquals(4, $this->analyzer->getTotalUniqueImports());
    }
}
