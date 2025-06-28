<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\HttpClient;

use GuzzleHttp\ClientInterface;

interface HttpClientFactoryInterface
{
    public function createHttpClient(): ClientInterface;
}
