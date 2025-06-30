<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Integration;

use Scheb\YahooFinanceApi\ApiClientFactory;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class ApiClientCachedIntegrationTest extends ApiClientIntegrationTest
{
    protected function setUp(): void
    {
        $cache = new FilesystemAdapter();
        $this->client = ApiClientFactory::createApiClient(
            retries: 1,
            cache: $cache,
            cacheKey: 'yahoo_finance_session_context_integration_tests',
        );
    }
}
