<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context\Provider;

/**
 * @final
 */
class QueryServerProvider
{
    public static function getRandomQueryServer(): int
    {
        return random_int(1, 2);
    }
}
