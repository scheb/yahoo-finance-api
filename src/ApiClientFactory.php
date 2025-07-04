<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use Psr\Cache\CacheItemPoolInterface;
use Scheb\YahooFinanceApi\Context\ContextManager;
use Scheb\YahooFinanceApi\Context\ContextManagerInterface;
use Scheb\YahooFinanceApi\Context\Provider\CookieProvider;
use Scheb\YahooFinanceApi\Context\Provider\CrumbProvider;
use Scheb\YahooFinanceApi\Context\RetryableContextManager;
use Scheb\YahooFinanceApi\Context\Storage\CachedSessionContextStorage;
use Scheb\YahooFinanceApi\Context\Storage\SessionContextStorage;
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
        $contextManager = self::createContextManager($clientOptions, $retries, $retryDelay, $cache, $cacheTtl, $cacheKey);
        $resultDecoder = new ResultDecoder(new ValueMapper());

        return new ApiClient($contextManager, $resultDecoder);
    }

    public static function createContextManager(
        array $clientOptions = [],
        int $retries = self::DEFAULT_RETRIES,
        int $retryDelay = self::DEFAULT_RETRY_DELAY,
        ?CacheItemPoolInterface $cache = null,
        int $cacheTtl = self::DEFAULT_TTL,
        string $cacheKey = self::DEFAULT_CACHE_KEY,
    ): ContextManagerInterface {
        $sessionContextStorage = self::createSessionContextStorage($clientOptions, $cache, $cacheTtl, $cacheKey);
        $contextManager = new ContextManager($sessionContextStorage, new CrumbProvider(new CookieProvider()));
        if ($retries > 0) {
            $contextManager = new RetryableContextManager($contextManager, $retries + 1, $retryDelay);
        }

        return $contextManager;
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
