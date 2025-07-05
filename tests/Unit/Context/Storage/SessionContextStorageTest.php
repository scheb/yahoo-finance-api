<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Unit\Context\Storage;

use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Scheb\YahooFinanceApi\Context\SessionContext;
use Scheb\YahooFinanceApi\Context\Storage\SessionContextStorage;
use Scheb\YahooFinanceApi\HttpClient\HttpClientFactoryInterface;
use Scheb\YahooFinanceApi\Tests\TestCase;

class SessionContextStorageTest extends TestCase
{
    private const QUERY_SERVER = 2;

    private MockObject|ClientInterface $httpClient;
    private SessionContextStorage $storage;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $httpClientFactory = $this->createMock(HttpClientFactoryInterface::class);
        $httpClientFactory
            ->expects($this->any())
            ->method('createHttpClient')
            ->willReturn($this->httpClient);

        $this->storage = new SessionContextStorage($httpClientFactory);
    }

    #[Test]
    public function getSessionContext_whenSessionContextIsNull_createsNewSessionContext(): void
    {
        $result = $this->storage->getSessionContext();

        $this->assertSame($this->httpClient, $result->httpClient);
        $this->assertIsInt($result->queryServer);
        $this->assertNull($result->cookies);
        $this->assertNull($result->crumb);
    }

    #[Test]
    public function getSessionContext_whenSessionContextAlreadyExists_returnsExistingSessionContext(): void
    {
        // First call creates the session context
        $firstResult = $this->storage->getSessionContext();

        // Second call should return the same instance
        $secondResult = $this->storage->getSessionContext();

        $this->assertSame($firstResult, $secondResult);
    }

    #[Test]
    public function setSessionContext_newSessionContextGiven_storesSessionContext(): void
    {
        $sessionContext = new SessionContext($this->httpClient, self::QUERY_SERVER);
        $this->storage->setSessionContext($sessionContext);

        $result = $this->storage->getSessionContext();
        $this->assertSame($sessionContext, $result);
    }

    #[Test]
    public function setSessionContext_overwriteExistingSessionContext_returnLatestSessionContext(): void
    {
        // Create initial session context
        $initialResult = $this->storage->getSessionContext();

        // Set a new session context
        $newSessionContext = new SessionContext($this->httpClient, self::QUERY_SERVER);
        $this->storage->setSessionContext($newSessionContext);

        $result = $this->storage->getSessionContext();
        $this->assertNotSame($initialResult, $result);
        $this->assertSame($newSessionContext, $result);
    }

    #[Test]
    public function invalidateSessionContext_clearStoredSessionContext_returnFreshSessionContext(): void
    {
        // Create initial session context
        $initialResult = $this->storage->getSessionContext();

        // Invalidate the session context
        $this->storage->invalidateSessionContext();

        // Get session context again - should create a new one
        $newResult = $this->storage->getSessionContext();

        $this->assertNotSame($initialResult, $newResult);
        $this->assertSame($this->httpClient, $newResult->httpClient);
        $this->assertIsInt($newResult->queryServer);
        $this->assertNull($newResult->cookies);
        $this->assertNull($newResult->crumb);
    }
}
