<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class OptionChain implements \JsonSerializable
{
    private ?string $underlyingSymbol = null;
    private ?array $expirationDates = null;
    private ?array $strikes = null;
    private ?bool $hasMiniOptions = null;
    private ?array $options = null;

    public function __construct(array $values)
    {
        foreach ($values as $property => $value) {
            $this->{$property} = $value;
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'underlyingSymbol' => $this->underlyingSymbol,
            'expirationDates' => $this->expirationDates,
            'strikes' => $this->strikes,
            'hasMiniOptions' => $this->hasMiniOptions,
            'options' => array_map(fn (Option $option): array => $option->jsonSerialize(), $this->options ?? []),
        ];
    }

    public function getUnderlyingSymbol(): ?string
    {
        return $this->underlyingSymbol;
    }

    public function getExpirationDates(): array
    {
        return $this->expirationDates ?? [];
    }

    public function getStrikes(): ?array
    {
        return $this->strikes;
    }

    public function getHasMiniOptions(): ?bool
    {
        return $this->hasMiniOptions;
    }

    public function getOptions(): array
    {
        return $this->options ?? [];
    }
}
