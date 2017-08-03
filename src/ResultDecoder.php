<?php
namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\Exception\ApiException;
use Scheb\YahooFinanceApi\Results\ExchangeRate;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;

class ResultDecoder
{
    const HISTORICAL_DATA_HEADER_LINE = ['Date', 'Open', 'High', 'Low', 'Close', 'Adj Close', 'Volume'];
    const SEARCH_RESULT_FIELDS = ['symbol', 'name', 'exch', 'type', 'exchDisp', 'typeDisp'];
    const EXCHANGE_RATE_FIELDS = ['Name', 'Rate', 'Date', 'Time', 'Ask', 'Bid'];
    const QUOTE_FIELDS_MAP = [
        'AverageDailyVolume' => ['averageDailyVolume', 'int'],
        'BookValue' => ['bookValue', 'float'],
        'Change' => ['change', 'float'],
        'Currency' => ['currency', 'string'],
        'DividendShare' => ['dividendShare', 'float'],
        'EarningsShare' => ['earningsShare', 'float'],
        'EPSEstimateCurrentYear' => ['epsEstimateCurrentYear', 'float'],
        'EPSEstimateNextYear' => ['epsEstimateNextYear', 'float'],
        'EPSEstimateNextQuarter' => ['epsEstimateNextQuarter', 'float'],
        'DaysLow' => ['dayLow', 'float'],
        'DaysHigh' => ['dayHigh', 'float'],
        'YearLow' => ['yearLow', 'float'],
        'YearHigh' => ['yearHigh', 'float'],
        'MarketCapitalization' => ['marketCapitalization', 'string'],
        'EBITDA' => ['ebitda', 'string'],
        'ChangeFromYearLow' => ['changeFromYearLow', 'float'],
        'PercentChangeFromYearLow' => ['percentChangeFromYearLow', 'percent'],
        'ChangeFromYearHigh' => ['changeFromYearHigh', 'float'],
        'PercebtChangeFromYearHigh' => ['percentChangeFromYearHigh', 'percent'],
        'LastTradePriceOnly' => ['lastTradePrice', 'float'],
        'FiftydayMovingAverage' => ['fiftyDayMovingAverage', 'float'],
        'TwoHundreddayMovingAverage' => ['twoHundredDayMovingAverage', 'float'],
        'ChangeFromTwoHundreddayMovingAverage' => ['changeFromTwoHundredDayMovingAverage', 'float'],
        'PercentChangeFromTwoHundreddayMovingAverage' => ['percentChangeFromTwoHundredDayMovingAverage', 'percent'],
        'ChangeFromFiftydayMovingAverage' => ['changeFromFiftyDayMovingAverage', 'float'],
        'PercentChangeFromFiftydayMovingAverage' => ['percentChangeFromFiftyDayMovingAverage', 'percent'],
        'Name' => ['name', 'string'],
        'Open' => ['open', 'float'],
        'PreviousClose' => ['previousClose', 'float'],
        'ChangeinPercent' => ['changeInPercent', 'percent'],
        'PriceSales' => ['priceSales', 'float'],
        'PriceBook' => ['priceBook', 'float'],
        'ExDividendDate' => ['exDividendDate', 'date'],
        'PERatio' => ['peRatio', 'float'],
        'DividendPayDate' => ['dividendPayDate', 'date'],
        'PEGRatio' => ['pegRatio', 'float'],
        'PriceEPSEstimateCurrentYear' => ['priceEpsEstimateCurrentYear', 'float'],
        'PriceEPSEstimateNextYear' => ['priceEpsEstimateNextYear', 'float'],
        'Symbol' => ['symbol', 'string'],
        'ShortRatio' => ['shortRatio', 'float'],
        'OneyrTargetPrice' => ['oneYearTargetPrice', 'float'],
        'Volume' => ['volume', 'int'],
        'StockExchange' => ['stockExchange', 'string'],
        'DividendYield' => ['dividendYield', 'float'],
        'PercentChange' => ['percentChange', 'percent'],
    ];

    /**
     * @var array
     */
    private $quoteFields;

    public function __construct()
    {
        $this->quoteFields = array_keys(self::QUOTE_FIELDS_MAP);
        $this->quoteFields[] = "LastTradeDate";
        $this->quoteFields[] = "LastTradeDate";
    }

