<?php

namespace Wallace\Models\CssAnalyzer;

class CssRuleAnalyzer implements CssAnalyzerInterface
{
    public function __construct($rules)
    {
        $this->rules = $rules;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function getTotalRules()
    {
        return count($this->rules);
    }

    public function analyze()
    {
        return [
            'total_rules' => $this->getTotalRules()
        ];
    }
}
