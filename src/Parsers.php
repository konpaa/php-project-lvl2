<?php

namespace Php\Project\Lvl2\Parsers;

use function Php\Project\Lvl2\Parsers\parseJson as Json;
use function Php\Project\Lvl2\Parsers\parseYam as Yaml;

function parseData(string $data, string $parserType): object
{
    $parsers = [
        'json' => fn($data) => Json($data),
        'yaml' => fn($data) => Yaml($data),
        'yml' => fn($data) => Yaml($data)
    ];

    if (!array_key_exists($parserType, $parsers)) {
        throw new \Exception("Unsupported parser type: {$parserType}");
    }

    return $parsers[$parserType]($data);
}
