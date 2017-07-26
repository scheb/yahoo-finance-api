<?php
namespace Scheb\YahooFinanceApi;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class ApiClientFactory
{
    /**
     * @param ClientInterface|null $guzzleClient
     *
     * @return ApiClient
     */
    public static function createApiClient(ClientInterface $guzzleClient = null)
    {
        $guzzleClient = $guzzleClient ? $guzzleClient : new Client();
        $resultDecoder = new ResultDecoder();

        return new ApiClient($guzzleClient, $resultDecoder);
    }
}
