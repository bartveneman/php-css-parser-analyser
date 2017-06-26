<?php

namespace Wallace\Models\CssAnalyzer;

use Wallace\Models\CssParser\CssParser;
use Wallace\Models\CssParser\CssSelector;

class CssAnalyzer implements CssAnalyzerInterface
{
    public function __construct($raw)
    {
        $parser = new CssParser($raw);
        $this->raw = $raw;

        $this->rules = $parser->getRules();
        $this->media_queries = $parser->getMediaQueries();
        $this->keyframes = $parser->getKeyframes();
        $this->selectors = $parser->getSelectors();
        $this->declarations = $parser->getDeclarations();
        $this->imports = $parser->getImports();
        $this->supports_rules = $parser->getSupportsRules();

        $this->media_query_analyzer = new CssMediaQueryAnalyzer($this->media_queries);
        $this->keyframes_analyzer = new CssKeyframesAnalyzer($this->keyframes);
        $this->rule_analyzer = new CssRuleAnalyzer($this->rules);
        $this->selector_analyzer = new CssSelectorAnalyzer($this->selectors);
        $this->declaration_analyzer = new CssDeclarationAnalyzer($this->declarations);
        $this->import_analyzer = new CssImportAnalyzer($this->imports);
        $this->supports_rules_analyzer = new CssSupportsRuleAnalyzer($this->supports_rules);
        $this->stylesheet_analyzer = new CssStylesheetAnalyzer(
            $this->raw,
            $this->rule_analyzer,
            $this->selector_analyzer,
            $this->declaration_analyzer
        );
    }

    public function analyze()
    {
        return array_merge(
            $this->stylesheet_analyzer->analyze(),
            $this->media_query_analyzer->analyze(),
            $this->keyframes_analyzer->analyze(),
            $this->rule_analyzer->analyze(),
            $this->selector_analyzer->analyze(),
            $this->declaration_analyzer->analyze(),
            $this->import_analyzer->analyze(),
            $this->supports_rules_analyzer->analyze(),
            [
                'browser_hacks' => array_merge(
                    $this->media_query_analyzer->getBrowserHacks(),
                    $this->selector_analyzer->getBrowserHacks(),
                    $this->declaration_analyzer->getUniqueBrowserHacks(),
                    $this->supports_rules_analyzer->getUniqueBrowserHacks()
                ),
                'total_browser_hacks' => array_sum([
                    $this->media_query_analyzer->getTotalBrowserHacks(),
                    $this->selector_analyzer->getTotalBrowserHacks(),
                    $this->declaration_analyzer->getTotalBrowserHacks(),
                    $this->supports_rules_analyzer->getTotalBrowserHacks()
                ])
            ]
        );
    }
}
