<?php

namespace Php\Project\Lvl2\Parsers;

function parseJson(string $data): object
{
    return json_decode($data);
}
