<?php
namespace Scheb\YahooFinanceApi\Exception;

class ApiException extends \Exception
{
    const INVALID_RESPONSE = 1;
    const INVALID_VALUE = 2;
    const MISSING_CRUMB = 3;
}
