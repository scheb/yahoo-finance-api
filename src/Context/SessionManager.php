<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

use Psr\Http\Message\ResponseInterface;

/**
 * @final
 */
class SessionManager implements SessionManagerInterface
{
    public function __construct(
        private readonly SessionContextStorage $sessionContextStorage,
        private readonly CrumbProvider $crumbProvider,
    ) {
    }

    public function renewSession(): void
    {
        $this->sessionContextStorage->invalidateSessionContext();
    }

    public function request(string $method, string $url): ResponseInterface
    {
        $sessionContext = $this->sessionContextStorage->getSessionContext();

        $requestOptions = [];
        $url = str_replace('{queryServer}', (string) $sessionContext->queryServer, $url);

        // Acquire crumb
        if (str_contains($url, '{crumb}')) {
            if (null === $sessionContext->crumb) {
                $sessionContext = $this->crumbProvider->acquireCrumb($sessionContext);
            }

            /** @psalm-suppress PossiblyNullArgument Crumb will always be set at this point */
            $url = str_replace('{crumb}', $sessionContext->crumb, $url);
            $requestOptions = ['cookies' => $sessionContext->cookies];
        }

        return $sessionContext->httpClient->request($method, $url, $requestOptions);
    }
}
