<?php

namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\Exception\ApiException;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;

class ResultDecoder
{
    const HISTORICAL_DATA_HEADER_LINE = ['Date', 'Open', 'High', 'Low', 'Close', 'Adj Close', 'Volume'];
    const SEARCH_RESULT_FIELDS = ['symbol', 'name', 'exch', 'type', 'exchDisp', 'typeDisp'];
    const EXCHANGE_RATE_FIELDS = ['Name', 'Rate', 'Date', 'Time', 'Ask', 'Bid'];
    const QUOTE_FIELDS_MAP = [
        'ask' => 'float',
        'askSize' => 'int',
        'averageDailyVolume10Day' => 'int',
        'averageDailyVolume3Month' => 'int',
        'bid' => 'float',
        'bidSize' => 'int',
        'bookValue' => 'float',
        'currency' => 'string',
        'dividendDate' => 'date',
        'earningsTimestamp' => 'date',
        'earningsTimestampStart' => 'date',
        'earningsTimestampEnd' => 'date',
        'epsForward' => 'float',
        'epsTrailingTwelveMonths' => 'float',
        'exchange' => 'string',
        'exchangeDataDelayedBy' => 'int',
        'exchangeTimezoneName' => 'string',
        'exchangeTimezoneShortName' => 'string',
        'fiftyDayAverage' => 'float',
        'fiftyDayAverageChange' => 'float',
        'fiftyDayAverageChangePercent' => 'float',
        'fiftyTwoWeekHigh' => 'float',
        'fiftyTwoWeekHighChange' => 'float',
        'fiftyTwoWeekHighChangePercent' => 'float',
        'fiftyTwoWeekLow' => 'float',
        'fiftyTwoWeekLowChange' => 'float',
        'fiftyTwoWeekLowChangePercent' => 'float',
        'financialCurrency' => 'string',
        'forwardPE' => 'float',
        'fullExchangeName' => 'string',
        'gmtOffSetMilliseconds' => 'int',
        'language' => 'string',
        'longName' => 'string',
        'market' => 'string',
        'marketCap' => 'int',
        'marketState' => 'string',
        'messageBoardId' => 'string',
        'postMarketChange' => 'float',
        'postMarketChangePercent' => 'float',
        'postMarketPrice' => 'float',
        'postMarketTime' => 'date',
        'priceHint' => 'int',
        'priceToBook' => 'float',
        'quoteSourceName' => 'string',
        'quoteType' => 'string',
        'regularMarketChange' => 'float',
        'regularMarketChangePercent' => 'float',
        'regularMarketDayHigh' => 'float',
        'regularMarketDayLow' => 'float',
        'regularMarketOpen' => 'float',
        'regularMarketPreviousClose' => 'float',
        'regularMarketPrice' => 'float',
        'regularMarketTime' => 'date',
        'regularMarketVolume' => 'int',
        'sharesOutstanding' => 'int',
        'shortName' => 'string',
        'sourceInterval' => 'int',
        'symbol' => 'string',
        'tradeable' => 'bool',
        'trailingAnnualDividendRate' => 'float',
        'trailingAnnualDividendYield' => 'float',
        'trailingPE' => 'float',
        'twoHundredDayAverage' => 'float',
        'twoHundredDayAverageChange' => 'float',
        'twoHundredDayAverageChangePercent' => 'float',
    ];

    /**
     * @var array
     */
    private $quoteFields;

    public function __construct()
    {
        $this->quoteFields = array_keys(self::QUOTE_FIELDS_MAP);
        $this->quoteFields[] = 'LastTradeDate';
        $this->quoteFields[] = 'LastTradeDate';
    }

