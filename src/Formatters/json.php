<?php

namespace Php\Project\Lvl2\Formatters\json;

function format($ast)
{
    return json_encode($ast, JSON_PRETTY_PRINT);
}
