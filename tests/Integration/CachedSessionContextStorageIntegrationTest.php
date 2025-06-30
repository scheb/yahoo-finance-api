<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Cookie\SetCookie;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Scheb\YahooFinanceApi\Context\CachedSessionContextStorage;
use Scheb\YahooFinanceApi\Context\SessionContext;
use Scheb\YahooFinanceApi\HttpClient\GuzzleHttpClientFactory;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Marshaller\DefaultMarshaller;

class CachedSessionContextStorageIntegrationTest extends TestCase
{
    private const CACHE_KEY = 'yahoo_finance_session_context_integration_test';
    private const CACHE_TTL = 60;
    private const QUERY_SERVER = 123;
    private const CRUMB_VALUE = 'crumb-value';

    protected function setUp(): void
    {
        // Ensure clean state
        $this->createCache()->deleteItem(self::CACHE_KEY);
    }

    private function createCache(): FilesystemAdapter
    {
        $marshaller = new DefaultMarshaller(throwOnSerializationFailure: true); // Make serialization issues visible

        return new FilesystemAdapter(marshaller: $marshaller);
    }

    private function createSessionContextStorage(): CachedSessionContextStorage
    {
        return new CachedSessionContextStorage(
            new GuzzleHttpClientFactory(),
            $this->createCache(),
            self::CACHE_TTL,
            self::CACHE_KEY,
        );
    }

    #[Test]
    public function getSessionContext_cacheEmpty_returnNewSessionContext(): void
    {
        $sessionContextStorage = $this->createSessionContextStorage();
        $sessionContextStorage->invalidateSessionContext();

        $sessionContextStorage = $this->createSessionContextStorage();
        $fetchedSessionContext = $sessionContextStorage->getSessionContext();

        $this->assertInstanceOf(Client::class, $fetchedSessionContext->httpClient);
        $this->assertIsInt(self::QUERY_SERVER);
        $this->assertNull($fetchedSessionContext->cookies);
        $this->assertNull($fetchedSessionContext->crumb);
    }

    #[Test]
    public function getSessionContext_cachePopulated_returnCachedSessionContext(): void
    {
        $setCookieArray = [new SetCookie(['Name' => 'cookieName', 'Value' => 'cookieValue', 'Domain' => 'example.com'])];
        $initialSessionContext = new SessionContext(new Client(), self::QUERY_SERVER, new CookieJar(false, $setCookieArray), self::CRUMB_VALUE);
        $sessionContextStorage = $this->createSessionContextStorage();
        $sessionContextStorage->setSessionContext($initialSessionContext);

        $sessionContextStorage = $this->createSessionContextStorage();
        $fetchedSessionContext = $sessionContextStorage->getSessionContext();

        $this->assertNotSame($fetchedSessionContext, $initialSessionContext);
        $this->assertInstanceOf(Client::class, $fetchedSessionContext->httpClient);
        $this->assertEquals(self::QUERY_SERVER, $fetchedSessionContext->queryServer);
        $this->assertInstanceOf(CookieJarInterface::class, $fetchedSessionContext->cookies);
        $this->assertEquals($setCookieArray, iterator_to_array($fetchedSessionContext->cookies));
        $this->assertEquals(self::CRUMB_VALUE, $fetchedSessionContext->crumb);
    }
}
