<?php

namespace Scheb\YahooFinanceApi\Results;

class KeyStatisticsDefault implements \JsonSerializable
{
    private $annualHoldingsTurnover;
    private $enterpriseToRevenue;
    private $beta3Year;
    private $profitMargins;
    private $enterpriseToEbitda;
    private $morningStarRiskRating;
    private $forwardEps;
    private $revenueQuarterlyGrowth;
    private $sharesOutstanding;
    private $fundInceptionDate;
    private $annualReportExpenseRatio;
    private $totalAssets;
    private $bookValue;
    private $sharesShort;
    private $sharesPercentSharesOut;
    private $fundFamily;
    private $lastFiscalYearEnd;
    private $heldPercentInstitutions;
    private $netIncomeToCommon;
    private $trailingEps;
    private $lastDividendValue;
    private $SandP52WeekChange;
    private $priceToBook;
    private $heldPercentInsiders;
    private $nextFiscalYearEnd;
    private $yield;
    private $mostRecentQuarter;
    private $shortRatio;
    private $sharesShortPreviousMonthDate;
    private $floatShares;
    private $beta;
    private $enterpriseValue;
    private $priceHint;
    private $threeYearAverageReturn;
    private $lastSplitDate;
    private $lastSplitFactor;
    private $legalType;
    private $morningStarOverallRating;
    private $earningsQuarterlyGrowth;
    private $priceToSalesTrailing12Months;
    private $dateShortInterest;
    private $pegRatio;
    private $ytdReturn;
    private $forwardPE;
    private $maxAge;
    private $lastCapGain;
    private $shortPercentOfFloat;
    private $sharesShortPriorMonth;
    private $category;
    private $fiveYearAverageReturn;

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
    public function getAnnualHoldingsTurnover()
    {
        return $this->annualHoldingsTurnover;
    }

    /**
     * @return mixed
     */
    public function getEnterpriseToRevenue()
    {
        return $this->enterpriseToRevenue;
    }

    /**
     * @return mixed
     */
    public function getBeta3Year()
    {
        return $this->beta3Year;
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
    public function getEnterpriseToEbitda()
    {
        return $this->enterpriseToEbitda;
    }

    /**
     * @return mixed
     */
    public function getMorningStarRiskRating()
    {
        return $this->morningStarRiskRating;
    }

    /**
     * @return mixed
     */
    public function getForwardEps()
    {
        return $this->forwardEps;
    }

    /**
     * @return mixed
     */
    public function getRevenueQuarterlyGrowth()
    {
        return $this->revenueQuarterlyGrowth;
    }

    /**
     * @return mixed
     */
    public function getSharesOutstanding()
    {
        return $this->sharesOutstanding;
    }

    /**
     * @return mixed
     */
    public function getFundInceptionDate()
    {
        return $this->fundInceptionDate;
    }

    /**
     * @return mixed
     */
    public function getAnnualReportExpenseRatio()
    {
        return $this->annualReportExpenseRatio;
    }

    /**
     * @return mixed
     */
    public function getTotalAssets()
    {
        return $this->totalAssets;
    }

    /**
     * @return mixed
     */
    public function getBookValue()
    {
        return $this->bookValue;
    }

    /**
     * @return mixed
     */
    public function getSharesShort()
    {
        return $this->sharesShort;
    }

    /**
     * @return mixed
     */
    public function getSharesPercentSharesOut()
    {
        return $this->sharesPercentSharesOut;
    }

    /**
     * @return mixed
     */
    public function getFundFamily()
    {
        return $this->fundFamily;
    }

    /**
     * @return mixed
     */
    public function getLastFiscalYearEnd()
    {
        return $this->lastFiscalYearEnd;
    }

    /**
     * @return mixed
     */
    public function getHeldPercentInstitutions()
    {
        return $this->heldPercentInstitutions;
    }

    /**
     * @return mixed
     */
    public function getNetIncomeToCommon()
    {
        return $this->netIncomeToCommon;
    }

    /**
     * @return mixed
     */
    public function getTrailingEps()
    {
        return $this->trailingEps;
    }

    /**
     * @return mixed
     */
    public function getLastDividendValue()
    {
        return $this->lastDividendValue;
    }

    /**
     * @return mixed
     */
    public function getSandP52WeekChange()
    {
        return $this->SandP52WeekChange;
    }

    /**
     * @return mixed
     */
    public function getPriceToBook()
    {
        return $this->priceToBook;
    }

    /**
     * @return mixed
     */
    public function getHeldPercentInsiders()
    {
        return $this->heldPercentInsiders;
    }

    /**
     * @return mixed
     */
    public function getNextFiscalYearEnd()
    {
        return $this->nextFiscalYearEnd;
    }

    /**
     * @return mixed
     */
    public function getYield()
    {
        return $this->yield;
    }

    /**
     * @return mixed
     */
    public function getMostRecentQuarter()
    {
        return $this->mostRecentQuarter;
    }

    /**
     * @return mixed
     */
    public function getShortRatio()
    {
        return $this->shortRatio;
    }

    /**
     * @return mixed
     */
    public function getSharesShortPreviousMonthDate()
    {
        return $this->sharesShortPreviousMonthDate;
    }

    /**
     * @return mixed
     */
    public function getFloatShares()
    {
        return $this->floatShares;
    }

    /**
     * @return mixed
     */
    public function getBeta()
    {
        return $this->beta;
    }

    /**
     * @return mixed
     */
    public function getEnterpriseValue()
    {
        return $this->enterpriseValue;
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
    public function getThreeYearAverageReturn()
    {
        return $this->threeYearAverageReturn;
    }

    /**
     * @return mixed
     */
    public function getLastSplitDate()
    {
        return $this->lastSplitDate;
    }

    /**
     * @return mixed
     */
    public function getLastSplitFactor()
    {
        return $this->lastSplitFactor;
    }

    /**
     * @return mixed
     */
    public function getLegalType()
    {
        return $this->legalType;
    }

    /**
     * @return mixed
     */
    public function getMorningStarOverallRating()
    {
        return $this->morningStarOverallRating;
    }

    /**
     * @return mixed
     */
    public function getEarningsQuarterlyGrowth()
    {
        return $this->earningsQuarterlyGrowth;
    }

    /**
     * @return mixed
     */
    public function getPriceToSalesTrailing12Months()
    {
        return $this->priceToSalesTrailing12Months;
    }

    /**
     * @return mixed
     */
    public function getDateShortInterest()
    {
        return $this->dateShortInterest;
    }

    /**
     * @return mixed
     */
    public function getPegRatio()
    {
        return $this->pegRatio;
    }

    /**
     * @return mixed
     */
    public function getYtdReturn()
    {
        return $this->ytdReturn;
    }

    /**
     * @return mixed
     */
    public function getForwardPE()
    {
        return $this->forwardPE;
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
    public function getLastCapGain()
    {
        return $this->lastCapGain;
    }

    /**
     * @return mixed
     */
    public function getShortPercentOfFloat()
    {
        return $this->shortPercentOfFloat;
    }

    /**
     * @return mixed
     */
    public function getSharesShortPriorMonth()
    {
        return $this->sharesShortPriorMonth;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getFiveYearAverageReturn()
    {
        return $this->fiveYearAverageReturn;
    }
}
