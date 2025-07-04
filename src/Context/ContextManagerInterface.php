<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * @final
 */
interface ContextManagerInterface
{
    public function renewSession(): void;

    /**
     * @throws GuzzleException
     */
    public function request(string $method, string $url): ResponseInterface;
}
