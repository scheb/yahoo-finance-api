<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Unit\Context;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Scheb\YahooFinanceApi\Context\RetryableSessionManager;
use Scheb\YahooFinanceApi\Context\SessionContext;
use Scheb\YahooFinanceApi\Context\SessionManagerInterface;

class RetryableSessionManagerTest extends TestCase
{
    public const MAX_TRIES = 3;
    public const RETRY_DELAY = 0;

    private MockObject|SessionManagerInterface $mockSessionManager;
    private RetryableSessionManager $retryableSessionManager;

    protected function setUp(): void
    {
        $this->mockSessionManager = $this->createMock(SessionManagerInterface::class);
        $this->retryableSessionManager = new RetryableSessionManager(
            $this->mockSessionManager,
            self::MAX_TRIES,
            self::RETRY_DELAY,
        );
    }

    #[Test]
    public function getSessionContext_whenCalled_delegatesToWrappedSessionManager(): void
    {
        $expectedContext = $this->createMock(SessionContext::class);

        $this->mockSessionManager
            ->expects($this->once())
            ->method('getSessionContext')
            ->willReturn($expectedContext);

        $result = $this->retryableSessionManager->getSessionContext();

        $this->assertSame($expectedContext, $result);
    }

    #[Test]
    public function renewSession_whenCalled_delegatesToWrappedSessionManager(): void
    {
        $expectedContext = $this->createMock(SessionContext::class);

        $this->mockSessionManager
            ->expects($this->once())
            ->method('renewSession')
            ->willReturn($expectedContext);

        $result = $this->retryableSessionManager->renewSession();

        $this->assertSame($expectedContext, $result);
    }

    #[Test]
    public function request_successfulOnFirstTry_returnsResponse(): void
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $this->mockSessionManager
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'https://example.com')
            ->willReturn($expectedResponse);

        $result = $this->retryableSessionManager->request('GET', 'https://example.com');

        $this->assertSame($expectedResponse, $result);
    }

    #[Test]
    public function request_failsFirstTryThenSucceeds_retriesAndReturnsResponse(): void
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $exception = new \Exception('Network error');

        $this->mockSessionManager
            ->expects($this->exactly(2))
            ->method('request')
            ->with('GET', 'https://example.com')
            ->willReturnOnConsecutiveCalls(
                $this->throwException($exception),
                $expectedResponse
            );

        $this->mockSessionManager
            ->expects($this->once())
            ->method('renewSession');

        $result = $this->retryableSessionManager->request('GET', 'https://example.com');

        $this->assertSame($expectedResponse, $result);
    }

    #[Test]
    public function request_failsAllTries_throwsLastException(): void
    {
        $exception1 = new \Exception('First error');
        $exception2 = new \Exception('Second error');
        $exception3 = new \Exception('Third error');

        $this->mockSessionManager
            ->expects($this->exactly(3))
            ->method('request')
            ->with('GET', 'https://example.com')
            ->willReturnOnConsecutiveCalls(
                $this->throwException($exception1),
                $this->throwException($exception2),
                $this->throwException($exception3)
            );

        $this->mockSessionManager
            ->expects($this->exactly(2))
            ->method('renewSession');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Third error');

        $this->retryableSessionManager->request('GET', 'https://example.com');
    }

    #[Test]
    public function request_withRetryDelay_retryDelayIsApplied(): void
    {
        $retryableSessionManager = new RetryableSessionManager(
            $this->mockSessionManager,
            2,  // maxTries
            500 // retryDelay (500ms)
        );

        $exception = new \Exception('Network error');

        $this->mockSessionManager
            ->expects($this->exactly(2))
            ->method('request')
            ->with('GET', 'https://example.com')
            ->willReturnOnConsecutiveCalls(
                $this->throwException($exception),
                $this->createMock(ResponseInterface::class)
            );

        $startTime = microtime(true);
        $retryableSessionManager->request('GET', 'https://example.com');
        $endTime = microtime(true);

        // Verify that some delay was applied (allowing for some tolerance)
        $executionTime = ($endTime - $startTime) * 1000000; // Convert to microseconds
        $this->assertGreaterThan(500, $executionTime); // At least 500ms should have passed
    }
}
