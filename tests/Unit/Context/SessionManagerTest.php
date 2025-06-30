<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Unit\Context;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Scheb\YahooFinanceApi\Context\CrumbProvider;
use Scheb\YahooFinanceApi\Context\SessionContext;
use Scheb\YahooFinanceApi\Context\SessionContextStorageInterface;
use Scheb\YahooFinanceApi\Context\SessionManager;
use Scheb\YahooFinanceApi\Tests\TestCase;

class SessionManagerTest extends TestCase
{
    private const QUERY_SERVER = 2;
    private const CRUMB_VALUE = 'test-crumb-value';

    private MockObject|SessionContextStorageInterface $sessionContextStorage;
    private MockObject|CrumbProvider $mockCrumbProvider;
    private MockObject|ClientInterface $mockHttpClient;
    private MockObject|ResponseInterface $mockResponse;
    private MockObject|CookieJarInterface $mockCookieJar;
    private SessionManager $sessionManager;

    protected function setUp(): void
    {
        $this->mockHttpClient = $this->createMock(ClientInterface::class);
        $this->sessionContextStorage = $this->createMock(SessionContextStorageInterface::class);
        $this->mockCrumbProvider = $this->createMock(CrumbProvider::class);
        $this->mockResponse = $this->createMock(ResponseInterface::class);
        $this->mockCookieJar = $this->createMock(CookieJarInterface::class);

        $this->sessionManager = new SessionManager(
            $this->sessionContextStorage,
            $this->mockCrumbProvider
        );
    }

    #[Test]
    public function renewSession_whenSessionRenewed_invalidateSessionContextStorage(): void
    {
        $this->sessionContextStorage
            ->expects($this->once())
            ->method('invalidateSessionContext');

        $this->sessionManager->renewSession();
    }

    #[Test]
    public function request_withoutCrumbPlaceholder_makesRequestWithoutCrumb(): void
    {
        $method = 'GET';
        $url = 'https://example.com/api/data';

        $sessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER);
        $this->sessionContextStorage
            ->expects($this->once())
            ->method('getSessionContext')
            ->willReturn($sessionContext);

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with($method, $url, [])
            ->willReturn($this->mockResponse);

        $result = $this->sessionManager->request($method, $url);

