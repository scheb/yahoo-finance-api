<?php

namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\Exception\ApiException;
use Scheb\YahooFinanceApi\Results\DividendData;
use Scheb\YahooFinanceApi\Results\FundamentalTimeseries;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\KeyStatistics;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;
use Scheb\YahooFinanceApi\Results\SplitData;

class ResultDecoder
{
    const HISTORICAL_DATA_HEADER_LINE = ['Date', 'Open', 'High', 'Low', 'Close', 'Adj Close', 'Volume'];
    const DIVIDENDS_DATA_HEADER_LINE = ['Date', 'Dividends'];
    const SPLITS_DATA_HEADER_LINE = ['Date', 'Stock Splits'];
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
        'preMarketChange' => 'float',
        'preMarketChangePercent' => 'float',
        'preMarketPrice' => 'float',
        'preMarketTime' => 'date',
        'priceHint' => 'int',
        'priceToBook' => 'float',
        'openInterest' => 'float',
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

    const FUNDAMENTAL_TIMESERIES_FIELDS_MAP = [
        'quarterlyPeRatio' => 'float',
        'quarterlyForwardPeRatio' => 'float',
        'trailingEnterprisesValueEBITDARatio' => 'float',
        'quarterlyPegRatio' => 'float',
        'trailingMarketCap' => 'float',
        'trailingForwardPeRatio' => 'float',
        'quarterlyPsRatio' => 'float',
        'quarterlyMarketCap' => 'float',
        'trailingEnterpriseValue' => 'float',
        'quarterlyEnterprisesValueRevenueRatio' => 'float',
        'quarterlyEnterprisesValueEBITDARatio' => 'float',
        'trailingPegRatio' => 'float',
        'trailingPeRatio' => 'float',
        'quarterlyEnterpriseValue' => 'float',
        'trailingEnterprisesValueRevenueRatio' => 'float',
        'quarterlyPbRatio' => 'float',
        'trailingPbRatio' => 'float',
        'trailingPsRatio' => 'float',
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

    public function transformDividendsDataResult($responseBody)
    {
        $lines = array_map('trim', explode("\n", trim($responseBody)));
        $headerLine = array_shift($lines);
        $expectedHeaderLine = implode(',', self::DIVIDENDS_DATA_HEADER_LINE);
        if ($headerLine !== $expectedHeaderLine) {
            throw new ApiException('CSV header line did not match expected header line, given: '.$headerLine.', expected: '.$expectedHeaderLine, ApiException::INVALID_RESPONSE);
        }

        return array_map(function ($line) {
            return $this->createDividendData(explode(',', $line));
        }, $lines);
    }

    public function transformSplitsDataResult($responseBody)
    {
        $lines = array_map('trim', explode("\n", trim($responseBody)));
        $headerLine = array_shift($lines);
        $expectedHeaderLine = implode(',', self::SPLITS_DATA_HEADER_LINE);
        if ($headerLine !== $expectedHeaderLine) {
            throw new ApiException('CSV header line did not match expected header line, given: '.$headerLine.', expected: '.$expectedHeaderLine, ApiException::INVALID_RESPONSE);
        }

        return array_map(function ($line) {
            return $this->createSplitData(explode(',', $line));
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

    private function createDividendData(array $columns)
    {
        if (2 !== count($columns)) {
            throw new ApiException('CSV did not contain correct number of columns', ApiException::INVALID_RESPONSE);
        }

        try {
            $date = new \DateTime($columns[0], new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new ApiException('Not a date in column "Date":'.$columns[0], ApiException::INVALID_VALUE);
        }

        if (!is_numeric($columns[1]) && 'null' != $columns[1]) {
            throw new ApiException('Not a number in column "'.self::HISTORICAL_DATA_HEADER_LINE[$i].'": '.$columns[$i], ApiException::INVALID_VALUE);
        }

        $dividend = (float) $columns[1];

        return new DividendData($date, $dividend);
    }

    private function createSplitData(array $columns)
    {
        if (2 !== count($columns)) {
            throw new ApiException('CSV did not contain correct number of columns', ApiException::INVALID_RESPONSE);
        }

        try {
            $date = new \DateTime($columns[0], new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new ApiException('Not a date in column "Date":'.$columns[0], ApiException::INVALID_VALUE);
        }

        if (!is_string($columns[1]) && 'null' != $columns[1]) {
            throw new ApiException('Not a number in column "'.self::HISTORICAL_DATA_HEADER_LINE[$i].'": '.$columns[$i], ApiException::INVALID_VALUE);
        }

        return new SplitData($date, $columns[1]);
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

    public function transformFundamentalTimeseries($responseBody)
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['timeseries']['result']) || !is_array($decoded['timeseries']['result'])) {
            throw new ApiException('Yahoo Search API returned an invalid result.', ApiException::INVALID_RESPONSE);
        }

        $results = $decoded['timeseries']['result'];

        $arrayModels = array_map(function ($item) {
            return $this->createFundamentalTimeseries($item);
        }, $results);

        return call_user_func_array('array_merge', $arrayModels);
    }

    public function transformKeyStatistics($responseBody)
    {

        if (preg_match('#root.App.main = (?<json>.+?);\n#', $responseBody, $match)) {
            $json = json_decode($match['json'], true);
        } else {
            throw new ApiException('Could not extract json from response', ApiException::INVALID_RESPONSE);
        }

        if (
            !isset($json["context"]["dispatcher"]["stores"]["QuoteSummaryStore"])
        ) {
            throw new ApiException('Yahoo Search API returned an invalid result.', ApiException::INVALID_RESPONSE);
        }

        return new KeyStatistics($json["context"]["dispatcher"]["stores"]["QuoteSummaryStore"]);



//        $decoded = json_decode($responseBody, true);
//        if (!isset($decoded['timeseries']['result']) || !is_array($decoded['timeseries']['result'])) {
//            throw new ApiException('Yahoo Search API returned an invalid result.', ApiException::INVALID_RESPONSE);
//        }
//
//        $results = $decoded['timeseries']['result'];
//
//        $arrayModels = array_map(function ($item) use ($models) {
//            return $this->createFundamentalTimeseries($item);
//        }, $results);
//
//        return call_user_func_array('array_merge', $arrayModels);
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

    private function createFundamentalTimeseries(array $json)
    {
        $models = [];
        if (
            $json['meta'] && $json['meta']['type'] &&
            isset($json['meta']['type'][0]) &&
            isset($json[$json['meta']['type'][0]]) &&
            array_key_exists($json['meta']['type'][0], self::FUNDAMENTAL_TIMESERIES_FIELDS_MAP)
        ) {
            $fundamentalType = $json['meta']['type'][0];
            foreach ($json[$fundamentalType] as $ind => $item) {
                if (
                    isset($json['timestamp'][$ind]) &&
                    isset($json[$fundamentalType][$ind]) &&
                    isset($json[$fundamentalType][$ind]['periodType']) &&
                    isset($json[$fundamentalType][$ind]['reportedValue']) &&
                    isset($json[$fundamentalType][$ind]['reportedValue']['raw'])
                ) {
                    $models[] = new FundamentalTimeseries(
                        $this->mapValue($fundamentalType, $fundamentalType, 'string'),
                        $this->mapValue($fundamentalType, $json[$fundamentalType][$ind]['reportedValue']['raw'], self::FUNDAMENTAL_TIMESERIES_FIELDS_MAP[$fundamentalType]),
                        $this->mapValue($fundamentalType, $json['timestamp'][$ind], 'date'),
                        $this->mapValue($fundamentalType, $json[$fundamentalType][$ind]['periodType'], 'string')
                    );
                }
            }
        }

        return $models;
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
