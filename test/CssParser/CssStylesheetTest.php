<?php

namespace Wallace;

use Wallace\Models\CssParser\CssStylesheet;
use PHPUnit\Framework\TestCase;

class CssStylesheetTest extends TestCase
{
    public function testRules()
    {
        $css = new CssStylesheet('.css1 { color: blue } .css2 { color: red; }');

        $this->assertCount(2, $css->getRules());
        $this->assertContainsOnly('string', $css->getRules());
        $this->assertInternalType('array', $css->getRules());
    }

    public function testMediaQueries()
    {
        $css = new CssStylesheet('@media print {}');

        $this->assertCount(1, $css->getMediaQueries());
        $this->assertContainsOnly('string', $css->getMediaQueries());
        $this->assertInternalType('array', $css->getMediaQueries());
    }

    public function testKeyframes()
    {
        $css = new CssStylesheet('@keyframes id {}');

        $this->assertCount(1, $css->getKeyframes());
        $this->assertContainsOnly('string', $css->getKeyframes());
        $this->assertInternalType('array', $css->getKeyframes());
    }

    public function testImports()
    {
        $css = new CssStyleSheet('@import "stylesheet.css" screen;');

        $this->assertCount(1, $css->getImports());
        $this->assertContainsOnly('string', $css->getImports());
        $this->assertInternalType('array', $css->getImports());
    }
}
