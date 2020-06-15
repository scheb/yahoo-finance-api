<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Exception;

class ApiException extends \Exception
{
    public const INVALID_RESPONSE = 1;
    public const INVALID_VALUE = 2;
    public const MISSING_CRUMB = 3;
}
