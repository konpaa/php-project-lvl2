<?php

namespace Differ\Parsers;

function parseJson(string $data): object
{
    return json_decode($data);
}
