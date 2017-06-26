<?php

namespace Wallace\Models\CssAnalyzer;

use Wallace\Models\CssParser\CssMediaQuery;

class CssMediaQueryAnalyzer implements CssAnalyzerInterface
{
    const EMPTY_STRING = '';

    private $media_objects;
    private $media_queries;
    private $unique_media_queries;

    public function __construct(array $media_queries)
    {
        $this->setMediaQueries($media_queries);
        $this->setBrowserHacks();
    }

    private function setMediaQueries(array $media_queries)
    {
        $queries = [];

        foreach ($media_queries as $media_query) {
            $q = $media_query->getQueries();

            foreach ($q as $query) {
                if ($query !== self::EMPTY_STRING) {
                    $queries[] = $query;
                }
            }
        }

        usort($queries, function ($a, $b) {
            return strnatcasecmp($a, $b);
        });

        $this->media_objects = $media_queries;
        $this->media_queries = $queries;
        $this->unique_media_queries = array_values(array_unique($queries));

        return $this;
    }

    public function getTotalMediaQueries()
    {
        return count($this->media_queries);
    }

    public function getUniqueMediaQueries()
    {
        return $this->unique_media_queries;
    }

    public function getTotalUniqueMediaQueries()
    {
        return count($this->unique_media_queries);
    }

    private function setBrowserHacks()
    {
        $browser_hacks = [];

        foreach ($this->media_objects as $media_query) {
            if ($media_query->isBrowserHack()) {
                foreach ($media_query->getQueries() as $query) {
                    $browser_hacks[] = $query;
                }
            }
        }

        $this->browser_hacks = $browser_hacks;
        return $this;
    }

    public function getBrowserHacks()
    {
        return array_values(
            array_unique(
                $this->browser_hacks
            )
        );
    }

    public function getTotalBrowserHacks()
    {
        return count($this->browser_hacks);
    }

    public function analyze()
    {
        return [
            'total_media_queries' => $this->getTotalMediaQueries(),
            'unique_media_queries' => $this->getUniqueMediaQueries(),
            'total_unique_media_queries' => $this->getTotalUniqueMediaQueries(),
            'browser_media_query_hacks' => $this->getBrowserHacks(),
            'total_browser_media_query_hacks' => $this->getTotalBrowserHacks()
        ];
    }
}
