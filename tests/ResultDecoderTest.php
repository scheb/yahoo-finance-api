<?php

namespace Scheb\YahooFinanceApi\Tests;

use Scheb\YahooFinanceApi\ResultDecoder;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;
use PHPUnit\Framework\TestCase;

class ResultDecoderTest extends TestCase
{
    /**
     * @var ResultDecoder
     */
    private $resultDecoder;

    public function setUp()
    {
        $this->resultDecoder = new ResultDecoder();
    }

    /**
     * @test
     */
    public function transformSearchResult_jsonGiven_createArrayOfSearchResult()
    {
        $returnedResult = $this->resultDecoder->transformSearchResult(file_get_contents(__DIR__.'/fixtures/searchResult.json'));

        $this->assertInternalType('array', $returnedResult);
        $this->assertContainsOnlyInstancesOf(SearchResult::class, $returnedResult);
        $this->assertCount(10, $returnedResult);

        $expectedItem = new SearchResult(
            'AAPL',
            'Apple Inc.',
            'NAS',
            'S',
            'NASDAQ',
            'Equity'
        );
        $this->assertEquals($expectedItem, $returnedResult[0]);
    }

    /**
     * @test
     */
    public function extractCrumb_lookupPage_returnCrumbValue()
    {
        $returnedResult = $this->resultDecoder->extractCrumb(file_get_contents(__DIR__.'/fixtures/lookupPage.html'));
        $this->assertEquals('kWZQDiqqBck', $returnedResult);
    }

    /**
     * @test
     * @expectedException \Scheb\YahooFinanceApi\Exception\ApiException
     * @expectedExceptionMessage Could not extract crumb from response
     */
    public function extractCrumb_invalidStringGiven_throwApiException()
    {
        $invalidHtmlString = '<html><head></head><body>The CrumbStore is not existed.</body></html>';

        $this->resultDecoder->extractCrumb($invalidHtmlString);
    }

    /**
     * @test
     */
    public function transformHistoricalDataResult_csvGiven_returnArrayOfHistoricalData()
    {
        $returnedResult = $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/historicalData.csv'));

        $this->assertInternalType('array', $returnedResult);
        $this->assertContainsOnlyInstancesOf(HistoricalData::class, $returnedResult);

        $expectedExchangeRate = new HistoricalData(
            new \DateTime('2017-07-11'),
            144.729996,
            145.850006,
            144.380005,
            145.529999,
            145.529999,
            19781800
        );
        $this->assertEquals($expectedExchangeRate, $returnedResult[0]);
    }

    /**
     * @test
     * @expectedException \Scheb\YahooFinanceApi\Exception\ApiException
     * @expectedExceptionMessage CSV header line did not match expected header line, given: 12345	1234567, expected: Date,Open,High,Low,Close,Adj Close,Volume
     */
    public function transformHistoricalDataResult_unexpectedHeaderLineCsvGiven_throwApiException()
    {
        $invalidCsvString = "12345\t1234567\t";

        $this->resultDecoder->transformHistoricalDataResult($invalidCsvString);
    }

    /**
     * @test
     * @expectedException \Scheb\YahooFinanceApi\Exception\ApiException
     * @expectedExceptionMessage CSV did not contain correct number of columns
     */
    public function transformHistoricalDataResult_invalidColumnsCsvGiven_throwApiException()
    {
        $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/invalidColumnsHistoricalData.csv'));
    }

    /**
     * @test
     * @expectedException \Scheb\YahooFinanceApi\Exception\ApiException
     * @expectedExceptionMessage Not a date in column "Date":2017-07
     */
    public function transformHistoricalDataResult_invalidDateTimeFormatCsvGiven_throwApiException()
    {
        $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/invalidDateTimeFormatHistoricalData.csv'));
    }

    /**
     * @test
     * @expectedException \Scheb\YahooFinanceApi\Exception\ApiException
     * @expectedExceptionMessage Not a number in column "High": this_is_not_numeric_string
     */
    public function transformHistoricalDataResult_invalidNumericStringCsvGiven_throwApiException()
    {
        $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/invalidNumericStringHistoricalData.csv'));
    }

