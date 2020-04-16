<?php

namespace Scheb\YahooFinanceApi\Results;

class SplitData implements \JsonSerializable
{
    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var float
     */
    private $split;

    /**
     * @param \DateTime $date
     * @param float     $dividend
     */
    public function __construct(\DateTime $date, $split)
    {
        $this->date = $date;
        $this->split = $split;
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
    public function getSplit(): float
    {
        return $this->split;
    }
}
