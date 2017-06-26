<?php

namespace Wallace\Models\CssParser;

abstract class CssAtRule
{
    const OPENING_PARANTHESIS = '{';
    const CLOSING_PARANTHESIS = '}';

    protected $raw;

    public function __construct($raw)
    {
        $this->raw = $raw;
    }

    public function getRules()
    {
        $rules = [];
        $depth = 0;
        $rule = [];
        $tokens = str_split($this->raw);

        foreach ($tokens as $token) {
            if ($depth > 0) {
                $rule[] = $token;
            }

            if ($token === self::OPENING_PARANTHESIS) {
                $depth += 1;
            }

            if ($token === self::CLOSING_PARANTHESIS) {
                $depth -= 1;
            }

            if ($depth === 1 && $token === self::CLOSING_PARANTHESIS) {
                $rules[] = trim(implode($rule, ''));
                $rule = [];
            }
        }

        return $rules;
    }
}
