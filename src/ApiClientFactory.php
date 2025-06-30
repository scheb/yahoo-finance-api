<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use Psr\Cache\CacheItemPoolInterface;
use Scheb\YahooFinanceApi\Context\CachedSessionContextStorage;
use Scheb\YahooFinanceApi\Context\CookieProvider;
use Scheb\YahooFinanceApi\Context\CrumbProvider;
use Scheb\YahooFinanceApi\Context\RetryableSessionManager;
use Scheb\YahooFinanceApi\Context\SessionContextStorage;
use Scheb\YahooFinanceApi\Context\SessionManager;
use Scheb\YahooFinanceApi\HttpClient\GuzzleHttpClientFactory;

/**
 * @final
 */
class ApiClientFactory
{
    public static function createApiClient(
        array $clientOptions = [],
        int $retries = 0,
        int $retryDelay = 0,
        ?CacheItemPoolInterface $cache = null,
        int $cacheTtl = 3600,
        string $cacheKey = 'yahoo_finance_session_context',
    ): ApiClient {
        $resultDecoder = new ResultDecoder(new ValueMapper());
        $sessionContextStorage = self::createSessionContextStorage($clientOptions, $cache, $cacheTtl, $cacheKey);
        $sessionManager = new SessionManager($sessionContextStorage, new CrumbProvider(new CookieProvider()));
        if ($retries > 0) {
            $sessionManager = new RetryableSessionManager($sessionManager, $retries + 1, $retryDelay);
        }

        return new ApiClient($sessionManager, $resultDecoder);
    }

    private static function createSessionContextStorage(array $clientOptions, ?CacheItemPoolInterface $cache, int $cacheTtl, string $cacheKey): CachedSessionContextStorage|SessionContextStorage
    {
        $httpClientFactory = new GuzzleHttpClientFactory($clientOptions);
        if (null !== $cache) {
            $sessionContextStorage = new CachedSessionContextStorage($httpClientFactory, $cache, $cacheTtl, $cacheKey);
        } else {
            $sessionContextStorage = new SessionContextStorage($httpClientFactory);
        }

        return $sessionContextStorage;
    }
}
