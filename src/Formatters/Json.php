<?php

namespace Php\Project\Lvl2\Formatters\json;

function format(array $tree): string
{
    return json_encode($tree);
}
