<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests;

use PHPUnit\Framework\TestCase;
use Scheb\YahooFinanceApi\HttpClient\GuzzleHttpClientFactory;
use Scheb\YahooFinanceApi\Session\SessionManager;

class DebugTest extends TestCase
{
    public function test(): void
    {
        $sessionManager = new SessionManager(new GuzzleHttpClientFactory());

        $response = $sessionManager->request('GET', 'https://echo.free.beeceptor.com');
        echo $response->getBody();

        $this->assertTrue(true);
    }
}
