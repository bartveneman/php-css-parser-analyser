<?php

namespace Wallace\Models\CssParser;

class CssSupportsRule extends CssAtRule
{
    const SUPPORTS_REGEX = '/@supports\s*([^{])/i';
    const CONDITION_REGEX = '/@supports(?<condition>[^{]+)/i';
    const BROWSER_HACKS_REGEX = [
    '/-webkit-appearance\s*:\s*none/i',
    '/-moz-appearance\s*:\s*meterbar/i'
    ];

    public function isBrowserHack()
    {
        foreach (self::BROWSER_HACKS_REGEX as $regex) {
            if (preg_match($regex, $this->getCondition()) === 1) {
                return true;
            }
        }
        return false;
    }

    public function getCondition()
    {
        preg_match(self::CONDITION_REGEX, $this->raw, $match);

        return trim($match['condition']);
    }

    public function __toString()
    {
        return '@supports ' . $this->getCondition();
    }
}
