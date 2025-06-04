<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 */
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
        return array_merge(
            get_class_vars(self::class),
            get_object_vars($this)
        );
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
