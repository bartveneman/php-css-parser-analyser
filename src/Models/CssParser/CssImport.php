<?php

namespace Wallace\Models\CssParser;

/**
 * Regex https://regex101.com/r/iebPYG/1
 */
class CssImport
{
  // Regex group 1: url
  // Regex group 2: media query (optional)
    const IMPORT_REGEX = '/@import\s*([^;\s]+)\s*([^;]*)/';
    const IMPORT_MEDIA_QUERY_REGEX = '/@import\s*[^;]+\s[^;]+/';

    private $raw;

    public function __construct($raw)
    {
        $this->raw = trim($raw);
    }

    public function getUrl()
    {
        preg_match(self::IMPORT_REGEX, $this->raw, $matches);

        return trim($matches[1]);
    }

    public function getMediaQuery()
    {
        if (!$this->hasMediaQuery()) {
            return false;
        }

        preg_match(self::IMPORT_REGEX, $this->raw, $matches);

        return trim($matches[2]);
    }

    public function hasMediaQuery()
    {
        return preg_match(self::IMPORT_MEDIA_QUERY_REGEX, $this->raw) === 1;
    }

    public function __toString()
    {
        return $this->raw;
    }
}
