<?php

namespace Php\Project\Lvl2\DifferTest;

use PHPUnit\Framework\TestCase;

use function Php\Project\Lvl2\Differ\genDiff;

use const Php\Project\Lvl2\Formatters\FM_JSON;
use const Php\Project\Lvl2\Formatters\FM_PLAIN;
use const Php\Project\Lvl2\Formatters\FM_STYLISH;

class DifferTest extends TestCase
{
    public function testCompareJson(): void
    {
        $file1 = 'tests/fixtures/step3/file1.yml.json.yml.json.json';
        $file2 = 'tests/fixtures/step3/file2.yml.json.yml.json.json';
        $diff = genDiff($file1, $file2, FM_STYLISH);
        $this->assertStringEqualsFile('tests/fixtures/step3/res.diff', $diff);
    }

    public function testCompareYaml(): void
    {
        $file1 = 'tests/fixtures/step5/file1.yml.json.yml';
        $file2 = 'tests/fixtures/step5/file2.yml.json.yml';
        $diff = genDiff($file1, $file2, FM_STYLISH);
        $this->assertStringEqualsFile('tests/fixtures/step5/res.diff', $diff);
    }

    public function testCompareJsonRecursive(): void
    {
        $file1 = 'tests/fixtures/step5/file1.yml.json.yml.json.json';
        $file2 = 'tests/fixtures/step5/file2.yml.json.yml.json.json';
        $diff = genDiff($file1, $file2, FM_STYLISH);
        $this->assertStringEqualsFile('tests/fixtures/step5/res.diff', $diff);
    }

    public function testCompareYamlRecursive(): void
    {
        $file1 = 'tests/fixtures/step5/file1.yml.json.yml.json.yml';
        $file2 = 'tests/fixtures/step5/file2.yml.json.yml.json.yml';
        $diff = genDiff($file1, $file2, FM_STYLISH);
        $this->assertStringEqualsFile('tests/fixtures/step5/res.diff', $diff);
    }

    public function testCompareJsonRecursivePlain(): void
    {
        $file1 = 'tests/fixtures/step5/file1.yml.json.yml.json.json';
        $file2 = 'tests/fixtures/step5/file2.yml.json.yml.json.json';
        $diff = genDiff($file1, $file2, FM_PLAIN);
        $this->assertStringEqualsFile('tests/fixtures/step7/res.diff', $diff);
    }

    public function testCompareYamlRecursivePlain(): void
    {
        $file1 = 'tests/fixtures/step5/file1.yml.json.yml.json.yml';
        $file2 = 'tests/fixtures/step5/file2.yml.json.yml.json.yml';
        $diff = genDiff($file1, $file2, FM_PLAIN);
        $this->assertStringEqualsFile('tests/fixtures/step7/res.diff', $diff);
    }

    public function testCompareJsonRecursiveJson(): void
    {
        $file1 = 'tests/fixtures/step5/file1.yml.json.yml.json.json';
        $file2 = 'tests/fixtures/step5/file2.yml.json.yml.json.json';
        $diff = genDiff($file1, $file2, FM_PLAIN);
        $this->assertStringEqualsFile('tests/fixtures/step7/res.diff', $diff);
    }

    public function testCompareYamlRecursiveJson(): void
    {
        $file1 = 'tests/fixtures/step5/file1.yml.json.yml.json.yml';
        $file2 = 'tests/fixtures/step5/file2.yml.json.yml.json.yml';
        $diff = genDiff($file1, $file2, FM_JSON);
        $this->assertStringEqualsFile('tests/fixtures/step8/res.diff', $diff);
    }
}
