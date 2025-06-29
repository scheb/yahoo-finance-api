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
use Scheb\YahooFinanceApi\Context\SessionManager;
use Scheb\YahooFinanceApi\HttpClient\HttpClientFactoryInterface;
use Scheb\YahooFinanceApi\Tests\TestCase;

class SessionManagerTest extends TestCase
{
    private const QUERY_SERVER = 2;
    private const CRUMB_VALUE = 'test-crumb-value';

    private MockObject|HttpClientFactoryInterface $mockHttpClientFactory;
    private MockObject|CrumbProvider $mockCrumbProvider;
    private MockObject|ClientInterface $mockHttpClient;
    private MockObject|ResponseInterface $mockResponse;
    private MockObject|CookieJarInterface $mockCookieJar;
    private SessionManager $sessionManager;

    protected function setUp(): void
    {
        $this->mockHttpClient = $this->createMock(ClientInterface::class);
        $this->mockHttpClientFactory = $this->createMock(HttpClientFactoryInterface::class);
        $this->mockHttpClientFactory
            ->expects($this->any())
            ->method('createHttpClient')
            ->willReturn($this->mockHttpClient);

        $this->mockCrumbProvider = $this->createMock(CrumbProvider::class);
        $this->mockResponse = $this->createMock(ResponseInterface::class);
        $this->mockCookieJar = $this->createMock(CookieJarInterface::class);

        $this->sessionManager = new SessionManager(
            $this->mockHttpClientFactory,
            $this->mockCrumbProvider
        );
    }

    #[Test]
    public function renewSession_whenSessionContextExists_createsNewSessionContextAndReturnsIt(): void
    {
        $firstResult = $this->sessionManager->renewSession();
        $secondResult = $this->sessionManager->renewSession();

        $this->assertNotSame($firstResult, $secondResult);
    }

    #[Test]
    public function request_withoutCrumbPlaceholder_makesRequestWithoutCrumb(): void
    {
        $method = 'GET';
        $url = 'https://example.com/api/data';

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

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with($method, $this->callback(fn (string $actualUrl) => !str_contains($actualUrl, '{queryServer}')), [])
            ->willReturn($this->mockResponse);

        $result = $this->sessionManager->request($method, $url);

        $this->assertSame($this->mockResponse, $result);
    }

    #[Test]
    public function request_withCrumbPlaceholder_acquiresCrumbAndIncludesCookies(): void
    {
        $method = 'GET';
        $url = 'https://example.com/api/data?crumb={crumb}';
        $expectedUrl = 'https://example.com/api/data?crumb='.self::CRUMB_VALUE;

        $sessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER, $this->mockCookieJar, self::CRUMB_VALUE);

        $this->mockCrumbProvider
            ->expects($this->once())
            ->method('acquireCrumb')
            ->willReturn($sessionContext);

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with($method, $expectedUrl, ['cookies' => $this->mockCookieJar])
            ->willReturn($this->mockResponse);

        $result = $this->sessionManager->request($method, $url);

        $this->assertSame($this->mockResponse, $result);
    }

    #[Test]
    public function request_withCrumbPlaceholderAndCrumbProviderException_throwsException(): void
    {
        $method = 'GET';
        $url = 'https://example.com/api/data?crumb={crumb}';
        $exception = new \Exception('Crumb provider error');

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

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('HTTP client error');

        $this->sessionManager->request($method, $url);
    }
}
