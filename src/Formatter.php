<?php

namespace Php\Project\Lvl2\Formatter;

use Error;

use function Php\Project\Lvl2\Formatters\Pretty\format as formatPretty;
use function Php\Project\Lvl2\Formatters\Json\format as formatJson;
use function Php\Project\Lvl2\Formatters\Plain\format as formatPlain;

function format(array $tree, string $type): string
{
    switch ($type) {
        case 'pretty':
            return formatPretty($tree);
        case 'plain':
            return formatPlain($tree);
        case 'json':
            return formatJson($tree);
        default:
            throw new Error("unknown format: {$type}");
    }
}
