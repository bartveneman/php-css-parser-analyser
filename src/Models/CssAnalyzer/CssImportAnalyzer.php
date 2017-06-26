<?php

namespace Wallace\Models\CssAnalyzer;

class CssImportAnalyzer implements CssAnalyzerInterface
{
    private $imports;

    public function __construct(array $imports)
    {
        $this->imports = $imports;
    }

    public function getTotalImports()
    {
        return count($this->imports);
    }

    public function getUniqueImports()
    {
        return array_values(
            array_unique(
                array_map(function ($import) {
                    return (string)$import;
                }, $this->imports)
            )
        );
    }

    public function getTotalUniqueImports()
    {
        return count($this->getUniqueImports());
    }

    public function analyze()
    {
        return [
            'total_imports' => $this->getTotalImports(),
            'unique_imports' => $this->getUniqueImports(),
            'total_unique_imports' => $this->getTotalUniqueImports(),
        ];
    }
}
