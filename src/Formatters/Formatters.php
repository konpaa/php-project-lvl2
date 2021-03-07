<?php

namespace Differ\Formatters\Formatters;

use function Funct\Collection\flattenAll;

class Formatters
{
    public const INDENT_LENGTH = 4;

    /**
     * @param array $data
     * @param string $type
     * @return mixed
     */
    public function render($data, $type)
    {
        $map = [
            'json' => fn($data) => json_encode($data, JSON_PRETTY_PRINT),
            'plain' => fn($data) => implode("\n", $this->generateOutput($data, [])),
            'stylish' => fn($data) => $this->generateOutput($data),
        ];

        return $map[$type]($data);
    }

    /**
     * @param array $tree
     * @param ?array $propertyNames
     * @param ?int $depth
     * @return string|array
     */
    public function generateOutput($tree, $propertyNames = null, $depth = null)
    {
        if ($propertyNames) {
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

        } elseif ($depth) {
            $indent = str_repeat(' ', self::INDENT_LENGTH * $depth);
            $output = array_map(function ($node) use ($depth, $indent): string {
                switch ($node['state']) {
                    case 'added':
                        $formattedValue = $this->stringify($node['value'], $depth);
                        return "{$indent}  + {$node['name']}: {$formattedValue}";

                    case 'removed':
                        $formattedValue = $this->stringify($node['value'], $depth);
                        return "{$indent}  - {$node['name']}: {$formattedValue}";

                    case 'unchanged':
                        $formattedValue = $this->stringify($node['value'], $depth);
                        return "{$indent}    {$node['name']}: {$formattedValue}";

                    case 'changed':
                        $deleted = $this->stringify($node['oldValue'], $depth);
                        $added = $this->stringify($node['newValue'], $depth);
                        return "{$indent}  - {$node['name']}: {$deleted}\n{$indent}  + {$node['name']}: {$added}";

                    case 'nested':
                        $stylishOutput = $this->generateOutput($node['children'], $depth + 1);
                        return "{$indent}    {$node['name']}: {$stylishOutput}";

                    default:
                        throw new \Exception('Invalid node status!');
                }
            }, $tree);

            return implode("\n", ["{", ...$output, "{$indent}}"]);
        }
    }

    /**
     * @param mixed $value
     * @param ?int $depth
     * @return string
     */
    public function stringify($value, $depth = null)
    {
        if ($depth) {
            $stringifyComplexValue = function ($complexValue, $depth): string {
                $indent = str_repeat(' ', self::INDENT_LENGTH * $depth);
                $iter = function ($value, $key) use ($depth, $indent): string {
                    $formattedValue = $this->stringify($value, $depth);
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
        } else {
            $typeFormats = [
                'string' => fn($value) => "'{$value}'",
                'integer' => fn($value) => (string) $value,
                'object' => fn($value) => '[complex value]',
                'array' => fn($value) => '[complex value]',
                'boolean' => fn($value) => $value ? 'true' : 'false',
                'NULL' => fn($value) => 'null'
            ];
        }

        $type = gettype($value);

        return $typeFormats[$type]($value);
    }

//    public function generatePlainOutput(array $tree, array $propertyNames): array
//    {
//        $output = array_map(function ($child) use ($propertyNames) {
//            $name = implode('.', [...$propertyNames, $child['name']]);
//
//            switch ($child['state']) {
//                case 'added':
//                    $value = $this->stringifyPlain($child['value']);
//                    return "Property '{$name}' was added with value: {$value}";
//
//                case 'removed':
//                    return "Property '{$name}' was removed";
//
//                case 'unchanged':
//                    return "";
//
//                case 'changed':
//                    $oldValue = $this->stringifyPlain($child['oldValue']);
//                    $newValue = $this->stringifyPlain($child['newValue']);
//                    return "Property '{$name}' was updated. From {$oldValue} to {$newValue}";
//
//                case 'nested':
//                    return $this->generatePlainOutput($child['children'], [...$propertyNames, $child['name']]);
//
//                default:
//                    throw new \Exception("Invalid node state: {$child['state']}");
//            }
//        }, $tree);
//
//        $filteredOutput = array_filter($output, fn($part) => $part !== '');
//
//        return flattenAll($filteredOutput);
//    }
//
//    public function generateStylishOutput(array $tree, int $depth = 0): string
//    {
//        $indent = str_repeat(' ', self::INDENT_LENGTH * $depth);
//        $output = array_map(function ($node) use ($depth, $indent): string {
//            switch ($node['state']) {
//                case 'added':
//                    $formattedValue = $this->stringifyStylish($node['value'], $depth);
//                    return "{$indent}  + {$node['name']}: {$formattedValue}";
//
//                case 'removed':
//                    $formattedValue = $this->stringifyStylish($node['value'], $depth);
//                    return "{$indent}  - {$node['name']}: {$formattedValue}";
//
//                case 'unchanged':
//                    $formattedValue = $this->stringifyStylish($node['value'], $depth);
//                    return "{$indent}    {$node['name']}: {$formattedValue}";
//
//                case 'changed':
//                    $deleted = $this->stringifyStylish($node['oldValue'], $depth);
//                    $added = $this->stringifyStylish($node['newValue'], $depth);
//                    return "{$indent}  - {$node['name']}: {$deleted}\n{$indent}  + {$node['name']}: {$added}";
//
//                case 'nested':
//                    $stylishOutput = $this->generateStylishOutput($node['children'], $depth + 1);
//                    return "{$indent}    {$node['name']}: {$stylishOutput}";
//
//                default:
//                    throw new \Exception('Invalid node status!');
//            }
//        }, $tree);
//
//        return implode("\n", ["{", ...$output, "{$indent}}"]);
//    }
//
//    /**
//     * @param mixed $value
//     * @param int $depth
//     * @return string
//     */
//    public function stringifyStylish($value, $depth = 0)
//    {
//        $stringifyComplexValue = function ($complexValue, $depth): string {
//            $indent = str_repeat(' ', self::INDENT_LENGTH * $depth);
//            $iter = function ($value, $key) use ($depth, $indent): string {
//                $formattedValue = $this->stringifyStylish($value, $depth);
//                return "{$indent}    {$key}: {$formattedValue}";
//            };
//
//            $stringifiedValue = array_map($iter, $complexValue, array_keys($complexValue));
//            return implode("\n", ["{", ...$stringifiedValue, "{$indent}}"]);
//        };
//
//        $typeFormats = [
//            'string' => fn($value) => $value,
//            'integer' => fn($value) => (string) $value,
//            'object' => fn($value) => $stringifyComplexValue(get_object_vars($value), $depth + 1),
//            'array' => fn($value) => $stringifyComplexValue($value, $depth + 1),
//            'boolean' => fn($value) => $value ? "true" : "false",
//            'NULL' => fn($value) => 'null'
//        ];
//
//        $type = gettype($value);
//
//        return $typeFormats[$type]($value);
//    }
//
//    /**
//     * @param mixed $value
//     * @return string
//     */
//    public function stringifyPlain($value)
//    {
//        $typeFormats = [
//            'string' => fn($value) => "'{$value}'",
//            'integer' => fn($value) => (string) $value,
//            'object' => fn($value) => '[complex value]',
//            'array' => fn($value) => '[complex value]',
//            'boolean' => fn($value) => $value ? 'true' : 'false',
//            'NULL' => fn($value) => 'null'
//        ];
//
//        $type = gettype($value);
//
//        return $typeFormats[$type]($value);
//    }
}