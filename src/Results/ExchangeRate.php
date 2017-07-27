<?php
namespace Scheb\YahooFinanceApi\Results;

class ExchangeRate
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $rate;

    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var float
     */
    private $ask;

    /**
     * @var float
     */
    private $bid;

    /**
     * @param string $id
     * @param string $name
     * @param float $rate
     * @param \DateTime $dateTime
     * @param float $ask
     * @param float $bid
     */
    public function __construct($id, $name, $rate, \DateTime $dateTime, $ask, $bid)
    {
        $this->id = $id;
        $this->name = $name;
        $this->rate = $rate;
        $this->dateTime = $dateTime;
        $this->ask = $ask;
        $this->bid = $bid;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @return float
     */
    public function getAsk()
    {
        return $this->ask;
    }

    /**
     * @return float
     */
    public function getBid()
    {
        return $this->bid;
    }
}
