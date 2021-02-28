<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\Exception\ApiException;
use Scheb\YahooFinanceApi\Exception\InvalidValueException;
use Scheb\YahooFinanceApi\Results\DividendData;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;
use Scheb\YahooFinanceApi\Results\SplitData;

class ResultDecoder
{
    public const HISTORICAL_DATA_HEADER_LINE = ['Date', 'Open', 'High', 'Low', 'Close', 'Adj Close', 'Volume'];
    public const DIVIDEND_DATA_HEADER_LINE = ['Date', 'Dividends'];
    public const SPLIT_DATA_HEADER_LINE = ['Date', 'Stock Splits'];
    public const SEARCH_RESULT_FIELDS = ['symbol', 'name', 'exch', 'type', 'exchDisp', 'typeDisp'];
    public const QUOTE_FIELDS_MAP = [
        'ask' => ValueMapperInterface::TYPE_FLOAT,
        'askSize' => ValueMapperInterface::TYPE_INT,
        'averageDailyVolume10Day' => ValueMapperInterface::TYPE_INT,
        'averageDailyVolume3Month' => ValueMapperInterface::TYPE_INT,
        'bid' => ValueMapperInterface::TYPE_FLOAT,
        'bidSize' => ValueMapperInterface::TYPE_INT,
        'bookValue' => ValueMapperInterface::TYPE_FLOAT,
        'currency' => ValueMapperInterface::TYPE_STRING,
        'dividendDate' => ValueMapperInterface::TYPE_DATE,
        'earningsTimestamp' => ValueMapperInterface::TYPE_DATE,
        'earningsTimestampStart' => ValueMapperInterface::TYPE_DATE,
        'earningsTimestampEnd' => ValueMapperInterface::TYPE_DATE,
        'epsForward' => ValueMapperInterface::TYPE_FLOAT,
        'epsTrailingTwelveMonths' => ValueMapperInterface::TYPE_FLOAT,
        'exchange' => ValueMapperInterface::TYPE_STRING,
        'exchangeDataDelayedBy' => ValueMapperInterface::TYPE_INT,
        'exchangeTimezoneName' => ValueMapperInterface::TYPE_STRING,
        'exchangeTimezoneShortName' => ValueMapperInterface::TYPE_STRING,
        'fiftyDayAverage' => ValueMapperInterface::TYPE_FLOAT,
        'fiftyDayAverageChange' => ValueMapperInterface::TYPE_FLOAT,
        'fiftyDayAverageChangePercent' => ValueMapperInterface::TYPE_FLOAT,
        'fiftyTwoWeekHigh' => ValueMapperInterface::TYPE_FLOAT,
        'fiftyTwoWeekHighChange' => ValueMapperInterface::TYPE_FLOAT,
        'fiftyTwoWeekHighChangePercent' => ValueMapperInterface::TYPE_FLOAT,
        'fiftyTwoWeekLow' => ValueMapperInterface::TYPE_FLOAT,
        'fiftyTwoWeekLowChange' => ValueMapperInterface::TYPE_FLOAT,
        'fiftyTwoWeekLowChangePercent' => ValueMapperInterface::TYPE_FLOAT,
        'financialCurrency' => ValueMapperInterface::TYPE_STRING,
        'forwardPE' => ValueMapperInterface::TYPE_FLOAT,
        'fullExchangeName' => ValueMapperInterface::TYPE_STRING,
        'gmtOffSetMilliseconds' => ValueMapperInterface::TYPE_INT,
        'language' => ValueMapperInterface::TYPE_STRING,
        'longName' => ValueMapperInterface::TYPE_STRING,
        'market' => ValueMapperInterface::TYPE_STRING,
        'marketCap' => ValueMapperInterface::TYPE_INT,
        'marketState' => ValueMapperInterface::TYPE_STRING,
        'messageBoardId' => ValueMapperInterface::TYPE_STRING,
        'postMarketChange' => ValueMapperInterface::TYPE_FLOAT,
        'postMarketChangePercent' => ValueMapperInterface::TYPE_FLOAT,
        'postMarketPrice' => ValueMapperInterface::TYPE_FLOAT,
        'postMarketTime' => ValueMapperInterface::TYPE_DATE,
        'preMarketChange' => ValueMapperInterface::TYPE_FLOAT,
        'preMarketChangePercent' => ValueMapperInterface::TYPE_FLOAT,
        'preMarketPrice' => ValueMapperInterface::TYPE_FLOAT,
        'preMarketTime' => ValueMapperInterface::TYPE_DATE,
        'priceHint' => ValueMapperInterface::TYPE_INT,
        'priceToBook' => ValueMapperInterface::TYPE_FLOAT,
        'openInterest' => ValueMapperInterface::TYPE_FLOAT,
        'quoteSourceName' => ValueMapperInterface::TYPE_STRING,
        'quoteType' => ValueMapperInterface::TYPE_STRING,
        'regularMarketChange' => ValueMapperInterface::TYPE_FLOAT,
        'regularMarketChangePercent' => ValueMapperInterface::TYPE_FLOAT,
        'regularMarketDayHigh' => ValueMapperInterface::TYPE_FLOAT,
        'regularMarketDayLow' => ValueMapperInterface::TYPE_FLOAT,
        'regularMarketOpen' => ValueMapperInterface::TYPE_FLOAT,
        'regularMarketPreviousClose' => ValueMapperInterface::TYPE_FLOAT,
        'regularMarketPrice' => ValueMapperInterface::TYPE_FLOAT,
        'regularMarketTime' => ValueMapperInterface::TYPE_DATE,
        'regularMarketVolume' => ValueMapperInterface::TYPE_INT,
        'sharesOutstanding' => ValueMapperInterface::TYPE_INT,
        'shortName' => ValueMapperInterface::TYPE_STRING,
        'sourceInterval' => ValueMapperInterface::TYPE_INT,
        'symbol' => ValueMapperInterface::TYPE_STRING,
        'tradeable' => ValueMapperInterface::TYPE_BOOL,
        'trailingAnnualDividendRate' => ValueMapperInterface::TYPE_FLOAT,
        'trailingAnnualDividendYield' => ValueMapperInterface::TYPE_FLOAT,
        'trailingPE' => ValueMapperInterface::TYPE_FLOAT,
        'twoHundredDayAverage' => ValueMapperInterface::TYPE_FLOAT,
        'twoHundredDayAverageChange' => ValueMapperInterface::TYPE_FLOAT,
        'twoHundredDayAverageChangePercent' => ValueMapperInterface::TYPE_FLOAT,
    ];

