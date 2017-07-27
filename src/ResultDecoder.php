<?php
namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\Exception\ApiException;
use Scheb\YahooFinanceApi\Results\ExchangeRate;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;

class ResultDecoder
{
    const HISTORICAL_DATA_HEADER_LINE = 'Date,Open,High,Low,Close,Adj Close,Volume';

    public function transformSearchResult($responseBody)
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['data']['items']) && is_array($decoded['data']['items'])) {
            throw new ApiException("Yahoo Search API returned an invalid result.", ApiException::INVALID_RESULT);
        }

        return array_map(function ($item) {
            return $this->createSearchResultFromJson($item);
        }, $decoded['data']['items']);
    }

    private function createSearchResultFromJson(array $json)
    {
        $expectedFields = ['symbol', 'name', 'exch', 'type', 'exchDisp', 'typeDisp'];
        $missingFields = array_diff($expectedFields, array_keys($json));
        if ($missingFields) {
            throw new \InvalidArgumentException('Search result is missing fields: ' . implode(', ', $missingFields));
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
        if ($headerLine !== self::HISTORICAL_DATA_HEADER_LINE) {
            $errorMsg = 'CSV header line did not match expected header line, given: ' . $headerLine . ', expected: ' . self::HISTORICAL_DATA_HEADER_LINE;
            throw new ApiException($errorMsg, ApiException::INVALID_RESULT);
        }

        return array_map(function ($line) {
            return $this->createHistoricalData(explode(',', $line));
        }, $lines);
    }

    private function createHistoricalData(array $columns)
    {
        if (count($columns) !== 7) {
            throw new ApiException('CSV did not contain correct number of columns', ApiException::INVALID_RESULT);
        }

        try {
            $date = new \DateTime($columns[0], new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new ApiException('Not a date: ' . $columns[0], ApiException::INVALID_RESULT);
        }

        for ($i = 1; $i <= 6; $i++) {
            if (!is_numeric($columns[$i])) {
                throw new ApiException('Not a number: ' . $columns[$i], ApiException::INVALID_RESULT);
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
            throw new ApiException("Yahoo Search API returned an invalid result.", ApiException::INVALID_RESULT);
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
        return new Quote($json['symbol']);
    }

    public function transformExchangeRates($responseBody)
    {
        $decoded = json_decode($responseBody, true);
        if (!isset($decoded['query']['results']['rate']) && is_array($decoded['query']['results']['rate'])) {
            throw new ApiException("Yahoo Search API returned an invalid result.", ApiException::INVALID_RESULT);
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
        $expectedFields = ['Name', 'Rate', 'Date', 'Time', 'Ask', 'Bid'];
        $missingFields = array_diff($expectedFields, array_keys($json));
        if ($missingFields) {
            throw new \InvalidArgumentException('Search result is missing fields: ' . implode(', ', $missingFields));
        }

        $dateTimeString = $json['Date'] . ' ' . $json['Time'];
        try {
            $dateTime = new \DateTime($dateTimeString);
        } catch (\Exception $e) {
            throw new ApiException('Not a date: ' . $dateTimeString, ApiException::INVALID_RESULT);
        }

        $rate = (float)$json['Rate'];
        $ask = (float)$json['Ask'];
        $bid = (float)$json['Bid'];

        return new ExchangeRate($json['id'], $json['Name'], $rate, $dateTime, $ask, $bid);
    }
}
