<?php

namespace Wallace;

use Wallace\Models\CssParser\CssParser;
use Wallace\Models\CssParser\CssRule;
use Wallace\Models\CssParser\CssMediaQuery;
use Wallace\Models\CssParser\CssKeyframe;
use Wallace\Models\CssParser\CssSelector;
use Wallace\Models\CssParser\CssDeclaration;
use Wallace\Models\CssParser\CssImport;
use PHPUnit\Framework\TestCase;

class CssParserTest extends TestCase
{
    protected function setUp()
    {
        $css = file_get_contents('./test/fixtures/parser/parser.css');
        $this->parser = new CssParser($css);
    }

    protected function tearDown()
    {
        unset($this->parser);
    }

    public function testGetRules()
    {
        $rules = $this->parser->getRules();
        $this->assertInternalType('array', $rules);
        $this->assertContainsOnlyInstancesOf(CssRule::class, $rules);
    }

    public function testGetMediaQueries()
    {
        $mqs = $this->parser->getMediaQueries();
        $this->assertInternalType('array', $mqs);
        $this->assertContainsOnlyInstancesOf(CssMediaQuery::class, $mqs);
    }

    public function testGetKeyframes()
    {
        $keyframes = $this->parser->getKeyframes();
        $this->assertInternalType('array', $keyframes);
        $this->assertContainsOnlyInstancesOf(CssKeyframe::class, $keyframes);
    }

    public function testGetSelectors()
    {
        $selectors = $this->parser->getSelectors();
        $this->assertInternalType('array', $selectors);
        $this->assertContainsOnlyInstancesOf(CssSelector::class, $selectors);
    }

    public function testGetDeclarations()
    {
        $declarations = $this->parser->getDeclarations();
        $this->assertInternalType('array', $declarations);
        $this->assertContainsOnlyInstancesOf(CssDeclaration::class, $declarations);
    }

    public function testGetImports()
    {
        $imports = $this->parser->getImports();
        $this->assertInternalType('array', $imports);
        $this->assertContainsOnlyInstancesOf(CssImport::class, $imports);
    }
}