    /**
     * @test
     */
    public function transformQuotes_jsonGiven_createArrayOfQuote()
    {
        $returnedResult = $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/quote.json'));

        $this->assertInternalType('array', $returnedResult);
        $this->assertCount(1, $returnedResult);
        $this->assertContainsOnlyInstancesOf(Quote::class, $returnedResult);

        $expectedQuoteData = [
            'language' => 'en-US',
            'quoteType' => 'EQUITY',
            'quoteSourceName' => 'Nasdaq Real Time Price',
            'currency' => 'USD',
            'market' => 'us_market',
            'postMarketChangePercent' => -0.029183526,
            'postMarketTime' => new \DateTime('@1510698033'),
            'postMarketPrice' => 171.29,
            'postMarketChange' => -0.05000305,
            'regularMarketChangePercent' => -1.5117577,
            'regularMarketTime' => new \DateTime('@1510693202'),
            'regularMarketChange' => -2.630005,
            'regularMarketOpen' => 173.04,
            'regularMarketDayHigh' => 173.48,
            'regularMarketDayLow' => 171.18,
            'regularMarketVolume' => 22673604,
            'exchange' => 'NMS',
            'twoHundredDayAverage' => 155.03525,
            'twoHundredDayAverageChange' => 16.304749,
            'twoHundredDayAverageChangePercent' => 0.10516801,
            'marketCap' => 879712665600,
            'forwardPE' => 15.339301,
            'shortName' => 'Apple Inc.',
            'sharesOutstanding' => 5134309888,
            'bookValue' => 25.615,
            'fiftyDayAverage' => 160.96167,
            'fiftyDayAverageChange' => 10.378326,
            'fiftyDayAverageChangePercent' => 0.064477004,
            'marketState' => 'POST',
            'priceToBook' => 6.6890492,
            'sourceInterval' => 15,
            'exchangeTimezoneName' => 'America/New_York',
            'exchangeTimezoneShortName' => 'EST',
            'gmtOffSetMilliseconds' => -18000000,
            'tradeable' => true,
            'priceHint' => 2,
            'regularMarketPrice' => 171.34,
            'exchangeDataDelayedBy' => 0,
            'regularMarketPreviousClose' => 173.97,
            'bid' => 171.1,
            'ask' => 171.15,
            'bidSize' => 50,
            'askSize' => 3,
            'messageBoardId' => 'finmb_24937',
            'fullExchangeName' => 'NasdaqGS',
            'longName' => 'Apple Inc.',
            'financialCurrency' => 'USD',
            'averageDailyVolume3Month' => 28164953,
            'averageDailyVolume10Day' => 25880733,
            'trailingAnnualDividendYield' => 0.013450595,
            'epsTrailingTwelveMonths' => 8.808,
            'epsForward' => 11.17,
            'fiftyTwoWeekLowChange' => 65.17999,
            'fiftyTwoWeekLowChangePercent' => 0.6139788,
            'fiftyTwoWeekHighChange' => -4.900009,
            'fiftyTwoWeekHighChangePercent' => -0.027803047,
            'fiftyTwoWeekLow' => 106.16,
            'fiftyTwoWeekHigh' => 176.24,
            'dividendDate' => new \DateTime('@1510790400'),
            'earningsTimestamp' => new \DateTime('@1509652800'),
            'earningsTimestampStart' => new \DateTime('@1517259600'),
            'earningsTimestampEnd' => new \DateTime('@1517605200'),
            'trailingAnnualDividendRate' => 2.34,
            'trailingPE' => 19.45277,
            'symbol' => 'AAPL',
        ];

        $expectedQuote = new Quote($expectedQuoteData);
        $this->assertEquals($expectedQuote, $returnedResult[0]);
    }

