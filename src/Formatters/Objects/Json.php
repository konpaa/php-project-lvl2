<?php

namespace Differ\Formatters\Formatters\Objects;

use Differ\Formatters\Formatters\Objects\FormattersInterface;

class Json implements FormattersInterface
{
    public function render(array $data): string
    {
        return (string) json_encode($data, JSON_PRETTY_PRINT);
    }
}