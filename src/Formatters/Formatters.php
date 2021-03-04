<?php

namespace Differ\Formatters\Formatters;

class Formatters
{
    public const CREATED = 1;
    public const UPDATED = 2;
    public const REMOVED = 3;
    public const UNCHANGED = 4;
    public const NESTED = 5;

//    public function __construct(array $first, array $second)
//    {
//        $this->first = $first;
//        $this->second = $second;
//    }

    function compareData(array $first, array $second): array
    {
        $keys = array_unique([...array_keys($first), ...array_keys($second)]);

        return array_reduce($keys, function ($acc, $key) use ($first, $second) {
            $operation = $this->getDiffOperation($key, $first, $second);

            if ($operation === null) {
                throw new \InvalidArgumentException('Invalid operation');
            }

            $acc[] = $this->makeOperation(
                $operation,
                $key,
                $this->isNested($operation) ? $this->compareData($first[$key], $second[$key]) : ($first[$key] ?? null),
                $second[$key] ?? null
            );

            return $acc;
        }, []);
    }

    function getDiffOperation($key, $first, $second)
    {
        foreach ($this->getDiffOperations() as $operationName => $isNeedOperation) {
            if ($isNeedOperation($key, $first, $second)) {
                return $operationName;
            }
        }

        return null;
    }

    function getDiffOperations()
    {
        return [
            $this->operationCreated() => function (string $key, array $first, array $second) {
                return !array_key_exists($key, $first) && array_key_exists($key, $second);
            },
            $this->operationUpdated() => function (string $key, array $first, array $second) {
                return array_key_exists($key, $first) && array_key_exists($key, $second) && $second[$key] !== $first[$key];
            },
            $this->operationRemoved() => function (string $key, array $first, array $second) {
                return array_key_exists($key, $first) && !array_key_exists($key, $second);
            },
            $this->operationUnchanged() => function (string $key, array $first, array $second) {
                return array_key_exists($key, $first) && array_key_exists($key, $second) && $second[$key] === $first[$key];
            },
            $this->operationNested() => function (string $key, array $first, array $second) {
                $isArrays = is_array($first[$key]) && is_array($second[$key]);

                return array_key_exists($key, $first) && array_key_exists($key, $first) && $isArrays;
            }
        ];
    }

    function makeOperation(
        $operation,
        $name,
        $oldValue,
        $newValue
    ) {
        return [
            'operation' => $operation,
            'name' => $name,
            'oldValue' => $oldValue,
            'newValue' => $newValue
        ];
    }

    function getOperation($operation)
    {
        return $operation['operation'];
    }

    function getName($operation)
    {
        return $operation['name'];
    }

    function getOldValue($operation)
    {
        return $operation['oldValue'];
    }

    function getNewValue($operation)
    {
        return $operation['newValue'];
    }

    function isNested($operation)
    {
        return $operation === NESTED;
    }

    function operationCreated()
    {
        return CREATED;
    }

    function operationUpdated()
    {
        return UPDATED;
    }

    function operationRemoved()
    {
        return REMOVED;
    }

    function operationUnchanged()
    {
        return UNCHANGED;
    }

    function operationNested()
    {
        return NESTED;
    }
}