<?php
namespace Scheb\YahooFinanceApi\Results;

class SearchResult implements \JsonSerializable
{
    /**
     * @var string
     */
    private $symbol;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $exch;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $exchDisp;

    /**
     * @var string
     */
    private $typeDisp;

    public function __construct($symbol, $name, $exch, $type, $exchDisp, $typeDisp)
    {
        $this->symbol = $symbol;
        $this->name = $name;
        $this->exch = $exch;
        $this->type = $type;
        $this->exchDisp = $exchDisp;
        $this->typeDisp = $typeDisp;
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
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getExch()
    {
        return $this->exch;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getExchDisp()
    {
        return $this->exchDisp;
    }

    /**
     * @return string
     */
    public function getTypeDisp()
    {
        return $this->typeDisp;
    }
}
