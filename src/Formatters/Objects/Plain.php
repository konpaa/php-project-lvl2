<?php

namespace Differ\Formatters\Formatters\Objects;

use Differ\Formatters\Formatters\Objects\FormattersInterface;
use function Funct\Collection\flattenAll;

class Plain implements FormattersInterface
{
    public function render(array $data): string
    {
        return implode("\n", $this->generateOutput($data, []));
    }

    function generateOutput(array $tree, array $propertyNames): array
    {
        $output = array_map(function ($child) use ($propertyNames) {
            $name = implode('.', [...$propertyNames, $child['name']]);

            switch ($child['state']) {
                case 'added':
                    $value = $this->stringify($child['value']);
                    return "Property '{$name}' was added with value: {$value}";

                case 'removed':
                    return "Property '{$name}' was removed";

                case 'unchanged':
                    return "";

                case 'changed':
                    $oldValue = $this->stringify($child['oldValue']);
                    $newValue = $this->stringify($child['newValue']);
                    return "Property '{$name}' was updated. From {$oldValue} to {$newValue}";

                case 'nested':
                    return $this->generateOutput($child['children'], [...$propertyNames, $child['name']]);

                default:
                    throw new \Exception("Invalid node state: {$child['state']}");
            }
        }, $tree);

        $filteredOutput = array_filter($output, fn($part) => $part !== '');

        return flattenAll($filteredOutput);
    }

    /**
     * @param mixed $value
     * @return string
     */
    function stringify($value)
    {
        $typeFormats = [
            'string' => fn($value) => "'{$value}'",
            'integer' => fn($value) => (string) $value,
            'object' => fn($value) => '[complex value]',
            'array' => fn($value) => '[complex value]',
            'boolean' => fn($value) => $value ? 'true' : 'false',
            'NULL' => fn($value) => 'null'
        ];

        $type = gettype($value);

        return $typeFormats[$type]($value);
    }
}