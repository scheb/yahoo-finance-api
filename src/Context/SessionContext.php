<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;

/**
 * @final
 */
class SessionContext
{
    public function __construct(
        public readonly ClientInterface $httpClient,
        public readonly int $queryServer,
        public readonly ?CookieJarInterface $cookies = null,
        public readonly ?string $crumb = null,
    ) {
    }
}
