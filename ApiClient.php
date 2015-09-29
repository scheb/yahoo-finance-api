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
     * @return array
     * @throws \Scheb\YahooFinanceApi\Exception\ApiException
     */
    public function search($searchTerm)
    {
        $url = "http://autoc.finance.yahoo.com/autoc?query=".urlencode($searchTerm)."&callback=YAHOO.Finance.SymbolSuggest.ssCallback";
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
     * Execute the query
     * @param string $query
     * @return array
     * @throws \Scheb\YahooFinanceApi\Exception\ApiException
     */
    private function execQuery($query)
    {
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
            'env' => "http://datatables.org/alltables.env",
            'format' => "json",
            'q' => $query,
        );
        return "http://query.yahooapis.com/v1/public/yql?".http_build_query($params);
    }

}

