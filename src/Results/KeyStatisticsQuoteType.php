<?php

namespace Scheb\YahooFinanceApi\Results;

class KeyStatisticsQuoteType implements \JsonSerializable
{
    private $exchange;
    private $shortName;
    private $longName;
    private $exchangeTimezoneName;
    private $exchangeTimezoneShortName;
    private $isEsgPopulated;
    private $gmtOffSetMilliseconds;
    private $quoteType;
    private $symbol;
    private $messageBoardId;
    private $market;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $property => $value) {
            $this->{$property} = $value;
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @return mixed
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @return mixed
     */
    public function getLongName()
    {
        return $this->longName;
    }

    /**
     * @return mixed
     */
    public function getExchangeTimezoneName()
    {
        return $this->exchangeTimezoneName;
    }

    /**
     * @return mixed
     */
    public function getExchangeTimezoneShortName()
    {
        return $this->exchangeTimezoneShortName;
    }

    /**
     * @return mixed
     */
    public function getIsEsgPopulated()
    {
        return $this->isEsgPopulated;
    }

    /**
     * @return mixed
     */
    public function getGmtOffSetMilliseconds()
    {
        return $this->gmtOffSetMilliseconds;
    }

    /**
     * @return mixed
     */
    public function getQuoteType()
    {
        return $this->quoteType;
    }

    /**
     * @return mixed
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @return mixed
     */
    public function getMessageBoardId()
    {
        return $this->messageBoardId;
    }

    /**
     * @return mixed
     */
    public function getMarket()
    {
        return $this->market;
    }
}
