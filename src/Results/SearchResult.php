<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

class SearchResult implements \JsonSerializable
{
    private $symbol;
    private $name;
    private $exch;
    private $type;
    private $exchDisp;
    private $typeDisp;

    public function __construct(?string $symbol, ?string $name, ?string $exch, ?string $type, ?string $exchDisp, ?string $typeDisp)
    {
        $this->symbol = $symbol;
        $this->name = $name;
        $this->exch = $exch;
        $this->type = $type;
        $this->exchDisp = $exchDisp;
        $this->typeDisp = $typeDisp;
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
