<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

use GuzzleHttp\Cookie\CookieJar;

/**
 * @final
 */
class CookieProvider
{
    public function acquireCookies(SessionContext $sessionContext): SessionContext
    {
        $cookieJar = new CookieJar();

        // Initialize session cookies
        $initialUrl = 'https://fc.yahoo.com';
        $sessionContext->httpClient->request('GET', $initialUrl, ['cookies' => $cookieJar, 'http_errors' => false]);

        return new SessionContext(
            $sessionContext->httpClient,
            $sessionContext->queryServer,
            $cookieJar,
            $sessionContext->crumb,
        );
    }
}
