<?php
namespace Scheb\YahooFinanceApi\Results;

class Quote implements \JsonSerializable
{
    private $averageDailyVolume;
    private $bookValue;
    private $change;
    private $currency;
    private $dividendShare;
    private $earningsShare;
    private $epsEstimateCurrentYear;
    private $epsEstimateNextYear;
    private $epsEstimateNextQuarter;
    private $dayLow;
    private $dayHigh;
    private $yearLow;
    private $yearHigh;
    private $marketCapitalization;
    private $ebitda;
    private $changeFromYearLow;
    private $percentChangeFromYearLow;
    private $changeFromYearHigh;
    private $percentChangeFromYearHigh;
    private $lastTradePrice;
    private $fiftyDayMovingAverage;
    private $twoHundredDayMovingAverage;
    private $changeFromTwoHundredDayMovingAverage;
    private $percentChangeFromTwoHundredDayMovingAverage;
    private $changeFromFiftyDayMovingAverage;
    private $percentChangeFromFiftyDayMovingAverage;
    private $name;
    private $open;
    private $previousClose;
    private $changeInPercent;
    private $priceSales;
    private $priceBook;
    private $exDividendDate;
    private $peRatio;
    private $dividendPayDate;
    private $pegRatio;
    private $priceEpsEstimateCurrentYear;
    private $priceEpsEstimateNextYear;
    private $symbol;
    private $shortRatio;
    private $oneYearTargetPrice;
    private $volume;
    private $stockExchange;
    private $dividendYield;
    private $percentChange;
    private $lastTradeDateTime;

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
     * @return int
     */
    public function getAverageDailyVolume()
    {
        return $this->averageDailyVolume;
    }

    /**
     * @return float
     */
    public function getBookValue()
    {
        return $this->bookValue;
    }

    /**
     * @return float
     */
    public function getChange()
    {
        return $this->change;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getDividendShare()
    {
        return $this->dividendShare;
    }

    /**
     * @return float
     */
    public function getEarningsShare()
    {
        return $this->earningsShare;
    }

    /**
     * @return float
     */
    public function getEpsEstimateCurrentYear()
    {
        return $this->epsEstimateCurrentYear;
    }

    /**
     * @return float
     */
    public function getEpsEstimateNextYear()
    {
        return $this->epsEstimateNextYear;
    }

    /**
     * @return float
     */
    public function getEpsEstimateNextQuarter()
    {
        return $this->epsEstimateNextQuarter;
    }

    /**
     * @return float
     */
    public function getDayLow()
    {
        return $this->dayLow;
    }

    /**
     * @return float
     */
    public function getDayHigh()
    {
        return $this->dayHigh;
    }

    /**
     * @return float
     */
    public function getYearLow()
    {
        return $this->yearLow;
    }

    /**
     * @return mixed
     */
    public function getYearHigh()
    {
        return $this->yearHigh;
    }

    /**
     * @return string
     */
    public function getMarketCapitalization()
    {
        return $this->marketCapitalization;
    }

    /**
     * @return string
     */
    public function getEbitda()
    {
        return $this->ebitda;
    }

    /**
     * @return float
     */
    public function getChangeFromYearLow()
    {
        return $this->changeFromYearLow;
    }

    /**
     * @return float
     */
    public function getPercentChangeFromYearLow()
    {
        return $this->percentChangeFromYearLow;
    }

    /**
     * @return float
     */
    public function getChangeFromYearHigh()
    {
        return $this->changeFromYearHigh;
    }

    /**
     * @return float
     */
    public function getPercentChangeFromYearHigh()
    {
        return $this->percentChangeFromYearHigh;
    }

    /**
     * @return float
     */
    public function getLastTradePrice()
    {
        return $this->lastTradePrice;
    }

    /**
     * @return float
     */
    public function getFiftyDayMovingAverage()
    {
        return $this->fiftyDayMovingAverage;
    }

    /**
     * @return float
     */
    public function getTwoHundredDayMovingAverage()
    {
        return $this->twoHundredDayMovingAverage;
    }

    /**
     * @return float
     */
    public function getChangeFromTwoHundredDayMovingAverage()
    {
        return $this->changeFromTwoHundredDayMovingAverage;
    }

    /**
     * @return float
     */
    public function getPercentChangeFromTwoHundredDayMovingAverage()
    {
        return $this->percentChangeFromTwoHundredDayMovingAverage;
    }

    /**
     * @return float
     */
    public function getChangeFromFiftyDayMovingAverage()
    {
        return $this->changeFromFiftyDayMovingAverage;
    }

    /**
     * @return float
     */
    public function getPercentChangeFromFiftyDayMovingAverage()
    {
        return $this->percentChangeFromFiftyDayMovingAverage;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getOpen()
    {
        return $this->open;
    }

    /**
     * @return float
     */
    public function getPreviousClose()
    {
        return $this->previousClose;
    }

    /**
     * @return float
     */
    public function getChangeInPercent()
    {
        return $this->changeInPercent;
    }

    /**
     * @return float
     */
    public function getPriceSales()
    {
        return $this->priceSales;
    }

    /**
     * @return float
     */
    public function getPriceBook()
    {
        return $this->priceBook;
    }

    /**
     * @return \DateTime
     */
    public function getExDividendDate()
    {
        return $this->exDividendDate;
    }

    /**
     * @return float
     */
    public function getPeRatio()
    {
        return $this->peRatio;
    }

    /**
     * @return \DateTime
     */
    public function getDividendPayDate()
    {
        return $this->dividendPayDate;
    }

    /**
     * @return float
     */
    public function getPegRatio()
    {
        return $this->pegRatio;
    }

    /**
     * @return float
     */
    public function getPriceEpsEstimateCurrentYear()
    {
        return $this->priceEpsEstimateCurrentYear;
    }

    /**
     * @return float
     */
    public function getPriceEpsEstimateNextYear()
    {
        return $this->priceEpsEstimateNextYear;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @return float
     */
    public function getShortRatio()
    {
        return $this->shortRatio;
    }

    /**
     * @return float
     */
    public function getOneYearTargetPrice()
    {
        return $this->oneYearTargetPrice;
    }

    /**
     * @return int
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * @return string
     */
    public function getStockExchange()
    {
        return $this->stockExchange;
    }

    /**
     * @return float
     */
    public function getDividendYield()
    {
        return $this->dividendYield;
    }

    /**
     * @return float
     */
    public function getPercentChange()
    {
        return $this->percentChange;
    }

    /**
     * @return \DateTime
     */
    public function getLastTradeDateTime()
    {
        return $this->lastTradeDateTime;
    }
}
