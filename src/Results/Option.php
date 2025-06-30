<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Option implements \JsonSerializable
{
    private ?\DateTimeInterface $expirationDate = null;
    private ?bool $hasMiniOptions = null;
    private ?array $calls = null;
    private ?array $puts = null;

    public function __construct(array $values)
    {
        foreach ($values as $property => $value) {
            $this->{$property} = $value;
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'expirationDate' => $this->expirationDate,
            'hasMiniOptions' => $this->hasMiniOptions,
            'calls' => array_map(fn (OptionContract $optionContract): array => $optionContract->jsonSerialize(), $this->calls ?? []),
            'puts' => array_map(fn (OptionContract $optionContract): array => $optionContract->jsonSerialize(), $this->puts ?? []),
        ];
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function getHasMiniOptions(): ?bool
    {
        return $this->hasMiniOptions;
    }

    public function getCalls(): ?array
    {
        return $this->calls ?? [];
    }

    public function getPuts(): ?array
    {
        return $this->puts ?? [];
    }
}
