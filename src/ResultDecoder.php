<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\Exception\ApiException;
use Scheb\YahooFinanceApi\Exception\InvalidValueException;
use Scheb\YahooFinanceApi\Results\DividendData;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Option;
use Scheb\YahooFinanceApi\Results\OptionChain;
use Scheb\YahooFinanceApi\Results\OptionContract;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;
use Scheb\YahooFinanceApi\Results\SplitData;

/**
 * @final
 */
class ResultDecoder
{
    public const HISTORICAL_DATA_HEADER_LINE = ['Date', 'Open', 'High', 'Low', 'Close', 'Adj Close', 'Volume'];
    public const DIVIDEND_DATA_HEADER_LINE = ['Date', 'Dividends'];
    public const SPLIT_DATA_HEADER_LINE = ['Date', 'Stock Splits'];
    public const SEARCH_RESULT_FIELDS = ['symbol', 'exchange', 'quoteType', 'exchDisp', 'typeDisp'];

    public const OPTION_CHAIN_FIELDS_MAP = [
        'underlyingSymbol' => ValueMapperInterface::TYPE_STRING,
        'expirationDates' => ValueMapperInterface::TYPE_ARRAY,
        'strikes' => ValueMapperInterface::TYPE_ARRAY,
        'hasMiniOptions' => ValueMapperInterface::TYPE_BOOL,
        'options' => ValueMapperInterface::TYPE_ARRAY,
    ];

    public const OPTION_FIELDS_MAP = [
        'expirationDate' => ValueMapperInterface::TYPE_DATE,
        'hasMiniOptions' => ValueMapperInterface::TYPE_BOOL,
        'calls' => ValueMapperInterface::TYPE_ARRAY,
        'puts' => ValueMapperInterface::TYPE_ARRAY,
    ];

    public const OPTION_CONTRACT_FIELDS_MAP = [
        'contractSymbol' => ValueMapperInterface::TYPE_STRING,
        'strike' => ValueMapperInterface::TYPE_FLOAT,
        'currency' => ValueMapperInterface::TYPE_STRING,
        'lastPrice' => ValueMapperInterface::TYPE_FLOAT,
        'change' => ValueMapperInterface::TYPE_FLOAT,
        'percentChange' => ValueMapperInterface::TYPE_FLOAT,
        'volume' => ValueMapperInterface::TYPE_INT,
        'openInterest' => ValueMapperInterface::TYPE_INT,
        'bid' => ValueMapperInterface::TYPE_FLOAT,
        'ask' => ValueMapperInterface::TYPE_FLOAT,
        'contractSize' => ValueMapperInterface::TYPE_STRING,
        'expiration' => ValueMapperInterface::TYPE_DATE,
        'lastTradeDate' => ValueMapperInterface::TYPE_DATE,
        'impliedVolatility' => ValueMapperInterface::TYPE_FLOAT,
        'inTheMoney' => ValueMapperInterface::TYPE_BOOL,
    ];

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

    public function __construct(private readonly ValueMapperInterface $valueMapper)
    {
    }