    public function transformSearchResult($responseBody)
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['data']['items']) || !is_array($decoded['data']['items'])) {
            throw new ApiException('Yahoo Search API returned an invalid response', ApiException::INVALID_RESPONSE);
        }

        return array_map(function ($item) {
            return $this->createSearchResultFromJson($item);
        }, $decoded['data']['items']);
    }

    private function createSearchResultFromJson(array $json)
    {
        $missingFields = array_diff(self::SEARCH_RESULT_FIELDS, array_keys($json));
        if ($missingFields) {
            throw new ApiException('Search result is missing fields: '.implode(', ', $missingFields), ApiException::INVALID_RESPONSE);
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

    public function extractCrumb($responseBody)
    {
        if (preg_match('#CrumbStore":{"crumb":"(?<crumb>.+?)"}#', $responseBody, $match)) {
            return json_decode('"'.$match['crumb'].'"');
        } else {
            throw new ApiException('Could not extract crumb from response', ApiException::MISSING_CRUMB);
        }
    }

    public function transformHistoricalDataResult($responseBody)
    {
        $lines = array_map('trim', explode("\n", trim($responseBody)));
        $headerLine = array_shift($lines);
        $expectedHeaderLine = implode(',', self::HISTORICAL_DATA_HEADER_LINE);
        if ($headerLine !== $expectedHeaderLine) {
            throw new ApiException('CSV header line did not match expected header line, given: '.$headerLine.', expected: '.$expectedHeaderLine, ApiException::INVALID_RESPONSE);
        }

        return array_map(function ($line) {
            return $this->createHistoricalData(explode(',', $line));
        }, $lines);
    }

    private function createHistoricalData(array $columns)
    {
        if (7 !== count($columns)) {
            throw new ApiException('CSV did not contain correct number of columns', ApiException::INVALID_RESPONSE);
        }

        try {
            $date = new \DateTime($columns[0], new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new ApiException('Not a date in column "Date":'.$columns[0], ApiException::INVALID_VALUE);
        }

        for ($i = 1; $i <= 6; ++$i) {
            if (!is_numeric($columns[$i]) && 'null' != $columns[$i]) {
                throw new ApiException('Not a number in column "'.self::HISTORICAL_DATA_HEADER_LINE[$i].'": '.$columns[$i], ApiException::INVALID_VALUE);
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

    public function transformQuotes($responseBody)
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['quoteResponse']['result']) || !is_array($decoded['quoteResponse']['result'])) {
            throw new ApiException('Yahoo Search API returned an invalid result.', ApiException::INVALID_RESPONSE);
        }

        $results = $decoded['quoteResponse']['result'];

        // Single element is returned directly in "quote"
        return array_map(function ($item) {
            return $this->createQuote($item);
        }, $results);
    }

    private function createQuote(array $json)
    {
        $mappedValues = [];
        foreach ($json as $field => $value) {
            if (array_key_exists($field, self::QUOTE_FIELDS_MAP)) {
                $type = self::QUOTE_FIELDS_MAP[$field];
                $mappedValues[$field] = $this->mapValue($field, $value, $type);
            }
        }

        return new Quote($mappedValues);
    }

    private function mapValue($field, $rawValue, $type)
    {
        if (null === $rawValue) {
            return null;
        }

        switch ($type) {
            case 'float':
                return $this->mapFloatValue($field, $rawValue);
            case 'percent':
                return $this->mapPercentValue($field, $rawValue);
            case 'int':
                return $this->mapIntValue($field, $rawValue);
            case 'date':
                return $this->mapDateValue($field, $rawValue);
            case 'string':
                return (string) $rawValue;
            case 'bool':
                return $this->mapBoolValue($rawValue);
            default:
                throw new \InvalidArgumentException('Invalid data type '.$type.' for field '.$field);
        }
    }

    private function mapFloatValue($field, $rawValue)
    {
        if (!is_numeric($rawValue)) {
            throw new ApiException('Not a number in field "'.$field.'": '.$rawValue, ApiException::INVALID_VALUE);
        }

        return (float) $rawValue;
    }

    private function mapPercentValue($field, $rawValue)
    {
        if ('%' !== substr($rawValue, -1, 1)) {
            throw new ApiException('Not a percent in field "'.$field.'": '.$rawValue, ApiException::INVALID_VALUE);
        }

        $numericPart = substr($rawValue, 0, strlen($rawValue) - 1);
        if (!is_numeric($numericPart)) {
            throw new ApiException('Not a percent in field "'.$field.'": '.$rawValue, ApiException::INVALID_VALUE);
        }

        return (float) $numericPart;
    }

    private function mapIntValue($field, $rawValue)
    {
        if (!is_numeric($rawValue)) {
            throw new ApiException('Not a number in field "'.$field.'": '.$rawValue, ApiException::INVALID_VALUE);
        }

        return (int) $rawValue;
    }

    private function mapBoolValue($rawValue)
    {
        return (bool) $rawValue;
    }

    private function mapDateValue($field, $rawValue)
    {
        try {
            return new \DateTime('@'.$rawValue);
        } catch (\Exception $e) {
            throw new ApiException('Not a date in field "'.$field.'": '.$rawValue, ApiException::INVALID_VALUE);
        }
    }
}
