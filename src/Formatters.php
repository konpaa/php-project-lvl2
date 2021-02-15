<?php

namespace Php\Project\Lvl2\Formatters;

use function Php\Project\Lvl2\Formatters\Json\genJson;
use function Php\Project\Lvl2\Formatters\Plain\genPlain;
use function Php\Project\Lvl2\Formatters\Stylish\genStylish;

const FM_JSON = 'json';
const FM_PLAIN = 'plain';
const FM_STYLISH = 'stylish';

function formatDiff(array $diff, string $format): string
{
    switch ($format) {
        case FM_PLAIN:
            return genPlain($diff);
        case FM_JSON:
            return genJson($diff);
        case FM_STYLISH:
        default:
            return genStylish($diff);
    }
}
