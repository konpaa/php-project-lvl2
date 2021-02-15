<?php

namespace Php\Project\Lvl2\Parsers;

use Symfony\Component\Yaml\Yaml;

use function Funct\Collection\sortBy;

const ST_KEEP = 1;
const ST_NEW = 2;
const ST_OLD = 3;
const ST_CHANGE = 4;
const ST_TEXT = [ST_KEEP => ' ', ST_NEW => '+', ST_OLD => '-'];
const EXT_YAML = ['yml', 'yaml'];

function parseFile(string $ext, string $content): object
{
    if (in_array($ext, EXT_YAML, true)) {
        return Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
    }
    return json_decode($content);
}

function makeDiff(object $arr1, object $arr2): array
{
    $keys = array_unique(array_merge(array_keys(get_object_vars($arr1)), array_keys(get_object_vars($arr2))));

    $diff = array_reduce($keys, function ($acc, $key) use ($arr1, $arr2): array {
        if (property_exists($arr1, $key) && property_exists($arr2, $key)) {
            if (is_object($arr1->$key) && is_object($arr2->$key)) {
                return array_merge($acc, [['key' => $key, 'child' => makeDiff($arr1->$key, $arr2->$key)]]);
            } else {
                if ($arr1->$key === $arr2->$key) {
                    return array_merge($acc, [['key' => $key, 'old' => $arr1->$key,
                        'new' => $arr2->$key, 'status' => ST_KEEP]]);
                } else {
                    return array_merge($acc, [['key' => $key, 'old' => $arr1->$key,
                        'new' => $arr2->$key, 'status' => ST_CHANGE]]);
                }
            }
        } else {
            if (property_exists($arr1, $key)) {
                return array_merge($acc, [['key' => $key, 'old' => $arr1->$key, 'status' => ST_OLD]]);
            } else {
                return array_merge($acc, [['key' => $key, 'new' => $arr2->$key, 'status' => ST_NEW]]);
            }
        }
    }, []);
    return sortBy($diff, fn($elem) => $elem['key']);
}
