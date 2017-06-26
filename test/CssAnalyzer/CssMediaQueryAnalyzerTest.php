<?php

namespace Wallace;

use Wallace\Models\CssAnalyzer\CssAnalyzer;
use Wallace\Models\CssAnalyzer\CssMediaQueryAnalyzer;
use PHPUnit\Framework\TestCase;

class CssMediaQueryAnalyzerTest extends TestCase
{
  /**
   * Setup and Teardown
   */
    protected function setUp()
    {
        $css = file_get_contents('./test/fixtures/analyzer/media_queries.css');
        $analyzer = new CssAnalyzer($css, './temp');
        $this->analyzer = new CssMediaQueryAnalyzer($analyzer->media_queries);
    }

    protected function tearDown()
    {
        unset($this->analyzer);
    }

    public function testTotalMediaQueries()
    {
        $actual = $this->analyzer->getTotalMediaQueries();
        $expected = 21;

        $this->assertEquals($expected, $actual);
    }

    public function testUniqueMediaQueries()
    {
        $expected = [
            '@-moz-document url-prefix()',
            '@media (max-width: 200px)',
            '@media (min-resolution: .001dpcm)',
            '@media (min-width: 20px)',
            '@media all and (-webkit-min-device-pixel-ratio:0) and (min-resolution: .001dpcm)',
            '@media all and (min--moz-device-pixel-ratio:0) and (min-resolution: 3e1dpcm)',
            '@media print',
            '@media screen',
            '@media screen and (-moz-images-in-menus:0)',
            '@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none)',
            '@media screen and (min--moz-device-pixel-ratio:0)',
            '@media screen and (min-width: 0\0)',
            '@media screen and (min-width: 33em)',
            '@media screen or print',
            '@media screen\9',
            '@media \0 all',
            '@media \0screen',
            '@media \0screen\,screen\9',
        ];
        $actual = $this->analyzer->getUniqueMediaQueries();

        $this->assertEquals($expected, $actual);
        $this->assertContainsOnly('string', $actual);
    }

    public function testTotalUniqueMediaQueries()
    {
        $this->assertEquals(18, $this->analyzer->getTotalUniqueMediaQueries());
    }

    public function testBrowserHacks()
    {
        $expected = [
            '@media screen\9',
            '@media \0screen\,screen\9',
            '@media \0screen',
            '@media screen and (min-width: 0\0)',
            '@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none)',
            '@-moz-document url-prefix()',
            '@media all and (-webkit-min-device-pixel-ratio:0) and (min-resolution: .001dpcm)',
            '@media \0 all',
            '@media screen and (-moz-images-in-menus:0)',
            '@media screen and (min--moz-device-pixel-ratio:0)',
            '@media all and (min--moz-device-pixel-ratio:0) and (min-resolution: 3e1dpcm)',
            '@media (min-resolution: .001dpcm)',
        ];
        $actual = $this->analyzer->getBrowserHacks();

        $this->assertEquals($expected, $actual);
        $this->assertContainsOnly('string', $actual);
    }

    public function testTotalBrowserHacks()
    {
        $this->assertEquals(13, $this->analyzer->getTotalBrowserHacks());
    }
}
