<?php

namespace Php\Project\Lvl2\cli;

use Docopt;

function run()
{
    $doc = <<<'DOCOPT'
Generate diff
Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)

  Options:
  -h --help                     Show this screen
  -v --version                  Show version
DOCOPT;

    $result = Docopt::handle($doc, array('version' => '0.0.1'));
    echo $result;
    echo "\n";
}