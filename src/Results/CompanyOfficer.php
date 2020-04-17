<?php

namespace Scheb\YahooFinanceApi\Results;

class CompanyOfficer implements \JsonSerializable
{
    private $maxAge;
    private $name;
    private $age;
    private $title;
    private $yearBorn;
    private $fiscalYear;
    private $totalPay;
    private $exercisedValue;
    private $unexercisedValue;

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
    public function getMaxAge()
    {
        return $this->maxAge;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getYearBorn()
    {
        return $this->yearBorn;
    }

    /**
     * @return mixed
     */
    public function getFiscalYear()
    {
        return $this->fiscalYear;
    }

    /**
     * @return mixed
     */
    public function getTotalPay()
    {
        return $this->totalPay;
    }

    /**
     * @return mixed
     */
    public function getExercisedValue()
    {
        return $this->exercisedValue;
    }

    /**
     * @return mixed
     */
    public function getUnexercisedValue()
    {
        return $this->unexercisedValue;
    }
}
