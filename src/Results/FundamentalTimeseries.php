<?php

namespace Scheb\YahooFinanceApi\Results;

class FundamentalTimeseries implements \JsonSerializable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $periodType;

    public function __construct($name, $value, $date, $periodType)
    {
        $this->name = $name;
        $this->value = $value;
        $this->date = $date;
        $this->periodType = $periodType;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getPeriodType(): string
    {
        return $this->periodType;
    }
}