    public function transformSearchResult($responseBody)
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['data']['items']) && is_array($decoded['data']['items'])) {
            throw new ApiException("Yahoo Search API returned an invalid response", ApiException::INVALID_RESPONSE);
        }

        return array_map(function ($item) {
            return $this->createSearchResultFromJson($item);
        }, $decoded['data']['items']);
    }

    private function createSearchResultFromJson(array $json)
    {
        $missingFields = array_diff(self::SEARCH_RESULT_FIELDS, array_keys($json));
        if ($missingFields) {
            throw new ApiException('Search result is missing fields: ' . implode(', ', $missingFields), ApiException::INVALID_RESPONSE);
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
            return json_decode('"' . $match['crumb'] . '"');
        } else {
            throw new ApiException('Could not extract crumb from response', ApiException::MISSING_CRUMB);
        }
    }

    public function transformHistoricalDataResult($responseBody)
    {
        $lines = explode("\n", trim($responseBody));
        $headerLine = array_shift($lines);
        $expectedHeaderLine = implode(',', self::HISTORICAL_DATA_HEADER_LINE);
        if ($headerLine !== $expectedHeaderLine) {
            throw new ApiException('CSV header line did not match expected header line, given: ' . $headerLine . ', expected: ' . $expectedHeaderLine, ApiException::INVALID_RESPONSE);
        }

        return array_map(function ($line) {
            return $this->createHistoricalData(explode(',', $line));
        }, $lines);
    }

    private function createHistoricalData(array $columns)
    {
        if (count($columns) !== 7) {
            throw new ApiException('CSV did not contain correct number of columns', ApiException::INVALID_RESPONSE);
        }

        try {
            $date = new \DateTime($columns[0], new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new ApiException('Not a date in column "Date":' . $columns[0], ApiException::INVALID_VALUE);
        }

        for ($i = 1; $i <= 6; $i++) {
            if (!is_numeric($columns[$i])) {
                throw new ApiException('Not a number in column "' . self::HISTORICAL_DATA_HEADER_LINE[$i] . '": ' . $columns[$i], ApiException::INVALID_VALUE);
            }
        }

        $open = (float)$columns[1];
        $high = (float)$columns[2];
        $low = (float)$columns[3];
        $close = (float)$columns[4];
        $adjClose = (float)$columns[5];
        $volume = (int)$columns[6];

        return new HistoricalData($date, $open, $high, $low, $close, $adjClose, $volume);
    }

    public function transformQuotes($responseBody)
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['query']['results']['quote']) && is_array($decoded['query']['results']['quote'])) {
            throw new ApiException("Yahoo Search API returned an invalid result.", ApiException::INVALID_RESPONSE);
        }

        $results = $decoded['query']['results']['quote'];

        // Single element is returned directly in "quote"
        if (isset($results['symbol'])) {
            return [$this->createQuote($results)];
        } else {
            return array_map(function ($item) {
                return $this->createQuote($item);
            }, $results);
        }
    }

    private function createQuote(array $json)
    {
        $missingFields = array_diff($this->quoteFields, array_keys($json));
        if ($missingFields) {
            throw new ApiException('Quote is missing fields: ' . implode(', ', $missingFields), ApiException::INVALID_RESPONSE);
        }

        $mappedValues = [];
        foreach ($json as $field => $value) {
            if (array_key_exists($field, self::QUOTE_FIELDS_MAP)) {
                list($mappedField, $type) = self::QUOTE_FIELDS_MAP[$field];
                $mappedValues[$mappedField] = $this->mapValue($field, $value, $type);
            }
        }

        if ($json['LastTradeDate'] && $json['LastTradeTime']) {
            $dateTimeString = $json['LastTradeDate'] . ' ' . $json['LastTradeTime'];
            $mappedValues['lastTradeDateTime'] = $this->mapDateValue('LastTradeDate/LastTradeTime', $dateTimeString);
        }

        return new Quote($mappedValues);
    }

    private function mapValue($field, $rawValue, $type)
    {
        if ($rawValue === null) {
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
                return (string)$rawValue;
            default:
                throw new \InvalidArgumentException('Invalid data type ' . $type);
        }
    }

    private function mapFloatValue($field, $rawValue)
    {
        if (!is_numeric($rawValue)) {
            throw new ApiException('Not a number in field "' . $field . '": ' . $rawValue, ApiException::INVALID_VALUE);
        }
        return (float)$rawValue;
    }

    private function mapPercentValue($field, $rawValue)
    {
        if (substr($rawValue, -1, 1) !== '%') {
            throw new ApiException('Not a percent in field "' . $field . '": ' . $rawValue, ApiException::INVALID_VALUE);
        }

        $numericPart = substr($rawValue, 0, strlen($rawValue) - 1);
        if (!is_numeric($numericPart)) {
            throw new ApiException('Not a percent in field "' . $field . '": ' . $rawValue, ApiException::INVALID_VALUE);
        }

        return (float)$numericPart;
    }

    private function mapIntValue($field, $rawValue)
    {
        if (!is_numeric($rawValue)) {
            throw new ApiException('Not a number in field "' . $field . '": ' . $rawValue, ApiException::INVALID_VALUE);
        }
        return (int)$rawValue;
    }

    private function mapDateValue($field, $rawValue)
    {
        try {
            return new \DateTime($rawValue);
        } catch (\Exception $e) {
            throw new ApiException('Not a date in field "' . $field . '": ' . $rawValue, ApiException::INVALID_VALUE);
        }
    }

    public function transformExchangeRates($responseBody)
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['query']['results']['rate']) && is_array($decoded['query']['results']['rate'])) {
            throw new ApiException("Yahoo Search API returned an invalid result", ApiException::INVALID_RESPONSE);
        }

        $results = $decoded['query']['results']['rate'];

        // Single element is returned directly in "quote"
        if (isset($results['id'])) {
            return [$this->createExchangeRate($results)];
        } else {
            return array_map(function ($item) {
                return $this->createExchangeRate($item);
            }, $results);
        }
    }

    private function createExchangeRate(array $json)
    {
        $missingFields = array_diff(self::EXCHANGE_RATE_FIELDS, array_keys($json));
        if ($missingFields) {
            throw new ApiException('Search result is missing fields: ' . implode(', ', $missingFields), ApiException::INVALID_RESPONSE);
        }

        $dateTimeString = $json['Date'] . ' ' . $json['Time'];
        try {
            $dateTime = new \DateTime($dateTimeString);
        } catch (\Exception $e) {
            throw new ApiException('Not a date in field "Date": ' . $dateTimeString, ApiException::INVALID_VALUE);
        }

        $rate = (float)$json['Rate'];
        $ask = (float)$json['Ask'];
        $bid = (float)$json['Bid'];

        return new ExchangeRate($json['id'], $json['Name'], $rate, $dateTime, $ask, $bid);
    }
}
