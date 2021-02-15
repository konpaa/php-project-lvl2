<?php

namespace Php\Project\Lvl2\cli;

use function Php\Project\Lvl2\genDiff\genDiff;

const DOCOPT = <<<'DOCOPT'
Generate diff

Usage:
    gendiff (-h|--help)
    gendiff (-v|--version)
    gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
    -h --help                     Show this screen
    -v --version                  Show version
    --format <fmt>                Report format [default: pretty]
DOCOPT;

function run()
{
    $docopt = getArgs(DOCOPT);
    $pathToFile1 = $docopt['<firstFile>'];
    $pathToFile2 = $docopt['<secondFile>'];
    $format = $docopt['--format'];
    try {
        print_r(genDiff($pathToFile1, $pathToFile2, $format));
    } catch (\Exception $e) {
        print_r($e->getMessage());
    }
}

function getArgs($content)
{
    return \Docopt::handle($content, ['version' => '1.0.0']);
}
