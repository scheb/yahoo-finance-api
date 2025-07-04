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
use Scheb\YahooFinanceApi\Context\SessionManagerInterface;
use Scheb\YahooFinanceApi\HttpClient\GuzzleHttpClientFactory;

/**
 * @final
 */
class ApiClientFactory
{
    private const DEFAULT_RETRIES = 0;
    private const DEFAULT_RETRY_DELAY = 0;
    private const DEFAULT_TTL = 3600;
    private const DEFAULT_CACHE_KEY = 'yahoo_finance_session_context';

    public static function createApiClient(
        array $clientOptions = [],
        int $retries = self::DEFAULT_RETRIES,
        int $retryDelay = self::DEFAULT_RETRY_DELAY,
        ?CacheItemPoolInterface $cache = null,
        int $cacheTtl = self::DEFAULT_TTL,
        string $cacheKey = self::DEFAULT_CACHE_KEY,
    ): ApiClient {
        $sessionManager = self::createSessionManager($clientOptions, $retries, $retryDelay, $cache, $cacheTtl, $cacheKey);
        $resultDecoder = new ResultDecoder(new ValueMapper());

        return new ApiClient($sessionManager, $resultDecoder);
    }

    public static function createSessionManager(
        array $clientOptions = [],
        int $retries = self::DEFAULT_RETRIES,
        int $retryDelay = self::DEFAULT_RETRY_DELAY,
        ?CacheItemPoolInterface $cache = null,
        int $cacheTtl = self::DEFAULT_TTL,
        string $cacheKey = self::DEFAULT_CACHE_KEY,
    ): SessionManagerInterface {
        $sessionContextStorage = self::createSessionContextStorage($clientOptions, $cache, $cacheTtl, $cacheKey);
        $sessionManager = new SessionManager($sessionContextStorage, new CrumbProvider(new CookieProvider()));
        if ($retries > 0) {
            $sessionManager = new RetryableSessionManager($sessionManager, $retries + 1, $retryDelay);
        }

        return $sessionManager;
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
