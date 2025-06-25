<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

/**
 * @final
 */
class QueryServer
{
    public static function getRandomQueryServer(): int
    {
        return random_int(1, 2);
    }
}
