<?php

namespace Scheb\YahooFinanceApi\Results;

class KeyStatisticsFinancialData implements \JsonSerializable
{
    private $ebitdaMargins;
    private $profitMargins;
    private $grossMargins;
    private $operatingCashflow;
    private $revenueGrowth;
    private $operatingMargins;
    private $ebitda;
    private $targetLowPrice;
    private $recommendationKey;
    private $grossProfits;
    private $freeCashflow;
    private $targetMedianPrice;
    private $currentPrice;
    private $earningsGrowth;
    private $currentRatio;
    private $returnOnAssets;
    private $numberOfAnalystOpinions;
    private $targetMeanPrice;
    private $debtToEquity;
    private $returnOnEquity;
    private $targetHighPrice;
    private $totalCash;
    private $totalDebt;
    private $totalRevenue;
    private $totalCashPerShare;
    private $financialCurrency;
    private $maxAge;
    private $revenuePerShare;
    private $quickRatio;
    private $recommendationMean;

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
    public function getEbitdaMargins()
    {
        return $this->ebitdaMargins;
    }

    /**
     * @return mixed
     */
    public function getProfitMargins()
    {
        return $this->profitMargins;
    }

    /**
     * @return mixed
     */
    public function getGrossMargins()
    {
        return $this->grossMargins;
    }

    /**
     * @return mixed
     */
    public function getOperatingCashflow()
    {
        return $this->operatingCashflow;
    }

    /**
     * @return mixed
     */
    public function getRevenueGrowth()
    {
        return $this->revenueGrowth;
    }

    /**
     * @return mixed
     */
    public function getOperatingMargins()
    {
        return $this->operatingMargins;
    }

    /**
     * @return mixed
     */
    public function getEbitda()
    {
        return $this->ebitda;
    }

    /**
     * @return mixed
     */
    public function getTargetLowPrice()
    {
        return $this->targetLowPrice;
    }

    /**
     * @return mixed
     */
    public function getRecommendationKey()
    {
        return $this->recommendationKey;
    }

    /**
     * @return mixed
     */
    public function getGrossProfits()
    {
        return $this->grossProfits;
    }

    /**
     * @return mixed
     */
    public function getFreeCashflow()
    {
        return $this->freeCashflow;
    }

    /**
     * @return mixed
     */
    public function getTargetMedianPrice()
    {
        return $this->targetMedianPrice;
    }

    /**
     * @return mixed
     */
    public function getCurrentPrice()
    {
        return $this->currentPrice;
    }

    /**
     * @return mixed
     */
    public function getEarningsGrowth()
    {
        return $this->earningsGrowth;
    }

    /**
     * @return mixed
     */
    public function getCurrentRatio()
    {
        return $this->currentRatio;
    }

    /**
     * @return mixed
     */
    public function getReturnOnAssets()
    {
        return $this->returnOnAssets;
    }

    /**
     * @return mixed
     */
    public function getNumberOfAnalystOpinions()
    {
        return $this->numberOfAnalystOpinions;
    }

    /**
     * @return mixed
     */
    public function getTargetMeanPrice()
    {
        return $this->targetMeanPrice;
    }

    /**
     * @return mixed
     */
    public function getDebtToEquity()
    {
        return $this->debtToEquity;
    }

    /**
     * @return mixed
     */
    public function getReturnOnEquity()
    {
        return $this->returnOnEquity;
    }

    /**
     * @return mixed
     */
    public function getTargetHighPrice()
    {
        return $this->targetHighPrice;
    }

    /**
     * @return mixed
     */
    public function getTotalCash()
    {
        return $this->totalCash;
    }

    /**
     * @return mixed
     */
    public function getTotalDebt()
    {
        return $this->totalDebt;
    }

    /**
     * @return mixed
     */
    public function getTotalRevenue()
    {
        return $this->totalRevenue;
    }

    /**
     * @return mixed
     */
    public function getTotalCashPerShare()
    {
        return $this->totalCashPerShare;
    }

    /**
     * @return mixed
     */
    public function getFinancialCurrency()
    {
        return $this->financialCurrency;
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
    public function getRevenuePerShare()
    {
        return $this->revenuePerShare;
    }

    /**
     * @return mixed
     */
    public function getQuickRatio()
    {
        return $this->quickRatio;
    }

    /**
     * @return mixed
     */
    public function getRecommendationMean()
    {
        return $this->recommendationMean;
    }
}
