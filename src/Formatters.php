<?php

namespace Differ\Formatters;

use Differ\Formatters\Formatters\Objects;

function formatData(array $data, string $format): string
{

    $formatters = [
        'stylish' => function($data) {
            $stylishObject = new Objects\Stylish();
            $stylishObject->render($data);
        },
        'plain' => function($data) {
            $plainObject = new Objects\Plain();
            $plainObject->render($data);
        },
        'json' => function($data) {
            $jsonObject = new Objects\Json();
            $jsonObject->render($data);
        },
    ];

    if (!array_key_exists($format, $formatters)) {
        throw new \Exception("Unsupported format: {$format}");
    }

    return $formatters[$format]($data);
}
