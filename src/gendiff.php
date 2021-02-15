<?php

namespace Php\Project\Lvl2\genDiff;

use Docopt;
use Funct\Collection;
use function Php\Project\Lvl2\parsers\parse;

function genDiff($pathToFile1, $pathToFile2, $format = 'pretty')
{
    $firstFileExtension = pathinfo($pathToFile1, PATHINFO_EXTENSION);
    $secondFileExtension = pathinfo($pathToFile2, PATHINFO_EXTENSION);

    $firstFileData = file_get_contents(genAbsolutPath($pathToFile1));
    $secondFileData = file_get_contents(genAbsolutPath($pathToFile2));

    $firstFileDecodedData = parse($firstFileData, $firstFileExtension);
    $secondFileDecodedData = parse($secondFileData, $secondFileExtension);

    $ast = buildAst($firstFileDecodedData, $secondFileDecodedData);
    $render = "Php\Project\Lvl2\\Formatters\\{$format}\\render";
    return $render($ast);
}

function genAbsolutPath($pathToFile)
{
    $absolutPath = $pathToFile[0] === '/' ? $pathToFile : __DIR__ . "/{$pathToFile}";
    if (file_exists($absolutPath)) {
        return $absolutPath;
    }
    throw new \Exception("The '{$pathToFile}' doesn't exists");
}

function buildAst($arr1, $arr2)
{
    $unionKeys = Collection\union(array_keys($arr1), array_keys($arr2));
    $result = array_reduce($unionKeys, function ($acc, $value) use ($arr1, $arr2) {
        if (isset($arr1[$value]) && !isset($arr2[$value])) {
            $nodeType = 'deleted';
            $acc[] = buildNode($nodeType, $value, $arr1[$value], '');
        } elseif (!isset($arr1[$value])) {
            $nodeType = 'added';
            $acc[] = buildNode($nodeType, $value, $arr2[$value], '');
        } elseif (is_array($arr1[$value]) && is_array($arr2[$value])) {
            $nodeType = 'nested';
            $children = buildAST($arr1[$value], $arr2[$value]);
            $acc[] = buildNode($nodeType, $value, '', '', $children);
        } elseif ($arr1[$value] === $arr2[$value]) {
            $nodeType = 'not changed';
            $acc[] = buildNode($nodeType, $value, $arr1[$value], $arr2[$value]);
        } else {
            $nodeType = 'changed';
            $acc[] = buildNode($nodeType, $value, $arr1[$value], $arr2[$value]);
        }
        return $acc;
    });
    return $result;
}

function buildNode($nodeType, $name, $oldValue, $newValue, $children = [])
{
    return [
        'status' => $nodeType,
        'name' => $name,
        'oldValue' => $oldValue,
        'newValue' => $newValue,
        'children' => $children
    ];
}
