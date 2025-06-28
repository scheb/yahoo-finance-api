<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\Context\RetryableSessionManager;
use Scheb\YahooFinanceApi\Context\SessionManager;
use Scheb\YahooFinanceApi\HttpClient\GuzzleHttpClientFactory;

/**
 * @final
 */
class ApiClientFactory
{
    public static function createApiClient(
        array $clientOptions = [],
        int $retries = 0,
        int $retryDelay = 0,
    ): ApiClient {
        $resultDecoder = new ResultDecoder(new ValueMapper());
        $sessionManager = new SessionManager(new GuzzleHttpClientFactory($clientOptions));
        if ($retries > 0) {
            $sessionManager = new RetryableSessionManager($sessionManager, $retries + 1, $retryDelay);
        }

        return new ApiClient($sessionManager, $resultDecoder);
    }
}
