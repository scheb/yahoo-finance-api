<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use Scheb\YahooFinanceApi\Exception\ApiException;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;

class ApiClient
{
    public const INTERVAL_1_DAY = '1d';
    public const INTERVAL_1_WEEK = '1wk';
    public const INTERVAL_1_MONTH = '1mo';
    public const FILTER_HISTORICAL = 'history';
    public const FILTER_DIVIDENDS = 'div';
    public const FILTER_SPLITS = 'split';
    public const CURRENCY_SYMBOL_SUFFIX = '=X';

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
     * Search for stocks.
     *
     * @return array|SearchResult[]
     *
     * @throws ApiException
     */
    public function search(string $searchTerm): array
    {
        $url = 'https://finance.yahoo.com/_finance_doubledown/api/resource/searchassist;gossipConfig=%7B%22queryKey%22:%22query%22,%22resultAccessor%22:%22ResultSet.Result%22,%22suggestionTitleAccessor%22:%22symbol%22,%22suggestionMeta%22:[%22symbol%22],%22url%22:%7B%22query%22:%7B%22region%22:%22US%22,%22lang%22:%22en-US%22%7D%7D%7D;searchTerm='
            .urlencode($searchTerm)
            .'?bkt=[%22findd-ctrl%22,%22fin-strm-test1%22,%22fndmtest%22,%22finnossl%22]&device=desktop&feature=canvassOffnet,finGrayNav,newContentAttribution,relatedVideoFeature,videoNativePlaylist,livecoverage&intl=us&lang=en-US&partner=none&prid=eo2okrhcni00f&region=US&site=finance&tz=UTC&ver=0.102.432&returnMeta=true';
        $responseBody = (string) $this->client->request('GET', $url)->getBody();

        return $this->resultDecoder->transformSearchResult($responseBody);
    }

    /**
     * Get historical data for a symbol.
     *
     * @return array|HistoricalData[]
     *
     * @throws ApiException
     */
    public function getHistoricalData(string $symbol, string $interval, \DateTimeInterface $startDate, \DateTimeInterface $endDate, string $filter = self::FILTER_HISTORICAL): array
    {
        $allowedFilters = [self::FILTER_HISTORICAL, self::FILTER_SPLITS, self::FILTER_DIVIDENDS];
        if (!\in_array($filter, $allowedFilters)) {
            throw new \InvalidArgumentException(sprintf('Filter must be one of: %s', implode(', ', $allowedFilters)));
        }

        $allowedIntervals = [self::INTERVAL_1_DAY, self::INTERVAL_1_WEEK, self::INTERVAL_1_MONTH];
        if (!\in_array($interval, $allowedIntervals)) {
            throw new \InvalidArgumentException(sprintf('Interval must be one of: %s', implode(', ', $allowedIntervals)));
        }

        if ($startDate > $endDate) {
            throw new \InvalidArgumentException('Start date must be before end date');
        }

        $cookieJar = new CookieJar();

        $initialUrl = 'https://finance.yahoo.com/quote/'.urlencode($symbol).'/history?p='.urlencode($symbol);
        $responseBody = (string) $this->client->request('GET', $initialUrl, ['cookies' => $cookieJar])->getBody();
        $crumb = $this->resultDecoder->extractCrumb($responseBody);

        $dataUrl = 'https://query1.finance.yahoo.com/v7/finance/download/'.urlencode($symbol).'?period1='.$startDate->getTimestamp().'&period2='.$endDate->getTimestamp().'&interval='.$interval.'&events='.$filter.'&crumb='.urlencode($crumb);
        $responseBody = (string) $this->client->request('GET', $dataUrl, ['cookies' => $cookieJar])->getBody();

        if ($filter === self::FILTER_DIVIDENDS) {
            return $this->resultDecoder->transformDividendDataResult($responseBody);
        }

        if ($filter === self::FILTER_SPLITS) {
            return $this->resultDecoder->transformSplitDataResult($responseBody);
        }

        return $this->resultDecoder->transformHistoricalDataResult($responseBody);
    }

    /**
     * Get quote for a single symbol.
     */
    public function getQuote(string $symbol): ?Quote
    {
        $list = $this->fetchQuotes([$symbol]);

        return isset($list[0]) ? $list[0] : null;
    }

    /**
     * Get quotes for one or multiple symbols.
     *
     * @return array|Quote[]
     */
    public function getQuotes(array $symbols): array
    {
        return $this->fetchQuotes($symbols);
    }

    /**
     * Get exchange rate for two currencies. Accepts concatenated ISO 4217 currency codes.
     */
    public function getExchangeRate(string $currency1, string $currency2): ?Quote
    {
        $list = $this->getExchangeRates([[$currency1, $currency2]]);

        return isset($list[0]) ? $list[0] : null;
    }

    /**
     * Retrieves currency exchange rates. Accepts concatenated ISO 4217 currency codes such as "GBPUSD".
     *
     * @param array $currencyPairs List of pairs of currencies
     *
     * @return array|Quote[]
     */
    public function getExchangeRates(array $currencyPairs): array
    {
        $currencySymbols = array_map(function (array $currencies) {
            return implode($currencies).self::CURRENCY_SYMBOL_SUFFIX; // Currency pairs are suffixed with "=X"
        }, $currencyPairs);

        return $this->fetchQuotes($currencySymbols);
    }

    /**
     * Fetch quote data from API.
     *
     * @return array|Quote[]
     */
    private function fetchQuotes(array $symbols): array
    {
        $url = 'https://query1.finance.yahoo.com/v7/finance/quote?symbols='.urlencode(implode(',', $symbols));
        $responseBody = (string) $this->client->request('GET', $url)->getBody();

        return $this->resultDecoder->transformQuotes($responseBody);
    }
}
