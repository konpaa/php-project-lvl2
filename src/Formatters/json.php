<?php

namespace Php\Project\Lvl2\Formatters\json;

function render($data)
{
    return json_encode($data, JSON_PRETTY_PRINT);
}
