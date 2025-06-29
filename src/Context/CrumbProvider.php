<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

/**
 * @final
 */
class CrumbProvider
{
    public function __construct(private readonly CookieProvider $cookieProvider)
    {
    }

    public function acquireCrumb(SessionContext $sessionContext): SessionContext
    {
        $sessionContext = $this->cookieProvider->acquireCookies($sessionContext);

        // Get crumb value
        $initialUrl = 'https://query'.$sessionContext->queryServer.'.finance.yahoo.com/v1/test/getcrumb';
        $crumb = (string) $sessionContext->httpClient->request('GET', $initialUrl, ['cookies' => $sessionContext->cookies])->getBody();

        return new SessionContext(
            $sessionContext->httpClient,
            $sessionContext->queryServer,
            $sessionContext->cookies,
            $crumb,
        );
    }
}
