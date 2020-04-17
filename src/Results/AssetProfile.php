<?php

namespace Scheb\YahooFinanceApi\Results;

class AssetProfile implements \JsonSerializable
{
    private $address1;
    private $city;
    private $state;
    private $zip;
    private $country;
    private $phone;
    private $website;
    private $industry;
    private $sector;
    private $longBusinessSummary;
    private $fullTimeEmployees;
    private $companyOfficers = [];
    private $auditRisk;
    private $boardRisk;
    private $compensationRisk;
    private $shareHolderRightsRisk;
    private $overallRisk;
    private $governanceEpochDate;
    private $compensationAsOfEpochDate;
    private $maxAge;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $property => $value) {
            if ($property == "companyOfficers") {
                foreach ($value as $companyOfficerItem) {
                    $this->companyOfficers[] = new CompanyOfficer($companyOfficerItem);
                }
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
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @return mixed
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * @return mixed
     */
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * @return mixed
     */
    public function getLongBusinessSummary()
    {
        return $this->longBusinessSummary;
    }

    /**
     * @return mixed
     */
    public function getFullTimeEmployees()
    {
        return $this->fullTimeEmployees;
    }

    /**
     * @return CompanyOfficer[]
     */
    public function getCompanyOfficers()
    {
        return $this->companyOfficers;
    }

    /**
     * @return mixed
     */
    public function getAuditRisk()
    {
        return $this->auditRisk;
    }

    /**
     * @return mixed
     */
    public function getBoardRisk()
    {
        return $this->boardRisk;
    }

    /**
     * @return mixed
     */
    public function getCompensationRisk()
    {
        return $this->compensationRisk;
    }

    /**
     * @return mixed
     */
    public function getShareHolderRightsRisk()
    {
        return $this->shareHolderRightsRisk;
    }

    /**
     * @return mixed
     */
    public function getOverallRisk()
    {
        return $this->overallRisk;
    }

    /**
     * @return mixed
     */
    public function getGovernanceEpochDate()
    {
        return $this->governanceEpochDate;
    }

    /**
     * @return mixed
     */
    public function getCompensationAsOfEpochDate()
    {
        return $this->compensationAsOfEpochDate;
    }

    /**
     * @return mixed
     */
    public function getMaxAge()
    {
        return $this->maxAge;
    }
}
