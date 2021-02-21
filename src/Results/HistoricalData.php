<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

class HistoricalData implements \JsonSerializable
{
    private $date;
    private $open;
    private $high;
    private $low;
    private $close;
    private $adjClose;
    private $volume;

    public function __construct(\DateTime $date, ?float $open, ?float $high, ?float $low, ?float $close, ?float $adjClose, ?int $volume)
    {
        $this->date = $date;
        $this->open = $open;
        $this->high = $high;
        $this->low = $low;
        $this->close = $close;
        $this->adjClose = $adjClose;
        $this->volume = $volume;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getOpen(): ?float
    {
        return $this->open;
    }

    public function getHigh(): ?float
    {
        return $this->high;
    }

    public function getLow(): ?float
    {
        return $this->low;
    }

    public function getClose(): ?float
    {
        return $this->close;
    }

    public function getAdjClose(): ?float
    {
        return $this->adjClose;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }
}
