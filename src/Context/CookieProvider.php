<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;

/**
 * @final
 */
class CookieProvider
{
    public function __construct(
        private readonly ClientInterface $client,
    ) {
    }

    public function acquireCookies(): CookieJarInterface
    {
        $cookieJar = new CookieJar();

        // Initialize session cookies
        $initialUrl = 'https://fc.yahoo.com';
        $this->client->request('GET', $initialUrl, ['cookies' => $cookieJar, 'http_errors' => false]);

        return $cookieJar;
    }
}
