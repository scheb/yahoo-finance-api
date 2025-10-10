<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use GuzzleHttp\Exception\GuzzleException;
use Scheb\YahooFinanceApi\Context\ContextManagerInterface;
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

    public function __construct(
        private readonly ContextManagerInterface $contextManager,
        private readonly ResultDecoder $resultDecoder,
    ) {
    }

    /**
     * Search for stocks.
     *
     * @return SearchResult[]
     *
     * @throws GuzzleException|ApiException
     */
    public function search(string $searchTerm, string $locale = 'en-US', int $limit = 10): array
    {
        $url = 'https://query{queryServer}.finance.yahoo.com/v1/finance/search?'
            .'q='.urlencode($searchTerm)
            .'&lang='.urlencode($locale)
            .'&region=US&quotesCount='.$limit
            .'&quotesQueryId=tss_match_phrase_query&multiQuoteQueryId=multi_quote_single_token_query&enableCb=false&enableNavLinks=true&enableCulturalAssets=true&enableNews=false&enableResearchReports=false&enableLists=false&listsCount=0&recommendCount=0&enablePrivateCompany=true';

        $response = $this->contextManager->request('GET', $url);

        return $this->resultDecoder->transformSearchResult((string) $response->getBody());
    }

    /**
     * Get historical data for a symbol.
     *
     * @return HistoricalData[]
     *
     * @throws GuzzleException|ApiException|\InvalidArgumentException
     */
    public function getHistoricalQuoteData(string $symbol, string $interval, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $this->validateIntervals($interval);
        $this->validateDates($startDate, $endDate);

        $responseBody = $this->getHistoricalDataResponse($symbol, $interval, $startDate, $endDate, self::FILTER_HISTORICAL);

        return $this->resultDecoder->transformHistoricalDataResult($responseBody);
    }

    /**
     * Get dividend data for a symbol.
     *
     * @return DividendData[]
     *
     * @throws GuzzleException|ApiException|\InvalidArgumentException
     */
    public function getHistoricalDividendData(string $symbol, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $this->validateDates($startDate, $endDate);

        $responseBody = $this->getHistoricalDataResponse($symbol, self::INTERVAL_1_MONTH, $startDate, $endDate, self::FILTER_DIVIDENDS);

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
     * @throws GuzzleException|ApiException
     */
    public function getHistoricalSplitData(string $symbol, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $this->validateDates($startDate, $endDate);

        $responseBody = $this->getHistoricalDataResponse($symbol, self::INTERVAL_1_MONTH, $startDate, $endDate, self::FILTER_SPLITS);

        $historicData = $this->resultDecoder->transformSplitDataResult($responseBody);
        usort($historicData, fn (SplitData $a, SplitData $b): int =>
            // Data is not necessary in order, so ensure ascending order by date
            $a->getDate() <=> $b->getDate());

        return $historicData;
    }

    /**
     * Get quote for a single symbol.
     *
     * @throws GuzzleException|ApiException
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
     *
     * @throws GuzzleException|ApiException
     */
    public function getQuotes(array $symbols): array
    {
        return $this->fetchQuotes($symbols);
    }

    /**
     * Get exchange rate for two currencies. Accepts concatenated ISO 4217 currency codes such as "GBP" or "USD".
     *
     * @throws GuzzleException|ApiException
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
     *
     * @throws GuzzleException|ApiException
     */
    public function getExchangeRates(array $currencyPairs): array
    {
        $currencySymbols = array_map(function (array $currencies): string {
            return implode('', $currencies).self::CURRENCY_SYMBOL_SUFFIX; // Currency pairs are suffixed with "=X"
        }, $currencyPairs);

        return $this->fetchQuotes($currencySymbols);
    }

    /**
     * Fetch quote data from API.
     *
     * @return Quote[]
     *
     * @throws GuzzleException|ApiException
     */
    private function fetchQuotes(array $symbols): array
    {
        // Fetch quotes
        $url = 'https://query{queryServer}.finance.yahoo.com/v7/finance/quote?crumb={crumb}&symbols='.urlencode(implode(',', $symbols));
        $responseBody = (string) $this->contextManager->request('GET', $url)->getBody();

        return $this->resultDecoder->transformQuotes($responseBody);
    }

    /**
     * @throws GuzzleException
     */
    private function getHistoricalDataResponse(string $symbol, string $interval, \DateTimeInterface $startDate, \DateTimeInterface $endDate, string $filter): string
    {
        $url = 'https://query{queryServer}.finance.yahoo.com/v8/finance/chart/'.urlencode($symbol).'?period1='.$startDate->getTimestamp().'&period2='.$endDate->getTimestamp().'&interval='.$interval.'&events='.$filter;
        $response = $this->contextManager->request('GET', $url);

        return (string) $response->getBody();
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function validateIntervals(string $interval): void
    {
        $allowedIntervals = [self::INTERVAL_1_DAY, self::INTERVAL_1_WEEK, self::INTERVAL_1_MONTH];
        if (!\in_array($interval, $allowedIntervals)) {
            throw new \InvalidArgumentException(\sprintf('Interval must be one of: %s', implode(', ', $allowedIntervals)));
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function validateDates(\DateTimeInterface $startDate, \DateTimeInterface $endDate): void
    {
        if ($startDate > $endDate) {
            throw new \InvalidArgumentException('Start date must be before end date');
        }
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
     *
     * @throws GuzzleException|ApiException
     */
    public function getStockSummary(string $symbol, array $modules = []): array
    {
        // Fetch quotes
        $url = 'https://query{queryServer}.finance.yahoo.com/v10/finance/quoteSummary/'.urlencode($symbol).'?crumb={crumb}&modules='.urlencode(implode(',', $modules));

        $response = $this->contextManager->request('GET', $url);

        return $this->resultDecoder->transformQuotesSummary((string) $response->getBody());
    }

    /**
     * @throws GuzzleException|ApiException
     */
    public function getOptionChain(string $symbol, ?\DateTimeInterface $expiryDate = null): array
    {
        // Fetch options
        $url = 'https://query{queryServer}.finance.yahoo.com/v7/finance/options/'.urlencode($symbol).'?crumb={crumb}';
        if ($expiryDate instanceof \DateTimeInterface) {
            $url .= '&date='.$expiryDate->getTimestamp();
        }
        $response = $this->contextManager->request('GET', $url);

        return $this->resultDecoder->transformOptionChains((string) $response->getBody());
    }
}
