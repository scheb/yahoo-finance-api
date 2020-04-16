<?php

namespace Scheb\YahooFinanceApi\Results;

class KeyStatistics implements \JsonSerializable
{
    private $defaultKeyStatistics;
    private $priceData;
    private $financialData;
    private $quoteType;
    private $calendarEvents;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values["defaultKeyStatistics"])) {
            $this->defaultKeyStatistics = new KeyStatisticsDefault($values["defaultKeyStatistics"]);
        }
        if (isset($values["price"])) {
            $this->priceData = new KeyStatisticsPrice($values["price"]);
        }
        if (isset($values["financialData"])) {
            $this->financialData = new KeyStatisticsFinancialData($values["financialData"]);
        }
        if (isset($values["quoteType"])) {
            $this->quoteType = new KeyStatisticsQuoteType($values["quoteType"]);
        }
        if (isset($values["calendarEvents"])) {
            $this->calendarEvents = new KeyStatisticsCalendarEvents($values["calendarEvents"]);
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
     * @return KeyStatisticsDefault
     */
    public function getDefaultKeyStatistics(): KeyStatisticsDefault
    {
        return $this->defaultKeyStatistics;
    }

    /**
     * @return KeyStatisticsPrice
     */
    public function getPriceData(): KeyStatisticsPrice
    {
        return $this->priceData;
    }

    /**
     * @return KeyStatisticsFinancialData
     */
    public function getFinancialData(): KeyStatisticsFinancialData
    {
        return $this->financialData;
    }

    /**
     * @return KeyStatisticsQuoteType
     */
    public function getQuoteType(): KeyStatisticsQuoteType
    {
        return $this->quoteType;
    }

    /**
     * @return KeyStatisticsCalendarEvents
     */
    public function getCalendarEvents(): KeyStatisticsCalendarEvents
    {
        return $this->calendarEvents;
    }
}
