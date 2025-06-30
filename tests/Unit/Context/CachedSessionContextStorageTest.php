<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Unit\Context;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Scheb\YahooFinanceApi\Context\CachedSessionContextStorage;
use Scheb\YahooFinanceApi\Context\SessionContext;
use Scheb\YahooFinanceApi\HttpClient\HttpClientFactoryInterface;
use Scheb\YahooFinanceApi\Tests\TestCase;

class CachedSessionContextStorageTest extends TestCase
{
    private const QUERY_SERVER = 2;
    private const CACHE_KEY = 'yahoo_finance_session_context';
    private const CACHE_TTL = 1800;
    private const CRUMB_VALUE = 'crumb-value';

    private MockObject|ClientInterface $httpClient;
    private MockObject|HttpClientFactoryInterface $httpClientFactory;
    private MockObject|CacheItemPoolInterface $cache;
    private MockObject|CacheItemInterface $cacheItem;
    private CachedSessionContextStorage $storage;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->httpClientFactory = $this->createMock(HttpClientFactoryInterface::class);
        $this->httpClientFactory
            ->expects($this->any())
            ->method('createHttpClient')
            ->willReturn($this->httpClient);

        $this->cache = $this->createMock(CacheItemPoolInterface::class);
        $this->cacheItem = $this->createMock(CacheItemInterface::class);

        $this->storage = new CachedSessionContextStorage(
            $this->httpClientFactory,
            $this->cache,
            self::CACHE_TTL,
            self::CACHE_KEY,
        );
    }

    #[Test]
    public function getSessionContext_whenCacheHit_returnsCachedSessionContext(): void
    {
        $setCookieData = ['Name' => 'cookieName', 'Value' => 'cookieValue'];
        $cacheData = ['queryServer' => self::QUERY_SERVER, 'cookies' => [$setCookieData], 'crumb' => self::CRUMB_VALUE];

        $expectedCookieJar = new CookieJar(false, [new SetCookie($setCookieData)]);
        $expectedSessionContext = new SessionContext($this->httpClient, self::QUERY_SERVER, $expectedCookieJar, self::CRUMB_VALUE);

        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->with(self::CACHE_KEY)
            ->willReturn($this->cacheItem);

        $this->cacheItem
            ->expects($this->once())
            ->method('isHit')
            ->willReturn(true);

        $this->cacheItem
            ->expects($this->once())
            ->method('get')
            ->willReturn($cacheData);

        $result = $this->storage->getSessionContext();

        $this->assertEquals($expectedSessionContext, $result);
    }

    #[Test]
    public function getSessionContext_whenCacheMiss_createsNewSessionContextAndCachesIt(): void
    {
        $this->cache
            ->expects($this->exactly(2))
            ->method('getItem')
            ->with(self::CACHE_KEY)
            ->willReturn($this->cacheItem);

        $this->cacheItem
            ->expects($this->once())
            ->method('isHit')
            ->willReturn(false);

        $this->cacheItem
            ->expects($this->once())
            ->method('set')
            ->with($this->callback(function (array $cacheData) {
                return isset($cacheData['queryServer']) && \is_int($cacheData['queryServer']);
            }));

        $this->cacheItem
            ->expects($this->once())
            ->method('expiresAfter')
            ->with(self::CACHE_TTL);

        $this->cache
            ->expects($this->once())
            ->method('save')
            ->with($this->cacheItem);

        $result = $this->storage->getSessionContext();

        $this->assertSame($this->httpClient, $result->httpClient);
        $this->assertIsInt($result->queryServer);
        $this->assertNull($result->cookies);
        $this->assertNull($result->crumb);
    }

    #[Test]
    public function getSessionContext_whenCacheHitButInvalidData_createsNewSessionContext(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->with(self::CACHE_KEY)
            ->willReturn($this->cacheItem);

        $this->cacheItem
            ->expects($this->once())
            ->method('isHit')
            ->willReturn(true);

        $this->cacheItem
            ->expects($this->once())
            ->method('get')
            ->willReturn('invalid_data');

        $result = $this->storage->getSessionContext();

        $this->assertSame($this->httpClient, $result->httpClient);
        $this->assertIsInt($result->queryServer);
        $this->assertNull($result->cookies);
        $this->assertNull($result->crumb);
    }

    #[Test]
    public function setSessionContext_newSessionContextGiven_storesSessionContextInCache(): void
    {
        $expectedSetCookieData = [
            'Name' => 'cookieName',
            'Value' => 'cookieValue',
            'Domain' => 'example.com',
            'Path' => '/',
            'Max-Age' => null,
            'Expires' => null,
            'Secure' => false,
            'Discard' => false,
            'HttpOnly' => false,
        ];
        $expectedCacheData = ['queryServer' => self::QUERY_SERVER, 'cookies' => [$expectedSetCookieData], 'crumb' => self::CRUMB_VALUE];

        $cookieJar = new CookieJar(false, [new SetCookie($expectedSetCookieData)]);
        $sessionContext = new SessionContext($this->httpClient, self::QUERY_SERVER, $cookieJar, self::CRUMB_VALUE);

        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->with(self::CACHE_KEY)
            ->willReturn($this->cacheItem);

        $this->cacheItem
            ->expects($this->once())
            ->method('set')
            ->with($expectedCacheData);

        $this->cacheItem
            ->expects($this->once())
            ->method('expiresAfter')
            ->with(self::CACHE_TTL);

        $this->cache
            ->expects($this->once())
            ->method('save')
            ->with($this->cacheItem);

        $this->storage->setSessionContext($sessionContext);
    }

    #[Test]
    public function invalidateSessionContext_clearCache_deletesCacheItem(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('deleteItem')
            ->with(self::CACHE_KEY);

        $this->storage->invalidateSessionContext();
    }
}
