<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

class SplitData implements \JsonSerializable
{
    private $date;
    private $stock_splits;

    public function __construct(\DateTime $date, ?string $stock_splits )
    {
        $this->date = $date;
        $this->stock_splits = $stock_splits;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getStockSplits(): ?string
    {
        return $this->stock_splits;
    }
}
