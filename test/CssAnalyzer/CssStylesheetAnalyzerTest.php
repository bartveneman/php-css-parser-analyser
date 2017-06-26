<?php

namespace Wallace;

use Wallace\Models\CssAnalyzer\CssAnalyzer;
use Wallace\Models\CssAnalyzer\CssStylesheetAnalyzer;
use PHPUnit\Framework\TestCase;

class CssStylesheetAnalyzerTest extends TestCase
{

    protected function setUp()
    {
        $css = file_get_contents('./test/fixtures/analyzer/stylesheet.css');
        $analyzer = new CssAnalyzer($css, './temp');
        $this->analyzer = new CssStylesheetAnalyzer(
            $css,
            $analyzer->rule_analyzer,
            $analyzer->selector_analyzer,
            $analyzer->declaration_analyzer
        );
    }

    protected function tearDown()
    {
        unset($this->analyzer);
    }

    public function testSimplicity()
    {
        $expected = 5 / 6; // rules / selectors
        $actual = $this->analyzer->getSimplicity();

        $this->assertEquals($expected, $actual);
    }

    public function testFileSizeRaw()
    {
        $expected = 714;
        $actual = $this->analyzer->getFilesizeRaw();

        $this->assertEquals($expected, $actual);
    }

    public function testFileSizeGzip()
    {
        $expected = 233;
        $actual = $this->analyzer->getFilesizeGzip();

        $this->assertEquals($expected, $actual);
    }

    public function testFileSizeCompressionRatio()
    {
        $expected = 1 - 233/714;
        $actual = $this->analyzer->getFilesizeCompressionRatio();

        $this->assertEquals($expected, $actual);
    }

    public function testLowestCohesion()
    {
        $expected = 7;
        $actual = $this->analyzer->getLowestCohesion();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @depends testLowestCohesion
     */
    public function testLowestCohesionSelectors()
    {
        $expected = [
        '.lowest-cohesion',
        '.equal-low-cohesion'
        ];
        $actual = $this->analyzer->getLowestCohesionSelectors();

        $this->assertEquals($expected, $actual);
    }

    public function testAverageCohesion()
    {
        $expected = 27 / 5; // declarations / rules
        $actual = $this->analyzer->getAverageCohesion();

        $this->assertEquals($expected, $actual);
    }
}
