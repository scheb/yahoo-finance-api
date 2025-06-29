<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function loadFixtureFile(string $filename): string
    {
        return file_get_contents(__DIR__.'/fixtures/'.$filename);
    }
}
