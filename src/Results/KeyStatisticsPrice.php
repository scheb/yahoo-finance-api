<?php

namespace Scheb\YahooFinanceApi\Results;

class KeyStatisticsPrice implements \JsonSerializable
{
    private $quoteSourceName;
    private $regularMarketOpen;
    private $averageDailyVolume3Month;
    private $exchange;
    private $regularMarketTime;
    private $volume24Hr;
    private $regularMarketDayHigh;
    private $shortName;
    private $averageDailyVolume10Day;
    private $longName;
    private $regularMarketChange;
    private $currencySymbol;
    private $regularMarketPreviousClose;
    private $preMarketPrice;
    private $preMarketTime;
    private $exchangeDataDelayedBy;
    private $toCurrency;
    private $postMarketChange;
    private $postMarketPrice;
    private $exchangeName;
    private $preMarketChange;
    private $circulatingSupply;
    private $regularMarketDayLow;
    private $priceHint;
    private $currency;
    private $regularMarketPrice;
    private $regularMarketVolume;
    private $lastMarket;
    private $regularMarketSource;
    private $openInterest;
    private $marketState;
    private $underlyingSymbol;
    private $marketCap;
    private $quoteType;
    private $preMarketChangePercent;
    private $volumeAllCurrencies;
    private $strikePrice;
    private $symbol;
    private $preMarketSource;
    private $maxAge;
    private $fromCurrency;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $property => $value) {
            if (isset($value['raw'])) {
                $this->{$property} = $value['raw'];
            } else {
                $this->{$property} = $value;
            }
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
    public function getQuoteSourceName()
    {
        return $this->quoteSourceName;
    }

    /**
     * @return mixed
     */
    public function getRegularMarketOpen()
    {
        return $this->regularMarketOpen;
    }

    /**
     * @return mixed
     */
    public function getAverageDailyVolume3Month()
    {
        return $this->averageDailyVolume3Month;
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
    public function getRegularMarketTime()
    {
        return $this->regularMarketTime;
    }

    /**
     * @return mixed
     */
    public function getVolume24Hr()
    {
        return $this->volume24Hr;
    }

    /**
     * @return mixed
     */
    public function getRegularMarketDayHigh()
    {
        return $this->regularMarketDayHigh;
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
    public function getAverageDailyVolume10Day()
    {
        return $this->averageDailyVolume10Day;
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
    public function getRegularMarketChange()
    {
        return $this->regularMarketChange;
    }

    /**
     * @return mixed
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbol;
    }

    /**
     * @return mixed
     */
    public function getRegularMarketPreviousClose()
    {
        return $this->regularMarketPreviousClose;
    }

    /**
     * @return mixed
     */
    public function getPreMarketPrice()
    {
        return $this->preMarketPrice;
    }

    /**
     * @return mixed
     */
    public function getPreMarketTime()
    {
        return $this->preMarketTime;
    }

    /**
     * @return mixed
     */
    public function getExchangeDataDelayedBy()
    {
        return $this->exchangeDataDelayedBy;
    }

    /**
     * @return mixed
     */
    public function getToCurrency()
    {
        return $this->toCurrency;
    }

    /**
     * @return mixed
     */
    public function getPostMarketChange()
    {
        return $this->postMarketChange;
    }

    /**
     * @return mixed
     */
    public function getPostMarketPrice()
    {
        return $this->postMarketPrice;
    }

    /**
     * @return mixed
     */
    public function getExchangeName()
    {
        return $this->exchangeName;
    }

    /**
     * @return mixed
     */
    public function getPreMarketChange()
    {
        return $this->preMarketChange;
    }

    /**
     * @return mixed
     */
    public function getCirculatingSupply()
    {
        return $this->circulatingSupply;
    }

    /**
     * @return mixed
     */
    public function getRegularMarketDayLow()
    {
        return $this->regularMarketDayLow;
    }

    /**
     * @return mixed
     */
    public function getPriceHint()
    {
        return $this->priceHint;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getRegularMarketPrice()
    {
        return $this->regularMarketPrice;
    }

    /**
     * @return mixed
     */
    public function getRegularMarketVolume()
    {
        return $this->regularMarketVolume;
    }

    /**
     * @return mixed
     */
    public function getLastMarket()
    {
        return $this->lastMarket;
    }

    /**
     * @return mixed
     */
    public function getRegularMarketSource()
    {
        return $this->regularMarketSource;
    }

    /**
     * @return mixed
     */
    public function getOpenInterest()
    {
        return $this->openInterest;
    }

    /**
     * @return mixed
     */
    public function getMarketState()
    {
        return $this->marketState;
    }

    /**
     * @return mixed
     */
    public function getUnderlyingSymbol()
    {
        return $this->underlyingSymbol;
    }

    /**
     * @return mixed
     */
    public function getMarketCap()
    {
        return $this->marketCap;
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
    public function getPreMarketChangePercent()
    {
        return $this->preMarketChangePercent;
    }

    /**
     * @return mixed
     */
    public function getVolumeAllCurrencies()
    {
        return $this->volumeAllCurrencies;
    }

    /**
     * @return mixed
     */
    public function getStrikePrice()
    {
        return $this->strikePrice;
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
    public function getPreMarketSource()
    {
        return $this->preMarketSource;
    }

    /**
     * @return mixed
     */
    public function getMaxAge()
    {
        return $this->maxAge;
    }

    /**
     * @return mixed
     */
    public function getFromCurrency()
    {
        return $this->fromCurrency;
    }
}
