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

/**
 * @final
 */
class ApiClient
{
    public const INTERVAL_1_DAY = '1d';
    public const INTERVAL_1_WEEK = '1wk';
    public const INTERVAL_1_MONTH = '1mo';
    public const CURRENCY_SYMBOL_SUFFIX = '=X';
    private const FILTER_HISTORICAL = 'history';
    private const FILTER_DIVIDENDS = 'div';
    private const FILTER_SPLITS = 'split';

    private readonly string $userAgent;

    public function __construct(
        private readonly ClientInterface $client,
        private readonly ResultDecoder $resultDecoder,
    ) {
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
    public function search(string $searchTerm, string $locale = 'en-US', int $limit = 10): array
    {
        $qs = $this->getRandomQueryServer();
        $url = 'https://query'.$qs.'.finance.yahoo.com/v1/finance/search?'
            .'q='.urlencode($searchTerm)
            .'&lang='.urlencode($locale)
            .'&region=US&quotesCount='.$limit
            .'&quotesQueryId=tss_match_phrase_query&multiQuoteQueryId=multi_quote_single_token_query&enableCb=false&enableNavLinks=true&enableCulturalAssets=true&enableNews=false&enableResearchReports=false&enableLists=false&listsCount=0&recommendCount=0&enablePrivateCompany=true';

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
        usort($historicData, fn (DividendData $a, DividendData $b): int =>
            // Data is not necessary in order, so ensure ascending order by date
            $a->getDate() <=> $b->getDate());

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
        usort($historicData, fn (SplitData $a, SplitData $b): int =>
            // Data is not necessary in order, so ensure ascending order by date
            $a->getDate() <=> $b->getDate());

        return $historicData;
    }

    /**
     * Get quote for a single symbol.
     */
    public function getQuote(string $symbol): ?Quote
    {
        $list = $this->fetchQuotes([$symbol]);

        return $list[0] ?? null;
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

        return $list[0] ?? null;
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
        $currencySymbols = array_map(function (array $currencies): string {
            return implode('', $currencies).self::CURRENCY_SYMBOL_SUFFIX; // Currency pairs are suffixed with "=X"
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
        return random_int(1, 2);
    }

    /**
     * @deprecated In future versions, this function will be removed. Please consider using getStockSummary() instead.
     */
    public function stockSummary(string $symbol): array
    {
        return $this->getStockSummary($symbol);
    }

    /**
     * @param array $modules List of modules to be fetched.
     *
     * Known modules:
     *   summaryDetail,
     *   quoteType,
     *   assetProfile,
     *   defaultKeyStatistics,
     *   financialData,
     *   recommendationTrend,
     *   upgradeDowngradeHistory,
     *   majorHoldersBreakdown,
     *   insiderHolders,
     *   netSharePurchaseActivity,
     *   earnings,
     *   earningsHistory,
     *   earningsTrend,
     *   industryTrend,
     *   indexTrend,
     *   sectorTrend
     */
    public function getStockSummary(string $symbol, array $modules = []): array
    {
        $qs = $this->getRandomQueryServer();

        // Initialize session cookies
        $cookieJar = $this->getCookies();

        // Get crumb value
        $crumb = $this->getCrumb($qs, $cookieJar);

        // Fetch quotes
        $modulesParam = implode(',', $modules);
        $url = 'https://query'.$qs.'.finance.yahoo.com/v10/finance/quoteSummary/'.$symbol.'?crumb='.$crumb.'&modules='.$modulesParam;
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
        if ($expiryDate instanceof \DateTimeInterface) {
            $url .= '&date='.$expiryDate->getTimestamp();
        }
        $responseBody = (string) $this->client->request('GET', $url, ['cookies' => $cookieJar, 'headers' => $this->getHeaders()])->getBody();

        return $this->resultDecoder->transformOptionChains($responseBody);
    }
}
