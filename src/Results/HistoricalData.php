<?php
namespace Scheb\YahooFinanceApi\Results;

class HistoricalData implements \JsonSerializable
{
    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var float
     */
    private $open;

    /**
     * @var float
     */
    private $high;

    /**
     * @var float
     */
    private $low;

    /**
     * @var float
     */
    private $close;

    /**
     * @var float
     */
    private $adjClose;

    /**
     * @var int
     */
    private $volume;

    /**
     * @param \DateTime $date
     * @param float $open
     * @param float $high
     * @param float $low
     * @param float $close
     * @param float $adjClose
     * @param int $volume
     */
    public function __construct(\DateTime $date, $open, $high, $low, $close, $adjClose, $volume)
    {
        $this->date = $date;
        $this->open = $open;
        $this->high = $high;
        $this->low = $low;
        $this->close = $close;
        $this->adjClose = $adjClose;
        $this->volume = $volume;
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
    public function getOpen()
    {
        return $this->open;
    }

    /**
     * @return float
     */
    public function getHigh()
    {
        return $this->high;
    }

    /**
     * @return float
     */
    public function getLow()
    {
        return $this->low;
    }

    /**
     * @return float
     */
    public function getClose()
    {
        return $this->close;
    }

    /**
     * @return float
     */
    public function getAdjClose()
    {
        return $this->adjClose;
    }

    /**
     * @return int
     */
    public function getVolume()
    {
        return $this->volume;
    }
}
