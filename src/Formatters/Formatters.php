<?php

namespace Differ\Formatters\Formatters;

use function Funct\Collection\flattenAll;

class Formatters
{
    public const INDENT_LENGTH = 4;

    public function render(array $data, string $type): string
    {
        $map = [
            'json' => fn($data) => json_encode($data, JSON_PRETTY_PRINT),
            'plain' => fn($data) => implode("\n", $this->generatePlainOutput($data, [])),
            'stylish' => fn($data) => $this->generateStylishOutput($data),
        ];

        return $map[$type]($data);
    }

    public function generatePlainOutput(array $tree, array $propertyNames): array
    {
        $output = array_map(function ($child) use ($propertyNames) {
            $name = implode('.', [...$propertyNames, $child['name']]);

            switch ($child['state']) {
                case 'added':
                    $value = $this->stringifyPlain($child['value']);
                    return "Property '{$name}' was added with value: {$value}";

                case 'removed':
                    return "Property '{$name}' was removed";

                case 'unchanged':
                    return "";

                case 'changed':
                    $oldValue = $this->stringifyPlain($child['oldValue']);
                    $newValue = $this->stringifyPlain($child['newValue']);
                    return "Property '{$name}' was updated. From {$oldValue} to {$newValue}";

                case 'nested':
                    return $this->generatePlainOutput($child['children'], [...$propertyNames, $child['name']]);

                default:
                    throw new \Exception("Invalid node state: {$child['state']}");
            }
        }, $tree);

        $filteredOutput = array_filter($output, fn($part) => $part !== '');

        return flattenAll($filteredOutput);

    }

    public function generateStylishOutput(array $tree, int $depth = 0): string
    {
        $indent = str_repeat(' ', self::INDENT_LENGTH * $depth);
        $output = array_map(function ($node) use ($depth, $indent): string {
            switch ($node['state']) {
                case 'added':
                    $formattedValue = $this->stringifyStylish($node['value'], $depth);
                    return "{$indent}  + {$node['name']}: {$formattedValue}";

                case 'removed':
                    $formattedValue = $this->stringifyStylish($node['value'], $depth);
                    return "{$indent}  - {$node['name']}: {$formattedValue}";

                case 'unchanged':
                    $formattedValue = $this->stringifyStylish($node['value'], $depth);
                    return "{$indent}    {$node['name']}: {$formattedValue}";

                case 'changed':
                    $deleted = $this->stringifyStylish($node['oldValue'], $depth);
                    $added = $this->stringifyStylish($node['newValue'], $depth);
                    return "{$indent}  - {$node['name']}: {$deleted}\n{$indent}  + {$node['name']}: {$added}";

                case 'nested':
                    $stylishOutput = $this->generateStylishOutput($node['children'], $depth + 1);
                    return "{$indent}    {$node['name']}: {$stylishOutput}";

                default:
                    throw new \Exception('Invalid node status!');
            }
        }, $tree);

        return implode("\n", ["{", ...$output, "{$indent}}"]);

    }

    public function stringifyStylish(mixed $value, int $depth = 0): string
    {
        $stringifyComplexValue = function ($complexValue, $depth): string {
            $indent = str_repeat(' ', self::INDENT_LENGTH * $depth);
            $iter = function ($value, $key) use ($depth, $indent): string {
                $formattedValue = $this->stringifyStylish($value, $depth);
                return "{$indent}    {$key}: {$formattedValue}";
            };

            $stringifiedValue = array_map($iter, $complexValue, array_keys($complexValue));
            return implode("\n", ["{", ...$stringifiedValue, "{$indent}}"]);
        };

        $typeFormats = [
            'string' => fn($value) => $value,
            'integer' => fn($value) => (string) $value,
            'object' => fn($value) => $stringifyComplexValue(get_object_vars($value), $depth + 1),
            'array' => fn($value) => $stringifyComplexValue($value, $depth + 1),
            'boolean' => fn($value) => $value ? "true" : "false",
            'NULL' => fn($value) => 'null'
        ];

        $type = gettype($value);

        return $typeFormats[$type]($value);
    }

    public function stringifyPlain(mixed $value): string
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