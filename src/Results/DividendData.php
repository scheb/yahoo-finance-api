<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 */
class DividendData implements \JsonSerializable
{
    public function __construct(
        private readonly \DateTime $date,
        private readonly ?float $dividends,
    ) {
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            get_class_vars(self::class),
            get_object_vars($this)
        );
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getDividends(): ?float
    {
        return $this->dividends;
    }
}
