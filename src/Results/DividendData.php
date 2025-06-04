<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

class DividendData implements \JsonSerializable
{
    public function __construct(
        private readonly \DateTime $date,
        private readonly ?float $dividends,
    ) {
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
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
