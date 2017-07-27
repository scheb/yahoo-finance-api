<?php
namespace Scheb\YahooFinanceApi;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use Scheb\YahooFinanceApi\Exception\ApiException;
use Scheb\YahooFinanceApi\Results\ExchangeRate;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;

class ApiClient
{
    const INTERVAL_1_DAY = '1d';
    const INTERVAL_1_WEEK = '1wk';
    const INTERVAL_1_MONTH = '1mo';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var ResultDecoder
     */
    private $resultDecoder;

    public function __construct(ClientInterface $guzzleClient, ResultDecoder $resultDecoder)
    {
        $this->client = $guzzleClient;
        $this->resultDecoder = $resultDecoder;
    }

    /**
     * Search for stocks
     *
     * @param string $searchTerm
     *
     * @return array|SearchResult[]
     * @throws ApiException
     */
    public function search($searchTerm)
    {
        $url = 'https://finance.yahoo.com/_finance_doubledown/api/resource/searchassist;gossipConfig=%7B%22queryKey%22:%22query%22,%22resultAccessor%22:%22ResultSet.Result%22,%22suggestionTitleAccessor%22:%22symbol%22,%22suggestionMeta%22:[%22symbol%22],%22url%22:%7B%22query%22:%7B%22region%22:%22US%22,%22lang%22:%22en-US%22%7D%7D%7D;searchTerm='
            . urlencode($searchTerm)
            . '?bkt=[%22findd-ctrl%22,%22fin-strm-test1%22,%22fndmtest%22,%22finnossl%22]&device=desktop&feature=canvassOffnet,finGrayNav,newContentAttribution,relatedVideoFeature,videoNativePlaylist,livecoverage&intl=us&lang=en-US&partner=none&prid=eo2okrhcni00f&region=US&site=finance&tz=UTC&ver=0.102.432&returnMeta=true';
        $responseBody = (string)$this->client->request('GET', $url)->getBody();

        return $this->resultDecoder->transformSearchResult($responseBody);

    }

    /**
     * Get historical data for a symbol
     *
     * @param string $symbol
     * @param string $interval
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array|HistoricalData[]
     * @throws ApiException
     */
    public function getHistoricalData($symbol, $interval, \DateTime $startDate, \DateTime $endDate)
    {
        $allowedIntervals = [self::INTERVAL_1_DAY, self::INTERVAL_1_WEEK, self::INTERVAL_1_MONTH];
        if (!in_array($interval, $allowedIntervals)) {
            throw new \InvalidArgumentException('Interval must be one of: ' . implode(', ', $allowedIntervals));
        }

        if ($startDate > $endDate) {
            throw new \InvalidArgumentException('Start date must be before end date');
        }

        $cookieJar = new CookieJar();

        $initialUrl = 'https://finance.yahoo.com/lookup?s=' . urlencode($symbol);
        $responseBody = (string)$this->client->request('GET', $initialUrl, ['cookies' => $cookieJar])->getBody();
        $crumb = $this->resultDecoder->extractCrumb($responseBody);

        $dataUrl = 'https://query1.finance.yahoo.com/v7/finance/download/' . urlencode($symbol) . '?period1=' . $startDate->getTimestamp() . '&period2=' . $endDate->getTimestamp() . '&interval=' . $interval . '&events=history&crumb=' . urlencode($crumb);
        $responseBody = (string)$this->client->request('GET', $dataUrl, ['cookies' => $cookieJar])->getBody();

        return $this->resultDecoder->transformHistoricalDataResult($responseBody);
    }

    /**
     * Get quote for a single symbol
     *
     * @param string $symbol
     *
     * @return Quote|null
     */
    public function getQuote($symbol)
    {
        $list = $this->getQuotes([$symbol]);
        return isset($list[0]) ? $list[0] : null;
    }

    /**
     * Get quotes for one or multiple symbols
     *
     * @param array $symbols
     *
     * @return array|Quote[]
     */
    public function getQuotes(array $symbols)
    {
        $query = "select * from yahoo.finance.quotes where symbol in ('" . implode("','", $symbols) . "')";
        $result = $this->executeYqlQuery($query);
        return $this->resultDecoder->transformQuotes($result);
    }

    /**
     * Get exchange rate for two currencies. Accepts concatenated ISO 4217 currency codes.
     *
     * @param string $currency1
     * @param string $currency2
     *
     * @return ExchangeRate|null
     */
    public function getExchangeRate($currency1, $currency2)
    {
        $list = $this->getExchangeRates([[$currency1, $currency2]]);
        return isset($list[0]) ? $list[0] : null;
    }

    /**
     * Retrieves currency exchange rates. Accepts concatenated ISO 4217 currency codes such as "GBPUSD".
     *
     * @param array $currencyPairs List of pairs of currencies
     *
     * @return array
     */
    public function getExchangeRates(array $currencyPairs)
    {
        $currencyPairs = array_map(function (array $currencies) {
            return implode($currencies);
        }, $currencyPairs);

        $query = "select * from yahoo.finance.xchange where pair in ('" . implode("','", $currencyPairs) . "')";
        $result = $this->executeYqlQuery($query);
        return $this->resultDecoder->transformExchangeRates($result);
    }

    /**
     * Execute a YQL query
     *
     * @param string $query
     *
     * @return array
     * @throws ApiException
     */
    private function executeYqlQuery($query)
    {
        $params = array(
            'env' => "store://datatables.org/alltableswithkeys",
            'format' => "json",
            'q' => $query,
        );
        $url = "http://query.yahooapis.com/v1/public/yql?" . http_build_query($params);

        return (string)$this->client->request('GET', $url)->getBody();
    }
}

