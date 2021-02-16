<?php

namespace Php\Project\Lvl2\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseYaml(string $data): object
{
    return Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
}
