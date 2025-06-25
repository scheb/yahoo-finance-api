<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;

/**
 * @final
 */
class CrumbProvider
{
    public function __construct(
        private readonly ClientInterface $client,
    ) {
    }

    public function acquireCrumb(CookieJarInterface $cookieJar): string
    {
        $qs = QueryServer::getRandomQueryServer();

        // Get crumb value
        $initialUrl = 'https://query'.$qs.'.finance.yahoo.com/v1/test/getcrumb';

        return (string) $this->client->request('GET', $initialUrl, ['cookies' => $cookieJar])->getBody();
    }
}
