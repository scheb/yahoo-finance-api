<?php
namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\Exception\HttpException;
use Scheb\YahooFinanceApi\Exception\ApiException;

class ApiClient
{
    /**
     * @var int $timeout
     */
    private $timeout;
    private $query;


    /**
     * ApiClient constructor.
     * @param int $timeout
     */
    public function __construct($timeout = 5)
    {
        $this->timeout = $timeout;
    }



    /**
     * Search for stocks
     * @param string $searchTerm
     * @param string $region
     * @param string $lang
     * @return array
     * @throws \Scheb\YahooFinanceApi\Exception\ApiException
     */
    public function search($searchTerm, $region = '', $lang = '')
    {
        $url = "http://autoc.finance.yahoo.com/autoc?query=".urlencode($searchTerm)."&region=".$region."&lang=".$lang."&callback=YAHOO.Finance.SymbolSuggest.ssCallback";
        try
        {
            $client = new HttpClient($url, $this->timeout);
            $response = $client->execute();
        }
        catch (HttpException $e)
        {
            throw new ApiException("Yahoo Search API is not available.", ApiException::UNAVIALABLE, $e);
        }

        //Remove callback function from response
        $response = preg_replace("#^YAHOO\\.Finance\\.SymbolSuggest\\.ssCallback\\((.*)\\)$#", "$1", $response);

        $decoded = json_decode($response, true);
        if (!isset($decoded['ResultSet']['Result']))
        {
            throw new ApiException("Yahoo Search API returned an invalid result.", ApiException::INVALID_RESULT);
        }
        return $decoded;
    }



    /**
     * Get historical data for a symbol
     *
     * @param string $symbol
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array
     * @throws \Scheb\YahooFinanceApi\Exception\ApiException
     */
    public function getHistoricalData($symbol, \DateTime $startDate, \DateTime $endDate)
    {
        $query = "select * from yahoo.finance.historicaldata where startDate='".$startDate->format("Y-m-d")."' and endDate='".$endDate->format("Y-m-d")."' and symbol='".$symbol."'";
        return $this->execQuery($query);
    }



    /**
     * Get quotes for one or multiple symbols
     *
     * @param array|string $symbols
     * @return array
     * @throws \Scheb\YahooFinanceApi\Exception\ApiException
     */
    public function getQuotes($symbols)
    {
        if (is_string($symbols))
        {
            $symbols = array($symbols);
        }
        $query = "select * from yahoo.finance.quotes where symbol in ('".implode("','", $symbols)."')";
        return $this->execQuery($query);
    }



    /**
     * Get quotes list for one or multiple symbols
     *
     * @param array|string $symbols
     * @return array
     * @throws \Scheb\YahooFinanceApi\Exception\ApiException
     */
    public function getQuotesList($symbols)
    {
        if (is_string($symbols))
        {
            $symbols = array($symbols);
        }
        $query = "select * from yahoo.finance.quoteslist where symbol in ('".implode("','", $symbols)."')";
        return $this->execQuery($query);
    }



    /**
     * Retrieves currency exchange rate data for given pair(s). Accepts concatenated ISO 4217 currency codes such as "GBPUSD".
     *
     * @param array|string $pairs
     * @return array
     * @throws \Scheb\YahooFinanceApi\Exception\ApiException
     */
    public function getCurrenciesExchangeRate($pairs)
    {
        if (is_string($pairs))
        {
            $pairs = array($pairs);
        }
        $query = "select * from yahoo.finance.xchange where pair in ('".implode("','", $pairs)."')";
        return $this->execQuery($query);
    }


    /**
     * Execute the query
     * @param string $query
     * @return array
     * @throws \Scheb\YahooFinanceApi\Exception\ApiException
     */
    private function execQuery($query)
    {
        $this->query = $query;
        try
        {
            $url = $this->createUrl($query);
            $client = new HttpClient($url, $this->timeout);
            $response = $client->execute();
        }
        catch (HttpException $e)
        {
            throw new ApiException("Yahoo Finance API is not available.", ApiException::UNAVIALABLE, $e);
        }
        $decoded = json_decode($response, true);
        if (!isset($decoded['query']['results']) || count($decoded['query']['results']) === 0)
        {
            throw new ApiException("Yahoo Finance API did not return a result.", ApiException::EMPTY_RESULT);
        }
        return $decoded;
    }



    /**
     * Create the URL to call
     * @param array $query
     * @return string
     */
    private function createUrl($query)
    {
        $params = array(
            'env' => "store://datatables.org/alltableswithkeys",
            'format' => "json",
            'q' => $query,
        );
        return "http://query.yahooapis.com/v1/public/yql?".http_build_query($params);
    }

    /**
     * Return the last executed query
     * @return string
     */

    public function getLastQuery()
    {
        return $this->query;
    }

}

