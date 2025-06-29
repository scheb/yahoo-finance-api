<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Unit\Context;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Scheb\YahooFinanceApi\Context\CookieProvider;
use Scheb\YahooFinanceApi\Context\CrumbProvider;
use Scheb\YahooFinanceApi\Context\SessionContext;
use Scheb\YahooFinanceApi\Tests\TestCase;

class CrumbProviderTest extends TestCase
{
    private const QUERY_SERVER = 2;
    private const CRUMB_VALUE = 'crumb-value';

    private MockObject|ClientInterface $mockHttpClient;
    private MockObject|CookieProvider $mockCookieProvider;
    private MockObject|CookieJarInterface $cookieJar;
    private CrumbProvider $crumbProvider;
    private SessionContext $sessionContext;

    protected function setUp(): void
    {
        $this->mockHttpClient = $this->createMock(ClientInterface::class);
        $this->mockCookieProvider = $this->createMock(CookieProvider::class);
        $this->crumbProvider = new CrumbProvider($this->mockCookieProvider);
        $this->cookieJar = $this->createMock(CookieJarInterface::class);

        $this->sessionContext = new SessionContext($this->mockHttpClient, self::QUERY_SERVER);
        $this->sessionContext->cookies = $this->createMock(CookieJarInterface::class);
    }

    private function createCrumbResponse(): MockObject|ResponseInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream
            ->expects($this->once())
            ->method('__toString')
            ->willReturn(self::CRUMB_VALUE);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        return $response;
    }

    #[Test]
    public function acquireCrumb_successfulRequest_returnsSessionContextWithCrumb(): void
    {
        $this->mockCookieProvider
            ->expects($this->once())
            ->method('acquireCookies')
            ->with($this->sessionContext)
            ->willReturn($this->sessionContext);

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://query2.finance.yahoo.com/v1/test/getcrumb',
                ['cookies' => $this->cookieJar]
            )
            ->willReturn($this->createCrumbResponse());

        $result = $this->crumbProvider->acquireCrumb($this->sessionContext);

        $this->assertSame($this->sessionContext, $result);
        $this->assertEquals(self::CRUMB_VALUE, $result->crumb);
    }

    #[Test]
    public function acquireCrumb_withExistingCrumb_overwritesExistingCrumb(): void
    {
        $this->sessionContext->crumb = 'old-crumb';

        $this->mockCookieProvider
            ->expects($this->once())
            ->method('acquireCookies')
            ->willReturn($this->sessionContext);

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->createCrumbResponse());

        $result = $this->crumbProvider->acquireCrumb($this->sessionContext);

        $this->assertEquals(self::CRUMB_VALUE, $result->crumb);
    }

    #[Test]
    public function acquireCrumb_withHttpClientException_throwsException(): void
    {
        $exception = new \Exception('Network error');

        $this->mockCookieProvider
            ->expects($this->once())
            ->method('acquireCookies')
            ->willReturn($this->sessionContext);

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Network error');

        $this->crumbProvider->acquireCrumb($this->sessionContext);
    }

    #[Test]
    public function acquireCrumb_withCookieProviderException_throwsException(): void
    {
        $exception = new \Exception('Cookie provider error');

        $this->mockCookieProvider
            ->expects($this->once())
            ->method('acquireCookies')
            ->willThrowException($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cookie provider error');

        $this->crumbProvider->acquireCrumb($this->sessionContext);
    }
}
