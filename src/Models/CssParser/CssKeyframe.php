<?php

namespace Wallace\Models\CssParser;

class CssKeyframe extends CssAtRule
{
    const KEYFRAMES_REGEX = '/@(?:-webkit-|-moz-|-o-)?keyframes/';
    const IDENTIFIER_REGEX = '/@(?:-webkit-|-moz-|-o-)?keyframes\s+([a-z0-9-_]+)/i';
    const IS_PREFIXED_REGEX = '/@-(?:webkit|moz|o)-/i';

    public function getIdentifier()
    {
        preg_match(self::IDENTIFIER_REGEX, $this->raw, $matches);

        return $matches[1];
    }

    public function isPrefixed()
    {
        return preg_match(self::IS_PREFIXED_REGEX, $this->raw) === 1;
    }
}
