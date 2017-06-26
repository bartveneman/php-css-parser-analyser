<?php

namespace Wallace\Models\CssAnalyzer;

use Wallace\Models\CssParser\CssSupportsRule;

class CssSupportsRuleAnalyzer implements CssAnalyzerInterface
{
    private $rules;

    private $hacks = [];

    public function __construct(array $rules)
    {
        $this->rules = $rules;
        $this->hacks = $this->getBrowserHacks();
    }

    public function getTotalRules()
    {
        return count($this->rules);
    }

    public function getUniqueRules()
    {
        return array_values(
            array_unique(
                array_map(
                    function ($rule) {
                        return (string)$rule;
                    },
                    $this->rules
                )
            )
        );
    }

    public function getTotalUniqueRules()
    {
        return count($this->getUniqueRules());
    }

    private function getBrowserHacks()
    {
        return array_values(array_filter($this->rules, function ($rule) {
            if ($rule->isBrowserHack()) {
                return (string)$rule;
            }
        }));
    }

    public function getUniqueBrowserHacks()
    {
        return array_values(
            array_unique(
                $this->hacks
            )
        );
    }

    public function getTotalBrowserHacks()
    {
        return count($this->hacks);
    }

    public function analyze()
    {
        return [
            'total_supports_rules' => $this->getTotalRules(),
            'unique_supports_rules' => $this->getUniqueRules(),
            'total_unique_supports_rules' => $this->getTotalUniqueRules(),
            'browser_supports_rule_hacks' => $this->getUniqueBrowserHacks(),
            'total_browser_supports_rule_hacks' => $this->getTotalBrowserHacks(),
        ];
    }
}
