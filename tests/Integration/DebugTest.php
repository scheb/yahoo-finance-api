<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Integration;

use Scheb\YahooFinanceApi\Context\Provider\CookieProvider;
use Scheb\YahooFinanceApi\Context\Provider\CrumbProvider;
use Scheb\YahooFinanceApi\Context\SessionManager;
use Scheb\YahooFinanceApi\Context\Storage\SessionContextStorage;
use Scheb\YahooFinanceApi\HttpClient\GuzzleHttpClientFactory;
use Scheb\YahooFinanceApi\Tests\TestCase;

class DebugTest extends TestCase
{
    public function test(): void
    {
        $sessionManager = new SessionManager(
            new SessionContextStorage(new GuzzleHttpClientFactory()),
            new CrumbProvider(new CookieProvider())
        );

        $response = $sessionManager->request('GET', 'https://echo.free.beeceptor.com');
        echo $response->getBody();

        $this->assertTrue(true);
    }
}
