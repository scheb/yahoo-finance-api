<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

/**
 * @final
 */
class GuzzleHttpClientFactory implements HttpClientFactoryInterface
{
    public function __construct(private readonly array $clientOptions = [])
    {
    }

    public function createHttpClient(): ClientInterface
    {
        $clientOptions = $this->clientOptions;

        // Remove the default User-Agent header when curl-impersonate is used
        if (false !== getenv('CURL_IMPERSONATE')) {
            $handlerStack = $clientOptions['handler'] ?? HandlerStack::create();
            $handlerStack->push(Middleware::mapRequest(fn (RequestInterface $request) => $request->withoutHeader('User-Agent')));
            $clientOptions['handler'] = $handlerStack;
        }

        return new Client($clientOptions);
    }
}
