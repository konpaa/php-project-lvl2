<?php

namespace Differ\Formatters;

use Differ\Formatters\Formatters\Formatters;

use function Differ\Formatters\Stylish\render as renderInStylish;
use function Differ\Formatters\Plain\render as renderInPlain;
use function Differ\Formatters\Json\render as renderInJson;

function formatData(array $data, string $format): string
{
    $formatData = new Formatters();

    $formatters = [
        'stylish' => fn($data) => $formatData->render($data, 'stylish'),
        'plain' => fn($data) => $formatData->render($data, 'plain'),
        'json' => fn($data) => $formatData->render($data, 'json'),
    ];
//    $formatters = [
//        'stylish' => fn($data) => renderInStylish($data),
//        'plain' => fn($data) => renderInPlain($data),
//        'json' => fn($data) => renderInJson($data)
//    ];

    if (!array_key_exists($format, $formatters)) {
        throw new \Exception("Unsupported format: {$format}");
    }

    return $formatters[$format]($data);
}
