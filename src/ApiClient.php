<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use Scheb\YahooFinanceApi\Exception\ApiException;
use Scheb\YahooFinanceApi\Results\DividendData;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;
use Scheb\YahooFinanceApi\Results\SplitData;

class ApiClient
{
    public const INTERVAL_1_DAY = '1d';
    public const INTERVAL_1_WEEK = '1wk';
    public const INTERVAL_1_MONTH = '1mo';
    public const CURRENCY_SYMBOL_SUFFIX = '=X';

    private const FILTER_HISTORICAL = 'history';
    private const FILTER_DIVIDENDS = 'div';
    private const FILTER_SPLITS = 'split';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var ResultDecoder
     */
    private $resultDecoder;

    /**
     * @var string
     */
    private $userAgent;

    public function __construct(ClientInterface $guzzleClient, ResultDecoder $resultDecoder)
    {
        $this->client = $guzzleClient;
        $this->resultDecoder = $resultDecoder;
        $this->userAgent = UserAgent::getRandomUserAgent();
    }

    public function getHeaders(): array
    {
        return [
            'User-Agent' => $this->userAgent,
        ];
    }

    /**
     * Search for stocks.
     *
     * @return SearchResult[]
     *
     * @throws ApiException
     */
    public function search(string $searchTerm, string $locale = 'en-US'): array
    {
        $url = 'https://finance.yahoo.com/_finance_doubledown/api/resource/searchassist;gossipConfig=%7B%22queryKey%22:%22query%22,%22resultAccessor%22:%22ResultSet.Result%22,%22suggestionTitleAccessor%22:%22symbol%22,%22suggestionMeta%22:[%22symbol%22],%22url%22:%7B%22query%22:%7B%22region%22:%22US%22,%22lang%22:%22'
            .urlencode($locale)
            .'%22%7D%7D%7D;searchTerm='
            .urlencode($searchTerm)
            .'?bkt=[%22findd-ctrl%22,%22fin-strm-test1%22,%22fndmtest%22,%22finnossl%22]&device=desktop&feature=canvassOffnet,finGrayNav,newContentAttribution,relatedVideoFeature,videoNativePlaylist,livecoverage&intl=us&lang='
            .urlencode($locale)
            .'&partner=none&prid=eo2okrhcni00f&region=DE&site=finance&tz=UTC&ver=0.102.432&returnMeta=true';
        $responseBody = (string) $this->client->request('GET', $url, ['headers' => $this->getHeaders()])->getBody();

        return $this->resultDecoder->transformSearchResult($responseBody);
    }

    /**
     * Get historical data for a symbol (deprecated).
     *
     * @deprecated In future versions, this function will be removed. Please consider using getHistoricalQuoteData() instead.
     *
     * @return HistoricalData[]
     *
     * @throws ApiException
     */
    public function getHistoricalData(string $symbol, string $interval, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        @trigger_error('[scheb/yahoo-finance-api] getHistoricalData() is deprecated and will be removed in a future release', \E_USER_DEPRECATED);

        return $this->getHistoricalQuoteData($symbol, $interval, $startDate, $endDate);
    }

    /**
     * Get historical data for a symbol.
     *
     * @return HistoricalData[]
     *
     * @throws ApiException
     */
    public function getHistoricalQuoteData(string $symbol, string $interval, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $this->validateIntervals($interval);
        $this->validateDates($startDate, $endDate);

        $responseBody = $this->getHistoricalDataResponseBodyJson($symbol, $interval, $startDate, $endDate, self::FILTER_HISTORICAL);

        return $this->resultDecoder->transformHistoricalDataResult($responseBody);
    }

    /**
     * Get dividend data for a symbol.
     *
     * @return DividendData[]
     *
     * @throws ApiException
     */
    public function getHistoricalDividendData(string $symbol, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $this->validateDates($startDate, $endDate);

        $responseBody = $this->getHistoricalDataResponseBodyJson($symbol, self::INTERVAL_1_MONTH, $startDate, $endDate, self::FILTER_DIVIDENDS);

        $historicData = $this->resultDecoder->transformDividendDataResult($responseBody);
        usort($historicData, function (DividendData $a, DividendData $b): int {
            // Data is not necessary in order, so ensure ascending order by date
            return $a->getDate() <=> $b->getDate();
        });

        return $historicData;
    }

