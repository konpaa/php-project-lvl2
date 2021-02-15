<?php

namespace Php\Project\Lvl2\Differ;

use function Php\Project\Lvl2\Parsers\parseFile;
use function Php\Project\Lvl2\Parsers\makeDiff;
use function Php\Project\Lvl2\Formatters\formatDiff;

use const Php\Project\Lvl2\Formatters\FM_STYLISH;

function genDiff(string $path1, string $path2, string $format = FM_STYLISH): string
{
    if (!file_exists($path1)) {
        return "Error: file '{$path1}' not found.";
    }
    $file1 = file_get_contents($path1);
    if ($file1 === false) {
        return "Error: file '{$path1}' read failed.";
    }
    if (!file_exists($path2)) {
        return "Error: file '{$path2}' not found.";
    }
    $file2 = file_get_contents($path2);
    if ($file2 === false) {
        return "Error: file '{$path2}' read failed.";
    }
    $arr1 = parseFile(pathinfo($path1, PATHINFO_EXTENSION), $file1);
    if ($arr1 == null) {
        return "Error: parse file '{$path1}' fail.";
    }
    $arr2 = parseFile(pathinfo($path2, PATHINFO_EXTENSION), $file2);
    if ($arr2 == null) {
        return "Error: parse file '{$path2}' fail.";
    }
    $diff = makeDiff($arr1, $arr2);
    $res = formatDiff($diff, $format);
    return $res;
}
