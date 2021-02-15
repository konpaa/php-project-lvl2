<?php

namespace Php\Project\Lvl2\gendiff;

use function Php\Project\Lvl2\path\{buildPathToFile, getExtention};
use function Php\Project\Lvl2\readfile\readFile;
use function Php\Project\Lvl2\ast\buildAst;
use function Php\Project\Lvl2\parsers\selectParser;

function genDiff($pathToFileBefore, $pathToFileAfter, $format = 'pretty')
{
    $realPathToFileBefore = buildPathToFile($pathToFileBefore);
    $realPathToFileAfter = buildPathToFile($pathToFileAfter);

    $extentionBefore = getExtention($pathToFileBefore);
    $extentionAfter = getExtention($pathToFileAfter);

    $contentBefore = readFile($realPathToFileBefore);
    $contentAfter = readFile($realPathToFileAfter);

    $parseBefore = selectParser($extentionBefore);
    $parseAfter = selectParser($extentionAfter);

    $dataBefore = $parseBefore($contentBefore);
    $dataAfter = $parseAfter($contentAfter);

    $ast = buildAst($dataBefore, $dataAfter);
    $format = "\Php\Project\Lvl2\Formatters\\{$format}\\format";
    return $format($ast);
}
