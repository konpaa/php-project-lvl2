<?php

namespace Php\Project\Lvl2\parsers;

use Symfony\Component\Yaml\Yaml;

function parse($data, $type)
{
    $mapping = [
        'yml' => function ($data) {
            return Yaml::parse($data);
        },
        'json' => function ($data) {
            return json_decode($data, true);
        }
    ];
    if (isset($mapping[$type])) {
        return $mapping[$type]($data);
    }
    throw new \Exception("The '.{$type}' data type is not supported");
}