        $this->assertSame($this->mockResponse, $result);
    }

    #[Test]
    public function request_withQueryServerPlaceholder_replacesPlaceholderWithQueryServer(): void
    {
        $method = 'GET';
        $url = 'https://query{queryServer}.finance.yahoo.com/api/data';

        $sessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER);
        $this->sessionContextStorage
            ->expects($this->once())
            ->method('getSessionContext')
            ->willReturn($sessionContext);

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with($method, $this->callback(fn (string $actualUrl) => !str_contains($actualUrl, '{queryServer}')), [])
            ->willReturn($this->mockResponse);

        $result = $this->sessionManager->request($method, $url);

        $this->assertSame($this->mockResponse, $result);
    }

    #[Test]
    public function request_withCrumbPlaceholderNotAcquired_acquiresCrumbAndIncludesCookies(): void
    {
        $method = 'GET';
        $url = 'https://example.com/api/data?crumb={crumb}';
        $expectedUrl = 'https://example.com/api/data?crumb='.self::CRUMB_VALUE;

        $initialSessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER);
        $crumbSessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER, $this->mockCookieJar, self::CRUMB_VALUE);
        $this->sessionContextStorage
            ->expects($this->once())
            ->method('getSessionContext')
            ->willReturn($initialSessionContext);

        $this->sessionContextStorage
            ->expects($this->once())
            ->method('setSessionContext')
            ->with($crumbSessionContext);

        $this->mockCrumbProvider
            ->expects($this->once())
            ->method('acquireCrumb')
            ->with($initialSessionContext)
            ->willReturn($crumbSessionContext);

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with($method, $expectedUrl, ['cookies' => $this->mockCookieJar])
            ->willReturn($this->mockResponse);

        $result = $this->sessionManager->request($method, $url);

        $this->assertSame($this->mockResponse, $result);
    }

    #[Test]
    public function request_withCrumbPlaceholderAlreadyAcquired_useExistingCrumb(): void
    {
        $method = 'GET';
        $url = 'https://example.com/api/data?crumb={crumb}';
        $expectedUrl = 'https://example.com/api/data?crumb='.self::CRUMB_VALUE;

        $initialSessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER, $this->mockCookieJar, self::CRUMB_VALUE);
        $this->sessionContextStorage
            ->expects($this->once())
            ->method('getSessionContext')
            ->willReturn($initialSessionContext);

        $this->sessionContextStorage
            ->expects($this->never())
            ->method('setSessionContext');

        $this->mockCrumbProvider
            ->expects($this->never())
            ->method('acquireCrumb');

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with($method, $expectedUrl, ['cookies' => $this->mockCookieJar])
            ->willReturn($this->mockResponse);

        $result = $this->sessionManager->request($method, $url);

        $this->assertSame($this->mockResponse, $result);
    }

    #[Test]
    public function request_sessionContextUnchanged_notChangeSessionContextStorage(): void
    {
        $method = 'GET';
        $url = 'https://example.com/api/data';

        $sessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER);
        $this->sessionContextStorage
            ->expects($this->once())
            ->method('getSessionContext')
            ->willReturn($sessionContext);

        $this->sessionContextStorage
            ->expects($this->never())
            ->method('setSessionContext');

        $this->sessionManager->request($method, $url);
    }

    #[Test]
    public function request_crumbValueAlreadySet_notChangeSessionContextStorage(): void
    {
        $method = 'GET';
        $url = 'https://example.com/api/data?crumb={crumb}';

        $sessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER, $this->mockCookieJar, self::CRUMB_VALUE);
        $this->sessionContextStorage
            ->expects($this->once())
            ->method('getSessionContext')
            ->willReturn($sessionContext);

        $this->sessionContextStorage
            ->expects($this->never())
            ->method('setSessionContext');

        $this->sessionManager->request($method, $url);
    }

    #[Test]
    public function request_crumbValueAcquired_updateSessionContextStorage(): void
    {
        $method = 'GET';
        $url = 'https://example.com/api/data?crumb={crumb}';

        $initialSessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER);
        $newSessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER, $this->mockCookieJar, self::CRUMB_VALUE);
        $this->sessionContextStorage
            ->expects($this->once())
            ->method('getSessionContext')
            ->willReturn($initialSessionContext);

        $this->mockCrumbProvider
            ->expects($this->once())
            ->method('acquireCrumb')
            ->with($initialSessionContext)
            ->willReturn($newSessionContext);

        $this->sessionContextStorage
            ->expects($this->once())
            ->method('setSessionContext')
            ->with($newSessionContext);

        $this->sessionManager->request($method, $url);
    }

    #[Test]
    public function request_crumbProviderException_throwsException(): void
    {
        $method = 'GET';
        $url = 'https://example.com/api/data?crumb={crumb}';
        $exception = new \Exception('Crumb provider error');

        $initialSessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER);
        $this->sessionContextStorage
            ->expects($this->once())
            ->method('getSessionContext')
            ->willReturn($initialSessionContext);

        $this->mockCrumbProvider
            ->expects($this->once())
            ->method('acquireCrumb')
            ->willThrowException($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Crumb provider error');

        $this->sessionManager->request($method, $url);
    }

    #[Test]
    public function request_withHttpClientException_throwsException(): void
    {
        $method = 'GET';
        $url = 'https://example.com/api/data';
        $exception = new \Exception('HTTP client error');

        $initialSessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER);
        $this->sessionContextStorage
            ->expects($this->once())
            ->method('getSessionContext')
            ->willReturn($initialSessionContext);

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('HTTP client error');

        $this->sessionManager->request($method, $url);
    }
}
