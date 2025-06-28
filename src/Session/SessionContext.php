<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Session;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;

/**
 * @final
 */
class SessionContext
{
    public ?CookieJarInterface $cookies = null;
    public ?string $crumb = null;

    public function __construct(
        public readonly ClientInterface $httpClient,
        public readonly int $queryServer,
    ) {
    }
}
