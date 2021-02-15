<?php

namespace Php\Project\Lvl2\Formatters\Plain;

const INDENT = '  ';

use const Php\Project\Lvl2\Parsers\ST_KEEP;
use const Php\Project\Lvl2\Parsers\ST_NEW;
use const Php\Project\Lvl2\Parsers\ST_OLD;
use const Php\Project\Lvl2\Parsers\ST_CHANGE;
use const Php\Project\Lvl2\Parsers\ST_TEXT;

/**
 * @param mixed $value
 */
function toString($value): string
{
    if (is_object($value)) {
        return '[complex value]';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    return var_export($value, true);
}

function genPlainElem(array $elem, string $parent = ''): string
{
    if (array_key_exists('old', $elem)) {
        $old = toString($elem['old']);
    } else {
        $old = '';
    }
    if (array_key_exists('new', $elem)) {
        $new = toString($elem['new']);
    } else {
        $new = '';
    }
    switch ($elem['status']) {
        case ST_OLD:
            return 'Property \'' . $parent . ($parent == '' ? '' : '.') . $elem['key'] .
                '\' was removed';
        case ST_NEW:
            return 'Property \'' . $parent . ($parent == '' ? '' : '.') . $elem['key'] .
                '\' was added with value: ' . $new;
        case ST_CHANGE:
            return 'Property \'' . $parent . ($parent == '' ? '' : '.') . $elem['key'] .
                '\' was updated. From ' . $old . ' to ' . $new;
        case ST_KEEP:
        default:
            return '';
    }
}

function genPlain(array $childs, string $parent = ''): string
{
    $res = array_reduce($parent == '' ? $childs : $childs['child'], function ($acc, $elem) use ($parent): array {
        if (isset($elem['child'])) {
            return array_merge($acc, [genPlain($elem, $parent . ($parent == '' ? '' : '.') . $elem['key'])]);
        } else {
            return array_merge($acc, [genPlainElem($elem, $parent)]);
        }
    }, []);
    return implode(PHP_EOL, array_filter($res, fn($elem) => $elem != ''));
}
