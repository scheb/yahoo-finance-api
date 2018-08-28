<?php

namespace Scheb\YahooFinanceApi\Results;

class Quote implements \JsonSerializable
{
    private $ask;
    private $askSize;
    private $averageDailyVolume10Day;
    private $averageDailyVolume3Month;
    private $bid;
    private $bidSize;
    private $bookValue;
    private $currency;
    private $dividendDate;
    private $earningsTimestamp;
    private $earningsTimestampStart;
    private $earningsTimestampEnd;
    private $epsForward;
    private $epsTrailingTwelveMonths;
    private $exchange;
    private $exchangeDataDelayedBy;
    private $exchangeTimezoneName;
    private $exchangeTimezoneShortName;
    private $fiftyDayAverage;
    private $fiftyDayAverageChange;
    private $fiftyDayAverageChangePercent;
    private $fiftyTwoWeekHigh;
    private $fiftyTwoWeekHighChange;
    private $fiftyTwoWeekHighChangePercent;
    private $fiftyTwoWeekLow;
    private $fiftyTwoWeekLowChange;
    private $fiftyTwoWeekLowChangePercent;
    private $financialCurrency;
    private $forwardPE;
    private $fullExchangeName;
    private $gmtOffSetMilliseconds;
    private $language;
    private $longName;
    private $market;
    private $marketCap;
    private $marketState;
    private $messageBoardId;
    private $postMarketChange;
    private $postMarketChangePercent;
    private $postMarketPrice;
    private $postMarketTime;
    private $priceHint;
    private $priceToBook;
    private $quoteSourceName;
    private $quoteType;
    private $regularMarketChange;
    private $regularMarketChangePercent;
    private $regularMarketDayHigh;
    private $regularMarketDayLow;
    private $regularMarketOpen;
    private $regularMarketPreviousClose;
    private $regularMarketPrice;
    private $regularMarketTime;
    private $regularMarketVolume;
    private $sharesOutstanding;
    private $shortName;
    private $sourceInterval;
    private $symbol;
    private $tradeable;
    private $trailingAnnualDividendRate;
    private $trailingAnnualDividendYield;
    private $trailingPE;
    private $twoHundredDayAverage;
    private $twoHundredDayAverageChange;
    private $twoHundredDayAverageChangePercent;

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
     * @return float
     */
    public function getAsk()
    {
        return $this->ask;
    }

    /**
     * @return int
     */
    public function getAskSize()
    {
        return $this->askSize;
    }

    /**
     * @return int
     */
    public function getAverageDailyVolume10Day()
    {
        return $this->averageDailyVolume10Day;
    }

    /**
     * @return int
     */
    public function getAverageDailyVolume3Month()
    {
        return $this->averageDailyVolume3Month;
    }

    /**
     * @return float
     */
    public function getBid()
    {
        return $this->bid;
    }

    /**
     * @return int
     */
    public function getBidSize()
    {
        return $this->bidSize;
    }

