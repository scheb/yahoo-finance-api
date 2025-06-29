<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

use Psr\Http\Message\ResponseInterface;
use Scheb\YahooFinanceApi\HttpClient\HttpClientFactoryInterface;

/**
 * @final
 */
class SessionManager implements SessionManagerInterface
{
    private ?SessionContext $sessionContext = null;

    public function __construct(
        private readonly HttpClientFactoryInterface $httpClientFactory,
        private readonly CrumbProvider $crumbProvider,
    ) {
    }

    public function getSessionContext(): SessionContext
    {
        if (null === $this->sessionContext) {
            return $this->renewSession();
        }

        return $this->sessionContext;
    }

    public function renewSession(): SessionContext
    {
        return $this->sessionContext = new SessionContext(
            $this->httpClientFactory->createHttpClient(),
            QueryServer::getRandomQueryServer(),
        );
    }

    public function request(string $method, string $url): ResponseInterface
    {
        $sessionContext = $this->getSessionContext();

        $requestOptions = [];
        $url = str_replace('{queryServer}', (string) $sessionContext->queryServer, $url);

        // Acquire crumb
        if (str_contains($url, '{crumb}')) {
            $sessionContext = $this->crumbProvider->acquireCrumb($sessionContext);

            /** @psalm-suppress PossiblyNullArgument Crumb will always be set at this point */
            $url = str_replace('{crumb}', $sessionContext->crumb, $url);
            $requestOptions = ['cookies' => $sessionContext->cookies];
        }

        return $sessionContext->httpClient->request($method, $url, $requestOptions);
    }
}
