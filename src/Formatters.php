<?php

namespace Differ\Formatters;

use Differ\Formatters\Formatters\Objects\Stylish;
use Differ\Formatters\Formatters\Objects\Plain;
use Differ\Formatters\Formatters\Objects\Json;


function formatData(array $data, string $format): string
{
    switch ($format) {
        case 'stylish':
            $stylishObject = new Stylish();
            $stylishObject->render($data);
            break;
        case 'plain':
            $plainObject = new Plain();
            $plainObject->render($data);
            break;
        case 'json':
            $jsonObject = new Json();
            $jsonObject->render($data);
            break;
        default:
            throw new  \Exception("Unsupported format: {$format}");
    }
}



//function formatData(array $data, string $format): string
//{
//    $formatters = [
//        'stylish' => function($data) {
//            $stylishObject = new Objects\Stylish();
//            $stylishObject->render($data);
//        },
//        'plain' => function($data) {
//            $plainObject = new Objects\Plain();
//            $plainObject->render($data);
//        },
//        'json' => function($data) {
//            $jsonObject = new Objects\Json();
//            $jsonObject->render($data);
//        },
//    ];
//
//    if (!array_key_exists($format, $formatters)) {
//        throw new \Exception("Unsupported format: {$format}");
//    }
//
//    return $formatters[$format]($data);
//}



