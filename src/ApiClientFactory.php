<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * @final
 */
class ApiClientFactory
{
    public static function createApiClient(?ClientInterface $guzzleClient = null): ApiClient
    {
        $userAgent = UserAgent::getRandomUserAgent();
        $guzzleClient = $guzzleClient ?: new Client(['headers' => ['User-Agent' => $userAgent]]);
        $resultDecoder = new ResultDecoder(new ValueMapper());

        return new ApiClient($guzzleClient, $resultDecoder);
    }
}
