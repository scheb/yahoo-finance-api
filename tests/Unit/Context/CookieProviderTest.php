<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Unit\Context;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Scheb\YahooFinanceApi\Context\CookieProvider;
use Scheb\YahooFinanceApi\Context\SessionContext;
use Scheb\YahooFinanceApi\Tests\TestCase;

class CookieProviderTest extends TestCase
{
    private const QUERY_SERVER = 2;

    private MockObject|ClientInterface $mockHttpClient;
    private MockObject|ResponseInterface $mockResponse;
    private CookieProvider $cookieProvider;

    protected function setUp(): void
    {
        $this->mockHttpClient = $this->createMock(ClientInterface::class);
        $this->mockResponse = $this->createMock(ResponseInterface::class);
        $this->cookieProvider = new CookieProvider();
    }

    #[Test]
    public function acquireCookies_withValidSessionContext_returnsNewSessionContextWithCookies(): void
    {
        $sessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER);

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://fc.yahoo.com',
                $this->callback(fn (array $options) => isset($options['cookies']) && $options['cookies'] instanceof CookieJarInterface)
            )
            ->willReturn($this->mockResponse);

        $result = $this->cookieProvider->acquireCookies($sessionContext);

        $this->assertSame($this->mockHttpClient, $result->httpClient);
        $this->assertEquals(self::QUERY_SERVER, $result->queryServer);
        $this->assertInstanceOf(CookieJarInterface::class, $result->cookies);
        $this->assertNull($result->crumb);
    }

    #[Test]
    public function acquireCookies_withExistingCookies_overwritesExistingCookies(): void
    {
        $existingCookieJar = $this->createMock(CookieJarInterface::class);
        $sessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER, $existingCookieJar);

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->mockResponse);

        $result = $this->cookieProvider->acquireCookies($sessionContext);

        $this->assertNotSame($existingCookieJar, $result->cookies);
        $this->assertInstanceOf(CookieJarInterface::class, $result->cookies);
    }

    #[Test]
    public function acquireCookies_withHttpClientException_throwsException(): void
    {
        $sessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER);
        $exception = new \Exception('Network error');

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Network error');

        $this->cookieProvider->acquireCookies($sessionContext);
    }
}
