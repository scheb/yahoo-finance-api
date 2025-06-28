<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\Context\SessionManager;
use Scheb\YahooFinanceApi\HttpClient\GuzzleHttpClientFactory;

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
