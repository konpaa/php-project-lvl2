<?php

namespace Php\Project\Lvl2\Formatters\Stylish;

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
    if (is_string($value)) {
        return $value;
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    return var_export($value, true);
}

function genStylishObject(string $objKey, object $objElem, int $lvl = 0, bool $st = false): string
{
    $keys = array_keys(get_object_vars($objElem));
    $res = array_reduce($keys, function ($acc, $key) use ($objElem, $lvl): array {
        if (is_object($objElem->$key)) {
            return array_merge($acc, [genStylishObject($key, $objElem->$key, $lvl + 1)]);
        } else {
            return array_merge($acc, [str_repeat(INDENT, $lvl * 2) . $key . ': ' . toString($objElem->$key)]);
        }
    }, []);
    return ($st ? '{' : str_repeat(INDENT, ($lvl - 1) * 2) . $objKey . ': {') . PHP_EOL .
        implode(PHP_EOL, $res) . PHP_EOL .
        str_repeat(INDENT, ($lvl - 1) * 2) . '}';
}

function genStylishElem(array $elem, int $lvl = 0): string
{
    $res = str_repeat(INDENT, $lvl * 2 - 1);
    if (array_key_exists('old', $elem)) {
        if (is_object($elem['old'])) {
            $old = genStylishObject($elem['key'], $elem['old'], $lvl + 1, true);
        } else {
            $old = toString($elem['old']);
        }
    } else {
        $old = '';
    }
    if (array_key_exists('new', $elem)) {
        if (is_object($elem['new'])) {
            $new = genStylishObject($elem['key'], $elem['new'], $lvl + 1, true);
        } else {
            $new = toString($elem['new']);
        }
    } else {
        $new = '';
    }
    switch ($elem['status']) {
        case ST_OLD:
            return $res . ST_TEXT[$elem['status']] . ' ' . $elem['key'] . ': ' . $old;
        case ST_NEW:
            return $res . ST_TEXT[$elem['status']] . ' ' . $elem['key'] . ': ' . $new;
        case ST_CHANGE:
            return $res . ST_TEXT[ST_OLD] . ' ' . $elem['key'] . ': ' . $old . PHP_EOL .
                str_repeat(INDENT, $lvl * 2 - 1) . ST_TEXT[ST_NEW] . ' ' . $elem['key'] . ': ' . $new;
        case ST_KEEP:
        default:
            return $res . ST_TEXT[ST_KEEP] . ' ' . $elem['key'] . ': ' . $old ?? $new;
    }
}

function genStylish(array $childs, int $lvl = 0): string
{
    $res = array_reduce($lvl == 0 ? $childs : $childs['child'], function ($acc, $elem) use ($lvl): array {
        if (isset($elem['child'])) {
            return array_merge($acc, [genStylish($elem, $lvl + 1)]);
        } else {
            return array_merge($acc, [genStylishElem($elem, $lvl + 1)]);
        }
    }, []);
    return ($lvl == 0 ? '{' : str_repeat(INDENT, ($lvl - 1) * 2 + 1) .
            ST_TEXT[$childs['status'] ?? ST_KEEP] . ' ' . $childs['key'] . ': {') . PHP_EOL .
        implode(PHP_EOL, $res) . PHP_EOL .
        ($lvl == 0 ? '}' : str_repeat(INDENT, $lvl * 2) . '}');
}
