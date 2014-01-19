<?php
namespace Scheb\YahooFinanceApi\Exception;

class ApiException extends \Exception
{
    const UNAVIALABLE = 1;
    const EMPTY_RESULT = 2;
    const INVALID_RESULT = 3;
}
