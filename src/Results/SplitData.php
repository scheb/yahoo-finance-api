<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

class SplitData implements \JsonSerializable
{
    private $date;
    private $stockSplits;

    public function __construct(\DateTime $date, ?string $stockSplits)
    {
        $this->date = $date;
        $this->stockSplits = $stockSplits;
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
        return $this->stockSplits;
    }
}
