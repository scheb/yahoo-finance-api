<?php
namespace Scheb\YahooFinanceApi\Results;

class Quote
{
    /**
     * @var string
     */
    private $symbol;

    /**
     * @param string $symbol
     */
    public function __construct($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }
}
