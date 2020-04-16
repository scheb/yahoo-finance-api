<?php

namespace Scheb\YahooFinanceApi\Results;

class DividendData implements \JsonSerializable
{
    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var float
     */
    private $dividend;

    /**
     * @param \DateTime $date
     * @param float     $dividend
     */
    public function __construct(\DateTime $date, $dividend)
    {
        $this->date = $date;
        $this->dividend = $dividend;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return float
     */
    public function getDividend(): float
    {
        return $this->dividend;
    }
}