    /**
     * @test
     */
    public function transformQuotes_jsonWithNullGiven_createArrayOfQuote()
    {
        $returnedResult = $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/nullQuote.json'));

        $this->assertInternalType('array', $returnedResult);
        $this->assertCount(1, $returnedResult);
        $this->assertContainsOnlyInstancesOf(Quote::class, $returnedResult);

        $expectedQuoteData = [
            'language' => null,
            'quoteType' => 'EQUITY',
            'quoteSourceName' => 'Nasdaq Real Time Price',
            'currency' => 'USD',
            'market' => 'us_market',
            'postMarketChangePercent' => -0.029183526,
            'postMarketTime' => new \DateTime('@1510698033'),
            'postMarketPrice' => 171.29,
            'postMarketChange' => -0.05000305,
            'regularMarketChangePercent' => -1.5117577,
            'regularMarketTime' => new \DateTime('@1510693202'),
            'regularMarketChange' => -2.630005,
            'regularMarketOpen' => 173.04,
            'regularMarketDayHigh' => 173.48,
            'regularMarketDayLow' => 171.18,
            'regularMarketVolume' => 22673604,
            'exchange' => 'NMS',
            'twoHundredDayAverage' => 155.03525,
            'twoHundredDayAverageChange' => 16.304749,
            'twoHundredDayAverageChangePercent' => 0.10516801,
            'marketCap' => 879712665600,
            'forwardPE' => 15.339301,
            'shortName' => 'Apple Inc.',
            'sharesOutstanding' => 5134309888,
            'bookValue' => 25.615,
            'fiftyDayAverage' => 160.96167,
            'fiftyDayAverageChange' => 10.378326,
            'fiftyDayAverageChangePercent' => 0.064477004,
            'marketState' => 'POST',
            'priceToBook' => 6.6890492,
            'sourceInterval' => 15,
            'exchangeTimezoneName' => 'America/New_York',
            'exchangeTimezoneShortName' => 'EST',
            'gmtOffSetMilliseconds' => -18000000,
            'tradeable' => true,
            'priceHint' => 2,
            'regularMarketPrice' => 171.34,
            'exchangeDataDelayedBy' => 0,
            'regularMarketPreviousClose' => 173.97,
            'bid' => 171.1,
            'ask' => 171.15,
            'bidSize' => 50,
            'askSize' => 3,
            'messageBoardId' => 'finmb_24937',
            'fullExchangeName' => 'NasdaqGS',
            'longName' => 'Apple Inc.',
            'financialCurrency' => 'USD',
            'averageDailyVolume3Month' => 28164953,
            'averageDailyVolume10Day' => 25880733,
            'trailingAnnualDividendYield' => 0.013450595,
            'epsTrailingTwelveMonths' => 8.808,
            'epsForward' => 11.17,
            'fiftyTwoWeekLowChange' => 65.17999,
            'fiftyTwoWeekLowChangePercent' => 0.6139788,
            'fiftyTwoWeekHighChange' => -4.900009,
            'fiftyTwoWeekHighChangePercent' => -0.027803047,
            'fiftyTwoWeekLow' => 106.16,
            'fiftyTwoWeekHigh' => 176.24,
            'dividendDate' => new \DateTime('@1510790400'),
            'earningsTimestamp' => new \DateTime('@1509652800'),
            'earningsTimestampStart' => new \DateTime('@1517259600'),
            'earningsTimestampEnd' => new \DateTime('@1517605200'),
            'trailingAnnualDividendRate' => 2.34,
            'trailingPE' => 19.45277,
            'symbol' => 'AAPL',
        ];

        $expectedQuote = new Quote($expectedQuoteData);
        $this->assertEquals($expectedQuote, $returnedResult[0]);
    }

    /**
     * @test
     * @expectedException \Scheb\YahooFinanceApi\Exception\ApiException
     * @expectedExceptionMessage Not a number in field "trailingPE": 19.45277%
     */
    public function transformQuotes_jsonWithInvalidFloatGiven_createArrayOfQuote()
    {
        $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/invalidFloatQuote.json'));
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage Not a date in field "postMarketTime": invalid_date_time
     */
    public function transformQuotes_jsonWithInvalidDateTimeGiven_createArrayOfQuote()
    {
        $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/invalidDateTimeQuote.json'));
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage Not a number in field "priceHint": invalid_integer
     */
    public function transformQuotes_jsonWithInvalidIntegerGiven_createArrayOfQuote()
    {
        $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/invalidIntegerQuote.json'));
    }

    /**
     * @test
     * @expectedException \Scheb\YahooFinanceApi\Exception\ApiException
     * @expectedExceptionMessage Search result is missing fields: symbol, name, exch, type, exchDisp, typeDisp
     */
    public function transformSearchResult_jsonWithMissedFieldGiven_createSearchResultFromJson()
    {
        $jsonArray = [
            'data' => [
                'items' => [
                    ['quoteSourceName' => 'Nasdaq Real Time Price'],
                    ['currency' => 'USD'],
                ],
            ],
        ];

        $this->resultDecoder->transformSearchResult(json_encode($jsonArray));
    }
}
