<?php

namespace Wallace\Models\Persistent;

use ActiveRecord\Model;
use Wallace\Models\CssAnalyzer\CssAnalyzer;

class Import extends Model
{

    public static $validates_presence_of = [
        ['metrics'],
        ['raw'],
        ['identifier'],
    ];

    public static $before_validation_on_create = ['analyze'];

    public function analyze()
    {
        $analyzer = new CssAnalyzer($this->raw);
        $analysis = $analyzer->analyze();

        $this->metrics = json_encode($analysis);
    }
}
