<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

interface ValueMapperInterface
{
    public const TYPE_FLOAT = 'float';
    public const TYPE_INT = 'int';
    public const TYPE_DATE = 'date';
    public const TYPE_STRING = 'string';
    public const TYPE_BOOL = 'bool';

    /**
     * @param mixed $rawValue
     *
     * @return mixed
     */
    public function mapValue($rawValue, string $type);
}
