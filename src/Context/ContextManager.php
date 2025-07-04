<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

use Psr\Http\Message\ResponseInterface;
use Scheb\YahooFinanceApi\Context\Provider\CrumbProvider;
use Scheb\YahooFinanceApi\Context\Storage\SessionContextStorageInterface;

/**
 * @final
 */
class ContextManager implements ContextManagerInterface
{
    public function __construct(
        private readonly SessionContextStorageInterface $sessionContextStorage,
        private readonly CrumbProvider $crumbProvider,
    ) {
    }

    public function renewSession(): void
    {
        $this->sessionContextStorage->invalidateSessionContext();
    }

    public function request(string $method, string $url): ResponseInterface
    {
        $initialSessionContext = $sessionContext = $this->sessionContextStorage->getSessionContext();

        $requestOptions = [];
        $url = str_replace('{queryServer}', (string) $sessionContext->queryServer, $url);

        // Acquire crumb
        if (str_contains($url, '{crumb}')) {
            if (null === $sessionContext->crumb) {
                $sessionContext = $this->crumbProvider->acquireCrumb($sessionContext);
            }

            /** @psalm-suppress PossiblyNullArgument Crumb will always be set at this point */
            $url = str_replace('{crumb}', urlencode($sessionContext->crumb), $url);
            $requestOptions = ['cookies' => $sessionContext->cookies];
        }

        $response = $sessionContext->httpClient->request($method, $url, $requestOptions);

        // Store the new session context when it changed
        if ($sessionContext !== $initialSessionContext) {
            $this->sessionContextStorage->setSessionContext($sessionContext);
        }

        return $response;
    }
}
