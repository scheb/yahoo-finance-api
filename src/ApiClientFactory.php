<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class ApiClientFactory
{
    public static function createApiClient(ClientInterface $guzzleClient = null): ApiClient
    {
        $guzzleClient = $guzzleClient ? $guzzleClient : new Client();
        $resultDecoder = new ResultDecoder(new ValueMapper());

        return new ApiClient($guzzleClient, $resultDecoder);
    }
}