    /**
     * @return float
     */
    public function getBookValue()
    {
        return $this->bookValue;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return \DateTime
     */
    public function getDividendDate()
    {
        return $this->dividendDate;
    }

    /**
     * @return \DateTime
     */
    public function getEarningsTimestamp()
    {
        return $this->earningsTimestamp;
    }

    /**
     * @return \DateTime
     */
    public function getEarningsTimestampStart()
    {
        return $this->earningsTimestampStart;
    }

    /**
     * @return \DateTime
     */
    public function getEarningsTimestampEnd()
    {
        return $this->earningsTimestampEnd;
    }

    /**
     * @return float
     */
    public function getEpsForward()
    {
        return $this->epsForward;
    }

    /**
     * @return float
     */
    public function getEpsTrailingTwelveMonths()
    {
        return $this->epsTrailingTwelveMonths;
    }

    /**
     * @return string
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @return int
     */
    public function getExchangeDataDelayedBy()
    {
        return $this->exchangeDataDelayedBy;
    }

    /**
     * @return string
     */
    public function getExchangeTimezoneName()
    {
        return $this->exchangeTimezoneName;
    }

    /**
     * @return string
     */
    public function getExchangeTimezoneShortName()
    {
        return $this->exchangeTimezoneShortName;
    }

    /**
     * @return float
     */
    public function getFiftyDayAverage()
    {
        return $this->fiftyDayAverage;
    }

    /**
     * @return float
     */
    public function getFiftyDayAverageChange()
    {
        return $this->fiftyDayAverageChange;
    }

    /**
     * @return float
     */
    public function getFiftyDayAverageChangePercent()
    {
        return $this->fiftyDayAverageChangePercent;
    }

    /**
     * @return float
     */
    public function getFiftyTwoWeekHigh()
    {
        return $this->fiftyTwoWeekHigh;
    }

    /**
     * @return float
     */
    public function getFiftyTwoWeekHighChange()
    {
        return $this->fiftyTwoWeekHighChange;
    }

    /**
     * @return float
     */
    public function getFiftyTwoWeekHighChangePercent()
    {
        return $this->fiftyTwoWeekHighChangePercent;
    }

    /**
     * @return float
     */
    public function getFiftyTwoWeekLow()
    {
        return $this->fiftyTwoWeekLow;
    }

    /**
     * @return float
     */
    public function getFiftyTwoWeekLowChange()
    {
        return $this->fiftyTwoWeekLowChange;
    }

    /**
     * @return float
     */
    public function getFiftyTwoWeekLowChangePercent()
    {
        return $this->fiftyTwoWeekLowChangePercent;
    }

    /**
     * @return string
     */
    public function getFinancialCurrency()
    {
        return $this->financialCurrency;
    }

    /**
     * @return float
     */
    public function getForwardPE()
    {
        return $this->forwardPE;
    }

    /**
     * @return string
     */
    public function getFullExchangeName()
    {
        return $this->fullExchangeName;
    }

    /**
     * @return int
     */
    public function getGmtOffSetMilliseconds()
    {
        return $this->gmtOffSetMilliseconds;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getLongName()
    {
        return $this->longName;
    }

    /**
     * @return string
     */
    public function getMarket()
    {
        return $this->market;
    }

    /**
     * @return int
     */
    public function getMarketCap()
    {
        return $this->marketCap;
    }

    /**
     * @return string
     */
    public function getMarketState()
    {
        return $this->marketState;
    }

    /**
     * @return string
     */
    public function getMessageBoardId()
    {
        return $this->messageBoardId;
    }

    /**
     * @return float
     */
    public function getPostMarketChange()
    {
        return $this->postMarketChange;
    }

    /**
     * @return float
     */
    public function getPostMarketChangePercent()
    {
        return $this->postMarketChangePercent;
    }

    /**
     * @return float
     */
    public function getPostMarketPrice()
    {
        return $this->postMarketPrice;
    }

    /**
     * @return \DateTime
     */
    public function getPostMarketTime()
    {
        return $this->postMarketTime;
    }

    /**
     * @return int
     */
    public function getPriceHint()
    {
        return $this->priceHint;
    }

    /**
     * @return float
     */
    public function getPriceToBook()
    {
        return $this->priceToBook;
    }

    /**
     * @return string
     */
    public function getQuoteSourceName()
    {
        return $this->quoteSourceName;
    }

    /**
     * @return string
     */
    public function getQuoteType()
    {
        return $this->quoteType;
    }

    /**
     * @return float
     */
    public function getRegularMarketChange()
    {
        return $this->regularMarketChange;
    }

    /**
     * @return float
     */
    public function getRegularMarketChangePercent()
    {
        return $this->regularMarketChangePercent;
    }

    /**
     * @return float
     */
    public function getRegularMarketDayHigh()
    {
        return $this->regularMarketDayHigh;
    }

    /**
     * @return float
     */
    public function getRegularMarketDayLow()
    {
        return $this->regularMarketDayLow;
    }

    /**
     * @return float
     */
    public function getRegularMarketOpen()
    {
        return $this->regularMarketOpen;
    }

    /**
     * @return float
     */
    public function getRegularMarketPreviousClose()
    {
        return $this->regularMarketPreviousClose;
    }

    /**
     * @return float
     */
    public function getRegularMarketPrice()
    {
        return $this->regularMarketPrice;
    }

    /**
     * @return \DateTime
     */
    public function getRegularMarketTime()
    {
        return $this->regularMarketTime;
    }

    /**
     * @return int
     */
    public function getRegularMarketVolume()
    {
        return $this->regularMarketVolume;
    }

    /**
     * @return int
     */
    public function getSharesOutstanding()
    {
        return $this->sharesOutstanding;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @return int
     */
    public function getSourceInterval()
    {
        return $this->sourceInterval;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @return bool
     */
    public function getTradeable()
    {
        return $this->tradeable;
    }

    /**
     * @return float
     */
    public function getTrailingAnnualDividendRate()
    {
        return $this->trailingAnnualDividendRate;
    }

    /**
     * @return float
     */
    public function getTrailingAnnualDividendYield()
    {
        return $this->trailingAnnualDividendYield;
    }

    /**
     * @return float
     */
    public function getTrailingPE()
    {
        return $this->trailingPE;
    }

    /**
     * @return float
     */
    public function getTwoHundredDayAverage()
    {
        return $this->twoHundredDayAverage;
    }

    /**
     * @return float
     */
    public function getTwoHundredDayAverageChange()
    {
        return $this->twoHundredDayAverageChange;
    }

    /**
     * @return float
     */
    public function getTwoHundredDayAverageChangePercent()
    {
        return $this->twoHundredDayAverageChangePercent;
    }
}
