<?php

namespace Php\Project\Lvl2\Formatters\pretty;

use Funct\Collection;

const TAB_INDENT = 4;
const FIRST_INDENT = 2;

function render($data, $depth = 0)
{
    $indent = genIndent($depth, FIRST_INDENT);
    $arrResult = array_reduce($data, function ($acc, $value) use ($depth, $indent) {
        $status = $value['status'];
        $name = $value['name'];
        $oldValue = genValue($value['oldValue'], $depth);
        $newValue = genValue($value['newValue'], $depth);
        $children = $value['children'];

        switch ($status) {
            case 'nested':
                $acc[] = "{$indent}  {$name}: " . render($value['children'], $depth + 1);
                break;
            case 'not changed':
                $acc[] = "{$indent}  {$name}: {$oldValue}";
                break;
            case 'deleted':
                $acc[] = "{$indent}- {$name}: {$oldValue}";
                break;
            case 'added':
                $acc[] = "{$indent}+ {$name}: {$oldValue}";
                break;
            case 'changed':
                $acc[] = "{$indent}+ {$name}: {$newValue}";
                $acc[] = "{$indent}- {$name}: {$oldValue}";
                break;
        }
        return $acc;
    }, []);

    $indent = genIndent($depth);
    $strResult = "{\n" . implode("\n", $arrResult) . $indent . "\n" . $indent . "}";
    return $strResult;
}

function genValue($value, $depth)
{
    if (is_array($value)) {
        $key = key($value);
        $firstIndent = genIndent($depth, TAB_INDENT * 2);
        $lastIndent = genIndent($depth, FIRST_INDENT);
        return "{\n" . $firstIndent . $key . ": " . $value[$key] . "\n" . $lastIndent . "  }";
    } elseif (is_bool($value)) {
        return $value ? 'true' : 'false';
    } else {
        return $value;
    }
}

function genIndent($depth, $additionalIndent = 0)
{
    $countSpacesIndent = TAB_INDENT * $depth;
    return str_repeat(' ', $countSpacesIndent + $additionalIndent);
}
