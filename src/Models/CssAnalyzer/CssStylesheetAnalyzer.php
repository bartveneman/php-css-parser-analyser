<?php

namespace Wallace\Models\CssAnalyzer;

class CssStylesheetAnalyzer implements CssAnalyzerInterface
{

    const GZIP_EXTENSION = '.gz';
    const CSS_EXTENSION = '.css';
    const GZIP_COMPRESSION = 'w9';
    const TEMP_DIR = './temp';

    private $lowest_cohesion_selectors = [];
    private $lowest_cohesion = 0;

    private $raw;
    private $files_directory;
    private $rule_analyzer;
    private $selector_analyzer;
    private $declaration_analyzer;

    public function __construct(
        $raw,
        CssRuleAnalyzer $rule_analyzer,
        CssSelectorAnalyzer $selector_analyzer,
        CssDeclarationAnalyzer $declaration_analyzer
    ) {
        $this->raw = $raw;
        $this->files_directory = self::TEMP_DIR;
        $this->rule_analyzer = $rule_analyzer;
        $this->selector_analyzer = $selector_analyzer;
        $this->declaration_analyzer = $declaration_analyzer;
        $this->setLowestCohesion();
    }

    private function getFileName()
    {
        return md5($this->raw) . self::CSS_EXTENSION;
    }

    private function createPlainFile()
    {
        $filename = md5($this->raw);
        $file_path = $this->files_directory . $this->getFileName();

        if (!file_exists($file_path)) {
            file_put_contents($file_path, $this->raw, LOCK_EX);
        }

        return $file_path;
    }

    private function createGzipFile()
    {
        $file_path = $this->createPlainFile();
        $gz_file_path = $file_path . self::GZIP_EXTENSION;
        $fp = gzopen($gz_file_path, self::GZIP_COMPRESSION);
        gzwrite($fp, $this->raw);
        gzclose($fp);

        return $gz_file_path;
    }

    private function deleteFiles()
    {
        $file_path = $this->files_directory . $this->getFileName();

        if (file_exists($file_path)) {
            unlink($file_path);
        }

        if (file_exists($file_path . self::GZIP_EXTENSION)) {
            unlink($file_path . self::GZIP_EXTENSION);
        }
    }

    public function getFilesizeRaw()
    {
        $filesize = filesize($this->createPlainFile());
        $this->deleteFiles();

        return $filesize;
    }

    public function getFilesizeGzip()
    {
        $filesize = filesize($this->createGzipFile());
        $this->deleteFiles();

        return $filesize;
    }

    public function getFilesizeCompressionRatio()
    {
        $raw = $this->getFilesizeRaw();
        $gzip = $this->getFilesizeGzip();

        if ($raw == 0) {
            return 0; // Catch DivideByZeroException
        }

        $compression = 1 - ($gzip / $raw);
        return $compression > 0 ? $compression : 0;
    }

    public function getSimplicity()
    {
        $rules = $this->rule_analyzer->getTotalRules();
        $selectors = $this->selector_analyzer->getTotalSelectors();

        if ($selectors === 0) {
            return 0; // Catch DivideByZeroException
        }

        return $rules / $selectors;
    }

    public function getAverageCohesion()
    {
        $rules = $this->rule_analyzer->getTotalRules();
        $declarations = $this->declaration_analyzer->getTotalDeclarations();

        if ($declarations === 0) {
            return 0; // Catch DivideByZeroException
        }

        return $declarations / $rules;
    }

    public function getLowestCohesion()
    {
        return $this->lowest_cohesion;
    }

    private function setLowestCohesion()
    {
        foreach ($this->rule_analyzer->getRules() as $rule) {
            $count = count($rule->getDeclarations());

            if ($count > $this->lowest_cohesion) {
                $this->lowest_cohesion = $count;
                $this->lowest_cohesion_selectors = $rule->getSelectors();
            } elseif ($count === $this->lowest_cohesion) {
                $this->lowest_cohesion_selectors = array_merge(
                    $this->lowest_cohesion_selectors,
                    $rule->getSelectors()
                );
            }
        }
    }

    public function getLowestCohesionSelectors()
    {
        return array_values(
            array_unique(
                $this->lowest_cohesion_selectors
            )
        );
    }

    public function analyze()
    {
        return [
            'filesize_raw' => $this->getFilesizeRaw(),
            'filesize_gzip' => $this->getFilesizeGzip(),
            'filesize_compression_ratio' => $this->getFilesizeCompressionRatio(),
            'simplicity' => $this->getSimplicity(),
            'average_cohesion' => $this->getAverageCohesion(),
            'lowest_cohesion' => $this->getLowestCohesion(),
            'lowest_cohesion_selectors' => $this->getLowestCohesionSelectors()
        ];
    }
}
