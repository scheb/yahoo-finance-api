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
    private SessionContext $sessionContext;

    protected function setUp(): void
    {
        $this->mockHttpClient = $this->createMock(ClientInterface::class);
        $this->mockResponse = $this->createMock(ResponseInterface::class);
        $this->cookieProvider = new CookieProvider();
        $this->sessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER);
    }

    #[Test]
    public function acquireCookies_withValidSessionContext_returnsSessionContextWithCookies(): void
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://fc.yahoo.com',
                $this->callback(fn (array $options) => isset($options['cookies']) && $options['cookies'] instanceof CookieJarInterface)
            )
            ->willReturn($this->mockResponse);

        $result = $this->cookieProvider->acquireCookies($this->sessionContext);

        $this->assertSame($this->sessionContext, $result);
        $this->assertInstanceOf(CookieJarInterface::class, $result->cookies);
    }

    #[Test]
    public function acquireCookies_withExistingCookies_overwritesExistingCookies(): void
    {
        $existingCookieJar = $this->createMock(CookieJarInterface::class);
        $this->sessionContext->cookies = $existingCookieJar;

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->mockResponse);

        $result = $this->cookieProvider->acquireCookies($this->sessionContext);

        $this->assertNotSame($existingCookieJar, $result->cookies);
        $this->assertInstanceOf(CookieJarInterface::class, $result->cookies);
    }

    #[Test]
    public function acquireCookies_withHttpClientException_throwsException(): void
    {
        $exception = new \Exception('Network error');

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Network error');

        $this->cookieProvider->acquireCookies($this->sessionContext);
    }
}
