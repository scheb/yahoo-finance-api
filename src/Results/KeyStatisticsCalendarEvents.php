<?php

namespace Scheb\YahooFinanceApi\Results;

class KeyStatisticsCalendarEvents implements \JsonSerializable
{
    private $earningsDates = [];
    private $exDividendDate;
    private $dividendDate;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['earnings']) && isset($values['earnings']['earningsDate'])) {
            foreach ($values['earnings']['earningsDate'] as $date) {
                if (isset($date['raw'])) {
                    $this->earningsDates[] = $date['raw'];
                }
            }
        }

        if (isset($values['exDividendDate']) && isset($values['exDividendDate']['raw'])) {
            $this->exDividendDate = $values['exDividendDate']['raw'];
        }
        if (isset($values['dividendDate']) && isset($values['dividendDate']['raw'])) {
            $this->exDividendDate = $values['dividendDate']['raw'];
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
     * @return array
     */
    public function getEarningsDates(): array
    {
        return $this->earningsDates;
    }

    /**
     * @return mixed
     */
    public function getExDividendDate()
    {
        return $this->exDividendDate;
    }

    /**
     * @return mixed
     */
    public function getDividendDate()
    {
        return $this->dividendDate;
    }
}
