<?php

namespace Wallace\Models\CssAnalyzer;

interface CssAnalyzerInterface
{
    // Should return an associative array where keys are the name of the metric
    // and the value is the result of the analysis:
    // [
    //    'filesize_raw': 98721,
    //    'total_selectors': 1280
    // ]
    public function analyze();
}
