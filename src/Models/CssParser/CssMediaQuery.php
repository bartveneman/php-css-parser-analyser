<?php

namespace Wallace\Models\CssParser;

class CssMediaQuery extends CssAtRule
{
    const MEDIA_QUERY_REGEX = '/@(?:media\s*(?:.+?)|-moz-document\s+url-prefix\(\)\s*){/';
    const MEDIA_QUERY_OPENING_PARENTHESIS_REGEX = '/\s*{\s*/';
    const EMPTY_STRING = '';
    const BROWSER_HACK_REGEXES = [
    '/screen\\\\9/i',
    '/\\\\0screen/i',
    '/screen\s+and\s*\(min-width\s*\:\s*0\\\\0\)$/i',
    '/screen\s+and\s*\(-ms-high-contrast\s*\:\s*active\),\s*\(-ms-high-contrast\s*\:\s*none\)$/i',
    '/-moz-document\s+url-prefix\(\)/i',
    '/all\s+and\s*\(-webkit-min-device-pixel-ratio\s*:\s*0\)\s+and\s*\(min-resolution\s*:\s*\.001dpcm\)/i',
    '/\\\\0\s+all/',
    '/\(min-resolution\s*:\s*\.001dpcm\)/',
    '/min--moz-device-pixel-ratio\s*:\s*0/',
    '/screen\s+and\s+\(-moz-images-in-menus\s*:\s*0\)/',
    ];

  /**
   * https://github.com/katiefenn/parker/blob/master/lib/CssMediaQuery.js#L9
   */
    public function getQueries()
    {
        preg_match_all(self::MEDIA_QUERY_REGEX, $this->raw, $results);
        $queries = [];

        foreach ($results[0] as $result) {
            $queries[] = trim(preg_replace(self::MEDIA_QUERY_OPENING_PARENTHESIS_REGEX, self::EMPTY_STRING, $result));
        }

        return $queries;
    }

    public function isBrowserHack()
    {
        foreach ($this->getQueries() as $query) {
            foreach (self::BROWSER_HACK_REGEXES as $regex) {
                if (preg_match($regex, $query)) {
                    return true;
                }
            }
        }

        return false;
    }
}