    public function transformSearchResult(string $responseBody): array
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['quotes']) || !\is_array($decoded['quotes'])) {
            throw new ApiException('Yahoo Search API returned an invalid response', ApiException::INVALID_RESPONSE);
        }

        return array_map(fn (array $item): SearchResult => $this->createSearchResultFromJson($item), $decoded['quotes']);
    }

    private function createSearchResultFromJson(array $json): SearchResult
    {
        $missingFields = array_diff(self::SEARCH_RESULT_FIELDS, array_keys($json));
        if ([] !== $missingFields) {
            throw new ApiException(\sprintf('Search result is missing fields: %s', implode(', ', $missingFields)), ApiException::INVALID_RESPONSE);
        }

        return new SearchResult(
            $json['symbol'],
            $json['shortname'] ?? null,
            $json['exchange'],
            $json['quoteType'],
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

    private function validateDate(string $value): \DateTime
    {
        try {
            return new \DateTime($value, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new ApiException(\sprintf('Not a date in column "Date":%s', $value), ApiException::INVALID_VALUE);
        }
    }

    public function transformHistoricalDataResult(string $responseBody): array
    {
        $decoded = json_decode($responseBody, true);

        if ((!\is_array($decoded)) || (isset($decoded['chart']['error']))) {
            throw new ApiException('Response is not a valid JSON', ApiException::INVALID_RESPONSE);
        }

        $result = $decoded['chart']['result'][0];

        if (0 === \count($result['indicators']['quote'][0])) {
            return [];
        }

        $entryCount = \count($result['indicators']['quote'][0]['open']);

        $returnArray = [];
        for ($i = 0; $i < $entryCount; ++$i) {
            $returnArray[] = $this->createHistoricalData($result, $i);
        }

        return $returnArray;
    }

    private function createHistoricalData(array $json, int $index): HistoricalData
    {
        $dateStr = date('Y-m-d', $json['timestamp'][$index]);
        if ('0' !== $dateStr) {
            $date = $this->validateDate($dateStr);
        } else {
            throw new ApiException(\sprintf('Not a date in column "Date":%s', $json['timestamp'][$index]), ApiException::INVALID_VALUE);
        }

        foreach (['open', 'high', 'low', 'close', 'volume'] as $column) {
            $columnValue = $json['indicators']['quote'][0][$column][$index];
            if (!is_numeric($columnValue) && 'null' !== $columnValue && !\is_null($columnValue)) {
                throw new ApiException(\sprintf('Not a number in column "%s": %s', $column, $column), ApiException::INVALID_VALUE);
            }
        }

        $columnValue = $json['indicators']['adjclose'][0]['adjclose'][$index];
        if (!is_numeric($columnValue) && 'null' !== $columnValue && !\is_null($columnValue)) {
            throw new ApiException(\sprintf('Not a number in column "%s": %s', 'adjclose', 'adjclose'), ApiException::INVALID_VALUE);
        }

        $open = (float) $json['indicators']['quote'][0]['open'][$index];
        $high = (float) $json['indicators']['quote'][0]['high'][$index];
        $low = (float) $json['indicators']['quote'][0]['low'][$index];
        $close = (float) $json['indicators']['quote'][0]['close'][$index];
        $volume = (int) $json['indicators']['quote'][0]['volume'][$index];
        $adjClose = (float) $json['indicators']['adjclose'][0]['adjclose'][$index];

        return new HistoricalData($date, $open, $high, $low, $close, $adjClose, $volume);
    }

    public function transformDividendDataResult(string $responseBody): array
    {
        $decoded = json_decode($responseBody, true);
        if ((!\is_array($decoded)) || (isset($decoded['chart']['error']))) {
            throw new ApiException('Response is not a valid JSON', ApiException::INVALID_RESPONSE);
        }

        if (!isset($decoded['chart']['result'][0]['events']['dividends'])) {
            return [];
        }

        return array_map(fn (array $item): DividendData => $this->createDividendData($item), $decoded['chart']['result'][0]['events']['dividends']);
    }

    private function createDividendData(array $json): DividendData
    {
        $dateStr = date('Y-m-d', $json['date']);
        if ('0' !== $dateStr) {
            $date = $this->validateDate($dateStr);
        } else {
            throw new ApiException(\sprintf('Not a date in column "Date":%s', $json['date']), ApiException::INVALID_VALUE);
        }

        $dividends = (float) $json['amount'];

        return new DividendData($date, $dividends);
    }

    public function transformSplitDataResult(string $responseBody): array
    {
        $decoded = json_decode($responseBody, true);
        if ((!\is_array($decoded)) || (isset($decoded['chart']['error']))) {
            throw new ApiException('Response is not a valid JSON', ApiException::INVALID_RESPONSE);
        }

        if (!isset($decoded['chart']['result'][0]['events']['splits'])) {
            return [];
        }

        return array_map(fn (array $item): SplitData => $this->createSplitData($item), $decoded['chart']['result'][0]['events']['splits']);
    }

    private function createSplitData(array $json): SplitData
    {
        $dateStr = date('Y-m-d', $json['date']);
        if ('0' !== $dateStr) {
            $date = $this->validateDate($dateStr);
        } else {
            throw new ApiException(\sprintf('Not a date in column "Date":%s', $json['date']), ApiException::INVALID_VALUE);
        }

        $stockSplits = (string) $json['splitRatio'];

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
        return array_map(fn (array $item): Quote => $this->createQuote($item), $results);
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
                    throw new ApiException(\sprintf('Not a %s in field "%s": %s', $type, $field, $value), ApiException::INVALID_VALUE, $e);
                }
            }
        }

        return new Quote($mappedValues);
    }

    public function transformQuotesSummary(string $responseBody): array
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['quoteSummary']['result']) || !\is_array($decoded['quoteSummary']['result'])) {
            throw new ApiException('Yahoo Search API returned an invalid result.', ApiException::INVALID_RESPONSE);
        }

        return $decoded['quoteSummary']['result'];
    }

    public function transformOptionChains(string $responseBody): array
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['optionChain']['result']) || !\is_array($decoded['optionChain']['result'])) {
            throw new ApiException('Yahoo Search API returned an invalid result.', ApiException::INVALID_RESPONSE);
        }

        $results = $decoded['optionChain']['result'];

        // Single element is returned directly in "OptionChain"
        $final = array_map(fn (array $item): OptionChain => $this->createOptionChain($item), $results);

        return $final;
    }

    private function createOptionChain(array $json): OptionChain
    {
        $mappedValues = [];
        foreach ($json as $field => $value) {
            if (!\array_key_exists($field, self::OPTION_CHAIN_FIELDS_MAP)) {
                continue;
            }

            $type = self::OPTION_CHAIN_FIELDS_MAP[$field];
            try {
                if ('options' === $field) {
                    if (!\is_array($value)) {
                        throw new InvalidValueException($type);
                    }

                    $mappedValues[$field] = array_map(fn (array $option): Option => $this->createOption($option), $value);
                } elseif ('expirationDates' === $field) {
                    $mappedValues[$field] = $this->valueMapper->mapValue($value, $type, ValueMapperInterface::TYPE_DATE);
                } elseif ('strikes' === $field) {
                    $mappedValues[$field] = $this->valueMapper->mapValue($value, $type, ValueMapperInterface::TYPE_FLOAT);
                } else {
                    $mappedValues[$field] = $this->valueMapper->mapValue($value, $type);
                }
            } catch (InvalidValueException $e) {
                throw new ApiException(\sprintf('%s in field "%s": %s', $e->getMessage(), $field, $this->jsonEncodeValue($value)), ApiException::INVALID_VALUE, $e);
            }
        }

        return new OptionChain($mappedValues);
    }

    private function createOption(array $json): Option
    {
        $mappedValues = [];
        foreach ($json as $field => $value) {
            if (!\array_key_exists($field, self::OPTION_FIELDS_MAP)) {
                continue;
            }

            $type = self::OPTION_FIELDS_MAP[$field];
            try {
                if ('calls' === $field || 'puts' === $field) {
                    if (!\is_array($value)) {
                        throw new InvalidValueException($type);
                    }

                    $mappedValues[$field] = array_map(fn (array $optionContract): OptionContract => $this->createOptionContract($optionContract), $value);
                } else {
                    $mappedValues[$field] = $this->valueMapper->mapValue($value, $type);
                }
            } catch (InvalidValueException $e) {
                throw new ApiException(\sprintf('%s in field "%s": %s', $e->getMessage(), $field, $this->jsonEncodeValue($value)), ApiException::INVALID_VALUE, $e);
            }
        }

        return new Option($mappedValues);
    }

    private function createOptionContract(array $values): OptionContract
    {
        $mappedValues = [];
        foreach ($values as $property => $value) {
            if (!\array_key_exists($property, self::OPTION_CONTRACT_FIELDS_MAP)) {
                continue;
            }

            try {
                $mappedValues[$property] = $this->valueMapper->mapValue($value, self::OPTION_CONTRACT_FIELDS_MAP[$property]);
            } catch (InvalidValueException $e) {
                throw new ApiException(\sprintf('%s in field "%s": %s', $e->getMessage(), $property, $this->jsonEncodeValue($value)), ApiException::INVALID_VALUE, $e);
            }
        }

        return new OptionContract($mappedValues);
    }

    private function jsonEncodeValue(mixed $value): string
    {
        $encoded = json_encode($value);
        if (false === $encoded) {
            return 'unknown value';
        }

        return $encoded;
    }
}
