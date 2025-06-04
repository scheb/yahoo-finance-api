<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

class SearchResult implements \JsonSerializable
{
    public function __construct(
        private readonly ?string $symbol,
        private readonly ?string $name,
        private readonly ?string $exch,
        private readonly ?string $type,
        private readonly ?string $exchDisp,
        private readonly ?string $typeDisp,
    ) {
    }

    public function jsonSerialize(): ?array
    {
        return get_object_vars($this);
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getExch(): ?string
    {
        return $this->exch;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getExchDisp(): ?string
    {
        return $this->exchDisp;
    }

    public function getTypeDisp(): ?string
    {
        return $this->typeDisp;
    }
}
