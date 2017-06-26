<?php

namespace Wallace\Controllers;

use Wallace\Models\Persistent\Import;

class ImportsController extends AbstractController
{
    const DEFAULT_LIMIT = 50;

    public function create($identifier, $css)
    {
        $import = Import::create([
            'identifier' => $identifier,
            'raw' => $css
        ]);
        $import = json_decode($import->to_json([
            'only' => ['created_at', 'metrics']
        ]));

        return $this->render($import);
    }

    public function show($identifier)
    {
        $all_imports = Import::all([
            'order'       => 'created_at ASC',
            'limit'       => self::DEFAULT_LIMIT,
            'conditions'  => [
                'identifier = ? AND created_at > DATE_SUB(now(), INTERVAL 3 MONTH)',
                $identifier
            ],
            'select'      => 'created_at, metrics',
        ]);
        $raw_metrics = [];
        $imports = [];

        foreach ($all_imports as $import) {
            foreach (json_decode($import->metrics) as $key => $value) {
                $raw_metrics[$key][] = $value;
            }

            $imports[] = [
                'created_at' => $import->created_at->format('atom'),
            ];
        }

        $metrics = [];
        foreach ($raw_metrics as $name => $values) {
            $metrics[] = [
                'name' => $name,
                'measurements' => $values,
            ];
        }

        return $this->render([
            'metrics' => $metrics,
            'imports' => $imports,
        ]);
    }
}