    /**
     * @var ValueMapperInterface
     */
    private $valueMapper;

    public function __construct(ValueMapperInterface $valueMapper)
    {
        $this->valueMapper = $valueMapper;
    }

    public function transformSearchResult(string $responseBody): array
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['data']['items']) || !\is_array($decoded['data']['items'])) {
            throw new ApiException('Yahoo Search API returned an invalid response', ApiException::INVALID_RESPONSE);
        }

        return array_map(function (array $item) {
            return $this->createSearchResultFromJson($item);
        }, $decoded['data']['items']);
    }

    private function createSearchResultFromJson(array $json): SearchResult
    {
        $missingFields = array_diff(self::SEARCH_RESULT_FIELDS, array_keys($json));
        if ($missingFields) {
            throw new ApiException(sprintf('Search result is missing fields: %s', implode(', ', $missingFields)), ApiException::INVALID_RESPONSE);
        }

        return new SearchResult(
            $json['symbol'],
            $json['name'],
            $json['exch'],
            $json['type'],
            $json['exchDisp'],
            $json['typeDisp']
        );
    }

    public function extractCrumb(string $responseBody): string
    {
        if (preg_match('#CrumbStore":{"crumb":"(?<crumb>.+?)"}#', $responseBody, $match)) {
            return json_decode('"'.$match['crumb'].'"');
        }

        throw new ApiException('Could not extract crumb from response', ApiException::MISSING_CRUMB);
    }

    private function validateHeaderLines(string $responseBody, array $expectedHeader): array
    {
        $lines = array_map('trim', explode("\n", trim($responseBody)));
        $headerLine = array_shift($lines);
        $expectedHeaderLine = implode(',', $expectedHeader);
        if ($headerLine !== $expectedHeaderLine) {
            throw new ApiException(sprintf('CSV header line did not match expected header line, given: %s, expected: %s', $headerLine, $expectedHeaderLine), ApiException::INVALID_RESPONSE);
        }

        return $lines;
    }

    private function validateDate(string $value): \DateTime
    {
        try {
            return new \DateTime($value, new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new ApiException(sprintf('Not a date in column "Date":%s', $value), ApiException::INVALID_VALUE);
        }
    }

    public function transformHistoricalDataResult(string $responseBody): array
    {
        $lines = $this->validateHeaderLines($responseBody, self::HISTORICAL_DATA_HEADER_LINE);

        return array_map(function ($line) {
            return $this->createHistoricalData(explode(',', $line));
        }, $lines);
    }

    private function createHistoricalData(array $columns): HistoricalData
    {
        if (7 !== \count($columns)) {
            throw new ApiException('CSV did not contain correct number of columns', ApiException::INVALID_RESPONSE);
        }

        $date = $this->validateDate($columns[0]);

        for ($i = 1; $i <= 6; ++$i) {
            if (!is_numeric($columns[$i]) && 'null' !== $columns[$i]) {
                throw new ApiException(sprintf('Not a number in column "%s": %s', self::HISTORICAL_DATA_HEADER_LINE[$i], $columns[$i]), ApiException::INVALID_VALUE);
            }
        }

        $open = (float) $columns[1];
        $high = (float) $columns[2];
        $low = (float) $columns[3];
        $close = (float) $columns[4];
        $adjClose = (float) $columns[5];
        $volume = (int) $columns[6];

        return new HistoricalData($date, $open, $high, $low, $close, $adjClose, $volume);
    }

    public function transformDividendDataResult(string $responseBody): array
    {
        $lines = $this->validateHeaderLines($responseBody, self::DIVIDEND_DATA_HEADER_LINE);

        return array_map(function ($line) {
            return $this->createDividendData(explode(',', $line));
        }, $lines);
    }

    private function createDividendData(array $columns): DividendData
    {
        if (2 !== \count($columns)) {
            throw new ApiException('CSV did not contain correct number of columns', ApiException::INVALID_RESPONSE);
        }

        $date = $this->validateDate($columns[0]);

        if (!is_numeric($columns[1]) && 'null' !== $columns[1]) {
            throw new ApiException(sprintf('Not a number in column Dividends: %s', $columns[1]), ApiException::INVALID_VALUE);
        }

        $dividends = (float) $columns[1];

        return new DividendData($date, $dividends);
    }

    public function transformSplitDataResult(string $responseBody): array
    {
        $lines = $this->validateHeaderLines($responseBody, self::SPLIT_DATA_HEADER_LINE);

        return array_map(function ($line) {
            return $this->createSplitData(explode(',', $line));
        }, $lines);
    }

    private function createSplitData(array $columns): SplitData
    {
        if (2 !== \count($columns)) {
            throw new ApiException('CSV did not contain correct number of columns', ApiException::INVALID_RESPONSE);
        }

        $date = $this->validateDate($columns[0]);

        $stockSplits = (string) $columns[1];

        return new SplitData($date, $stockSplits);
    }

    public function transformQuotes(string $responseBody): array
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['quoteResponse']['result']) || !\is_array($decoded['quoteResponse']['result'])) {
            throw new ApiException('Yahoo Search API returned an invalid result.', ApiException::INVALID_RESPONSE);
        }

        $results = $decoded['quoteResponse']['result'];

        // Single element is returned directly in "quote"
        return array_map(function (array $item) {
            return $this->createQuote($item);
        }, $results);
    }

    private function createQuote(array $json): Quote
    {
        $mappedValues = [];
        foreach ($json as $field => $value) {
            if (\array_key_exists($field, self::QUOTE_FIELDS_MAP)) {
                $type = self::QUOTE_FIELDS_MAP[$field];
                try {
                    $mappedValues[$field] = $this->valueMapper->mapValue($value, $type);
                } catch (InvalidValueException $e) {
                    throw new ApiException(sprintf('Not a %s in field "%s": %s', $type, $field, $value), ApiException::INVALID_VALUE, $e);
                }
            }
        }

        return new Quote($mappedValues);
    }
}
