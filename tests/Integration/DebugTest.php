<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Integration;

use Scheb\YahooFinanceApi\Context\ContextManager;
use Scheb\YahooFinanceApi\Context\Provider\CookieProvider;
use Scheb\YahooFinanceApi\Context\Provider\CrumbProvider;
use Scheb\YahooFinanceApi\Context\Storage\SessionContextStorage;
use Scheb\YahooFinanceApi\HttpClient\GuzzleHttpClientFactory;
use Scheb\YahooFinanceApi\Tests\TestCase;

class DebugTest extends TestCase
{
    public function test(): void
    {
        $contextManager = new ContextManager(
            new SessionContextStorage(new GuzzleHttpClientFactory()),
            new CrumbProvider(new CookieProvider())
        );

        try {
            $response = $contextManager->request('GET', 'https://echo.free.beeceptor.com');
            echo $response->getBody();
        } catch (\Throwable $e) {
            $this->markTestSkipped('Skipped test after exception '.\get_class($e).': ('.$e->getCode().') '.$e->getMessage());
        }

        $this->assertTrue(true);
    }
}
