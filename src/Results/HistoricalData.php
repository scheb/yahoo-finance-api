<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 */
class HistoricalData implements \JsonSerializable
{
    public function __construct(
        private readonly \DateTime $date,
        private readonly ?float $open,
        private readonly ?float $high,
        private readonly ?float $low,
        private readonly ?float $close,
        private readonly ?float $adjClose,
        private readonly ?int $volume,
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
