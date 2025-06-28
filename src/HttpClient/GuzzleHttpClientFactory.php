<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class GuzzleHttpClientFactory implements HttpClientFactoryInterface
{
    public function __construct(
        private readonly array $presetHeaders = [],
        private readonly array $clientOptions = [],
    ) {
    }

    public function createHttpClient(): ClientInterface
    {
        return new Client(['headers' => $this->presetHeaders, ...$this->clientOptions]);
    }
}
