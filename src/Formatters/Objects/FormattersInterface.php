<?php

namespace Differ\Formatters\Formatters\Objects;

interface FormattersInterface
{
    public function render(array $data): string;
}