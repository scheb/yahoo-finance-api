<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\HttpClient\GuzzleHttpClientFactory;
use Scheb\YahooFinanceApi\Session\SessionManager;

/**
 * @final
 */
class ApiClientFactory
{
    public static function createApiClient(array $clientOptions = []): ApiClient
    {
        $resultDecoder = new ResultDecoder(new ValueMapper());
        $sessionManager = new SessionManager(new GuzzleHttpClientFactory($clientOptions));

        return new ApiClient($sessionManager, $resultDecoder);
    }
}
