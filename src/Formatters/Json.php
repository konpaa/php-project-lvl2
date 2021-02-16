<?php

namespace Php\Project\Lvl2\Formatters\Json;

function render(array $data): string
{
    return (string) json_encode($data, JSON_PRETTY_PRINT);
}
