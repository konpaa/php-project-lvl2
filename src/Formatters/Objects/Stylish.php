<?php

namespace Differ\Formatters\Formatters\Objects;

use Differ\Formatters\Formatters\Objects\FormattersInterface;

class  Stylish implements FormattersInterface
{
    private const INDENT_LENGTH = 4;

    public function render(array $data): string
    {
        return $this->generateOutput($data);
    }

    private function generateOutput(array $tree, int $depth = 0): string
    {
        $indent = $this->getIndent($depth);
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

    private function getIndent(int $num): string
    {
        return str_repeat(' ', self::INDENT_LENGTH * $num);
    }

    /**
     * @param mixed $value
     * @param int $depth
     * @return string
     */
    private function stringify($value, $depth)
    {
        $stringifyComplexValue = function ($complexValue, $depth): string {
            $indent = $this->getIndent($depth);
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

        $type = gettype($value);

        return $typeFormats[$type]($value);
    }
}
