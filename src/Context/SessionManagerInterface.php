<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

use Psr\Http\Message\ResponseInterface;

/**
 * @final
 */
interface SessionManagerInterface
{
    public function getSessionContext(): SessionContext;

    public function renewSession(): SessionContext;

    public function request(string $method, string $url): ResponseInterface;
}
