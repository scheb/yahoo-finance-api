<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context\Storage;

use Scheb\YahooFinanceApi\Context\Provider\QueryServerProvider;
use Scheb\YahooFinanceApi\Context\SessionContext;
use Scheb\YahooFinanceApi\HttpClient\HttpClientFactoryInterface;

/**
 * @final
 */
class SessionContextStorage implements SessionContextStorageInterface
{
    private ?SessionContext $sessionContext = null;

    public function __construct(private HttpClientFactoryInterface $httpClientFactory)
    {
    }

    public function setSessionContext(SessionContext $sessionContext): void
    {
        $this->sessionContext = $sessionContext;
    }

    public function getSessionContext(): SessionContext
    {
        if (null === $this->sessionContext) {
            $this->sessionContext = new SessionContext(
                $this->httpClientFactory->createHttpClient(),
                QueryServerProvider::getRandomQueryServer(),
            );
        }

        return $this->sessionContext;
    }

    public function invalidateSessionContext(): void
    {
        $this->sessionContext = null;
    }
}
