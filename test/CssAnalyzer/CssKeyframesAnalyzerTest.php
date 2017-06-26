<?php

namespace Wallace;

use Wallace\Models\CssAnalyzer\CssAnalyzer;
use Wallace\Models\CssAnalyzer\CssKeyframesAnalyzer;
use PHPUnit\Framework\TestCase;

class CssKeyframesAnalyzerTest extends TestCase
{
    protected function setUp()
    {
        $css = file_get_contents('./test/fixtures/analyzer/keyframes.css');
        $analyzer = new CssAnalyzer($css, './temp');
        $this->analyzer = new CssKeyframesAnalyzer($analyzer->keyframes);
    }

    protected function tearDown()
    {
        unset($this->analyzer);
    }

    public function testUniqueKeyframes()
    {
        $expected = [
            'NAME-YOUR-ANIMATION',
            'fontbulger'
        ];

        $this->assertEquals($expected, $this->analyzer->getUniqueKeyframes());
        $this->assertCount(2, $expected);
        $this->assertContainsOnly('string', $expected);

        $this->assertEquals(2, $this->analyzer->getTotalUniqueKeyframes());
    }

    public function testTotalKeyframes()
    {
        $this->assertEquals(5, $this->analyzer->getTotalKeyframes());
    }
}
