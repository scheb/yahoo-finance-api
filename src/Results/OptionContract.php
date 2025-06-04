<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class OptionContract implements \JsonSerializable
{
    private ?string $contractSymbol;
    private ?float $strike;
    private ?string $currency;
    private ?float $lastPrice;
    private ?float $change;
    private ?float $percentChange;
    private ?int $volume;
    private ?int $openInterest;
    private ?float $bid;
    private ?float $ask;
    private ?string $contractSize;
    private ?\DateTimeInterface $expiration;
    private ?\DateTimeInterface $lastTradeDate;
    private ?float $impliedVolatility;
    private ?bool $inTheMoney;

    public function __construct(array $values)
    {
        foreach ($values as $property => $value) {
            $this->{$property} = $value;
        }
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function getContractSymbol(): ?string
    {
        return $this->contractSymbol;
    }

    public function getStrike(): ?float
    {
        return $this->strike;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getLastPrice(): ?float
    {
        return $this->lastPrice;
    }

    public function getChange(): ?float
    {
        return $this->change;
    }

    public function getPercentChange(): ?float
    {
        return $this->percentChange;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function getOpenInterest(): ?int
    {
        return $this->openInterest;
    }

    public function getBid(): ?float
    {
        return $this->bid;
    }

    public function getAsk(): ?float
    {
        return $this->ask;
    }

    public function getContractSize(): ?string
    {
        return $this->contractSize;
    }

    public function getExpiration(): ?\DateTimeInterface
    {
        return $this->expiration;
    }

    public function getLastTradeDate(): ?\DateTimeInterface
    {
        return $this->lastTradeDate;
    }

    public function getImpliedVolatility(): ?float
    {
        return $this->impliedVolatility;
    }

    public function getInTheMoney(): ?bool
    {
        return $this->inTheMoney;
    }
}
