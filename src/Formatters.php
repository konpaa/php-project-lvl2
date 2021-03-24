<?php

namespace Differ\Formatters;

use Differ\Formatters\Formatters\Objects\Stylish;
use Differ\Formatters\Formatters\Objects\Plain;
use Differ\Formatters\Formatters\Objects\Json;


function formatData(array $data, string $format): string
{
    $formatters = [
        'stylish' => new Stylish(),
        'plain' => new Plain(),
        'json' => new Json(),
    ];

    if (!array_key_exists($format, $formatters)) {
        throw new \Exception("Unsupported format: {$format}");
    }

    $formatter = $formatters[$format];

    return $formatter->render($data);
}


function formatData2(array $data, string $format): string
{
    switch ($format) {
        case 'stylish':
            $formatter = new Stylish();
            break;
        case 'plain':
            $formatter = new Plain();
            break;
        case 'json':
            $formatter = new Json();
            break;
        default:
            throw new  \Exception("Unsupported format: {$format}");
    }

    return $formatter->render($data);
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



