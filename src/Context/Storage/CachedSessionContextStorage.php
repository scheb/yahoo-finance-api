<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context\Storage;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Psr\Cache\CacheItemPoolInterface;
use Scheb\YahooFinanceApi\Context\Provider\QueryServerProvider;
use Scheb\YahooFinanceApi\Context\SessionContext;
use Scheb\YahooFinanceApi\HttpClient\HttpClientFactoryInterface;

/**
 * @final
 */
class CachedSessionContextStorage implements SessionContextStorageInterface
{
    private ?SessionContext $sessionContext = null;

    public function __construct(
        private readonly HttpClientFactoryInterface $httpClientFactory,
        private readonly CacheItemPoolInterface $cache,
        private readonly int $ttl,
        private readonly string $cacheKey,
    ) {
    }

    public function setSessionContext(SessionContext $sessionContext): void
    {
        $this->setCache($sessionContext);
        $this->sessionContext = $sessionContext;
    }

    public function getSessionContext(): SessionContext
    {
        // Return cached instance if available
        if (null !== $this->sessionContext) {
            return $this->sessionContext;
        }

        if ($sessionContext = $this->getCache()) {
            return $this->sessionContext = $sessionContext;
        }

        $sessionContext = new SessionContext(
            $this->httpClientFactory->createHttpClient(),
            QueryServerProvider::getRandomQueryServer(),
        );
        $this->setSessionContext($sessionContext);

        return $sessionContext;
    }

    public function invalidateSessionContext(): void
    {
        $this->cache->deleteItem($this->cacheKey);
        $this->sessionContext = null;
    }

    private function getCache(): ?SessionContext
    {
        $cacheItem = $this->cache->getItem($this->cacheKey);
        if ($cacheItem->isHit()) {
            $cacheData = $cacheItem->get();

            $cookieJar = null;
            if (isset($cacheData['cookies'])) {
                $cookies = array_map(fn (array $setCookieData) => new SetCookie($setCookieData), $cacheData['cookies']);
                $cookieJar = new CookieJar(false, $cookies);
            }

            return new SessionContext(
                $this->httpClientFactory->createHttpClient(),
                $cacheData['queryServer'] ?? QueryServerProvider::getRandomQueryServer(),
                $cookieJar,
                $cacheData['crumb'] ?? null,
            );
        }

        return null;
    }

    private function setCache(SessionContext $sessionContext): void
    {
        $cacheData = [
            'queryServer' => $sessionContext->queryServer,
            'cookies' => $sessionContext->cookies?->toArray(),
            'crumb' => $sessionContext->crumb,
        ];
        $cacheItem = $this->cache->getItem($this->cacheKey);
        $cacheItem->set($cacheData);
        $cacheItem->expiresAfter($this->ttl);
        $this->cache->save($cacheItem);
    }
}
