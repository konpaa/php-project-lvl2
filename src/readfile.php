<?php

namespace Php\Project\Lvl2\readfile;

function readFile($path)
{
    if (!file_exists($path)) {
        throw new \Exception("File \"{$path}\" not exist.");
    }

    return file_get_contents($path);
}
