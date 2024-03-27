<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

class Option implements \JsonSerializable
{
    private $expirationDate;
    private $hasMiniOptions;
    private $calls;
    private $puts;

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
            'calls' => array_map(function (OptionContract $optionContract): array {
                return $optionContract->jsonSerialize();
            }, $this->calls),
            'puts' => array_map(function (OptionContract $optionContract): array {
                return $optionContract->jsonSerialize();
            }, $this->puts),
        ];
    }

    public function getExpirationDate(): int
    {
        return $this->expirationDate;
    }

    public function getHasMiniOptions(): bool
    {
        return $this->hasMiniOptions;
    }

    public function getCalls(): array
    {
        return $this->calls;
    }

    public function getPuts(): array
    {
        return $this->puts;
    }
}