    /**
     * Get stock split data for a symbol.
     *
     * @return SplitData[]
     *
     * @throws ApiException
     */
    public function getHistoricalSplitData(string $symbol, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $this->validateDates($startDate, $endDate);

        $responseBody = $this->getHistoricalDataResponseBodyJson($symbol, self::INTERVAL_1_MONTH, $startDate, $endDate, self::FILTER_SPLITS);

        $historicData = $this->resultDecoder->transformSplitDataResult($responseBody);
        usort($historicData, function (SplitData $a, SplitData $b): int {
            // Data is not necessary in order, so ensure ascending order by date
            return $a->getDate() <=> $b->getDate();
        });

        return $historicData;
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
     * @return Quote[]
     */
    public function getQuotes(array $symbols): array
    {
        return $this->fetchQuotes($symbols);
    }

    /**
     * Get exchange rate for two currencies. Accepts concatenated ISO 4217 currency codes such as "GBP" or "USD".
     */
    public function getExchangeRate(string $currency1, string $currency2): ?Quote
    {
        $list = $this->getExchangeRates([[$currency1, $currency2]]);

        return isset($list[0]) ? $list[0] : null;
    }

    /**
     * Retrieves currency exchange rates. Accepts concatenated ISO 4217 currency codes such as "GBP" or "USD".
     *
     * @param string[][] $currencyPairs List of pairs of currencies, e.g. [["USD", "GBP"]]
     *
     * @return Quote[]
     */
    public function getExchangeRates(array $currencyPairs): array
    {
        $currencySymbols = array_map(function (array $currencies) {
            return implode($currencies).self::CURRENCY_SYMBOL_SUFFIX; // Currency pairs are suffixed with "=X"
        }, $currencyPairs);

        return $this->fetchQuotes($currencySymbols);
    }

    private function getCookies(): CookieJar
    {
        $cookieJar = new CookieJar();

        // Initialize session cookies
        $initialUrl = 'https://fc.yahoo.com';
        $this->client->request('GET', $initialUrl, ['cookies' => $cookieJar, 'http_errors' => false, 'headers' => $this->getHeaders()]);

        return $cookieJar;
    }

    /**
     * Get the crumb value from the Yahoo Finance API.
     */
    private function getCrumb(int $qs, CookieJar $cookies): string
    {
        // Get crumb value
        $initialUrl = 'https://query'.(string) $qs.'.finance.yahoo.com/v1/test/getcrumb';

        return (string) $this->client->request('GET', $initialUrl, ['cookies' => $cookies, 'headers' => $this->getHeaders()])->getBody();
    }

    /**
     * Fetch quote data from API.
     *
     * @return Quote[]
     */
    private function fetchQuotes(array $symbols)
    {
        $qs = $this->getRandomQueryServer();

        // Initialize session cookies
        $cookieJar = $this->getCookies();

        // Get crumb value
        $crumb = $this->getCrumb($qs, $cookieJar);

        // Fetch quotes
        $url = 'https://query'.$qs.'.finance.yahoo.com/v7/finance/quote?crumb='.$crumb.'&symbols='.urlencode(implode(',', $symbols));
        $responseBody = (string) $this->client->request('GET', $url, ['cookies' => $cookieJar, 'headers' => $this->getHeaders()])->getBody();

        return $this->resultDecoder->transformQuotes($responseBody);
    }

    private function getHistoricalDataResponseBodyJson(string $symbol, string $interval, \DateTimeInterface $startDate, \DateTimeInterface $endDate, string $filter): string
    {
        $qs = $this->getRandomQueryServer();
        $dataUrl = 'https://query'.$qs.'.finance.yahoo.com/v8/finance/chart/'.urlencode($symbol).'?period1='.$startDate->getTimestamp().'&period2='.$endDate->getTimestamp().'&interval='.$interval.'&events='.$filter;

        return (string) $this->client->request('GET', $dataUrl, ['headers' => $this->getHeaders()])->getBody();
    }

    private function validateIntervals(string $interval): void
    {
        $allowedIntervals = [self::INTERVAL_1_DAY, self::INTERVAL_1_WEEK, self::INTERVAL_1_MONTH];
        if (!\in_array($interval, $allowedIntervals)) {
            throw new \InvalidArgumentException(\sprintf('Interval must be one of: %s', implode(', ', $allowedIntervals)));
        }
    }

    private function validateDates(\DateTimeInterface $startDate, \DateTimeInterface $endDate): void
    {
        if ($startDate > $endDate) {
            throw new \InvalidArgumentException('Start date must be before end date');
        }
    }

    private function getRandomQueryServer(): int
    {
        return rand(1, 2);
    }

    public function stockSummary(string $symbol): array
    {
        $qs = $this->getRandomQueryServer();

        // Initialize session cookies
        $cookieJar = $this->getCookies();

        // Get crumb value
        $crumb = $this->getCrumb($qs, $cookieJar);

        // Fetch quotes
        $modules = 'financialData,quoteType,defaultKeyStatistics,assetProfile,summaryDetail';
        $url = 'https://query'.$qs.'.finance.yahoo.com/v10/finance/quoteSummary/'.$symbol.'?crumb='.$crumb.'&modules='.$modules;
        $responseBody = (string) $this->client->request('GET', $url, ['cookies' => $cookieJar, 'headers' => $this->getHeaders()])->getBody();

        return $this->resultDecoder->transformQuotesSummary($responseBody);
    }

    public function getOptionChain(string $symbol, ?\DateTimeInterface $expiryDate = null): array
    {
        $qs = $this->getRandomQueryServer();

        // Initialize session cookies
        $cookieJar = $this->getCookies();

        // Get crumb value
        $crumb = $this->getCrumb($qs, $cookieJar);

        // Fetch options
        $url = 'https://query'.$qs.'.finance.yahoo.com/v7/finance/options/'.$symbol.'?crumb='.$crumb;
        if ($expiryDate) {
            $url .= '&date='.(string) $expiryDate->getTimestamp();
        }
        $responseBody = (string) $this->client->request('GET', $url, ['cookies' => $cookieJar, 'headers' => $this->getHeaders()])->getBody();

        return $this->resultDecoder->transformOptionChains($responseBody);
    }
}
