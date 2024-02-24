<?php

declare(strict_types=1);

namespace Elminson\YahooFinanceApi\Tests;

use PHPUnit\Framework\TestCase;
use Elminson\YahooFinanceApi\Exception\ApiException;
use Elminson\YahooFinanceApi\ResultDecoder;
use Elminson\YahooFinanceApi\Results\DividendData;
use Elminson\YahooFinanceApi\Results\HistoricalData;
use Elminson\YahooFinanceApi\Results\Quote;
use Elminson\YahooFinanceApi\Results\SearchResult;
use Elminson\YahooFinanceApi\Results\SplitData;
use Elminson\YahooFinanceApi\ValueMapper;

class ResultDecoderTest extends TestCase
{
    /**
     * @var ResultDecoder
     */
    private $resultDecoder;

    public function setUp(): void
    {
        $this->resultDecoder = new ResultDecoder(new ValueMapper());
    }

    public function transformInvalidResponse(): array
    {
        return [
            [
                [
                    'data' => null,
                ],
            ],
            [
                [
                    'data' => [
                        'items' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider transformInvalidResponse
     */
    public function transformSearchResult_jsonGiven_createArrayOfInvalidResponse($responseBody): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Yahoo Search API returned an invalid response');

        $this->resultDecoder->transformSearchResult(json_encode($responseBody));
    }

    /**
     * @test
     */
    public function transformSearchResult_jsonGiven_createArrayOfSearchResult(): void
    {
        $returnedResult = $this->resultDecoder->transformSearchResult(file_get_contents(__DIR__.'/fixtures/searchResult.json'));

        $this->assertIsArray($returnedResult);
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
    public function extractCrumb_lookupPage_returnCrumbValue(): void
    {
        $returnedResult = $this->resultDecoder->extractCrumb(file_get_contents(__DIR__.'/fixtures/lookupPage.html'));
        $this->assertEquals('kWZQDiqqBck', $returnedResult);
    }

    /**
     * @test
     */
    public function extractCrumb_invalidStringGiven_throwApiException(): void
    {
        $invalidHtmlString = '<html><head></head><body>The CrumbStore is not existed.</body></html>';

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Could not extract crumb from response');

        $this->resultDecoder->extractCrumb($invalidHtmlString);
    }

    /**
     * @test
     */
    public function transformHistoricalDataResult_csvGiven_returnArrayOfHistoricalData(): void
    {
        $returnedResult = $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/historicalData.csv'));

        $this->assertIsArray($returnedResult);
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
     */
    public function transformHistoricalDataResult_invalidColumnsCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('CSV did not contain correct number of columns');

        $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/invalidColumnsHistoricalData.csv'));
    }

    /**
     * @test
     */
    public function transformHistoricalDataResult_unexpectedHeaderLineCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('CSV header line did not match expected header line, given: 12345	1234567, expected: Date,Open,High,Low,Close,Adj Close,Volume');

        $invalidCsvString = "12345\t1234567\t";
        $this->resultDecoder->transformHistoricalDataResult($invalidCsvString);
    }

    /**
     * @test
     */
    public function transformHistoricalDataResult_invalidDateTimeFormatCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a date in column "Date":2017-07');

        $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/invalidDateTimeFormatHistoricalData.csv'));
    }

    /**
     * @test
     */
    public function transformHistoricalDataResult_invalidNumericStringCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a number in column "High": this_is_not_numeric_string');

        $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/invalidNumericStringHistoricalData.csv'));
    }

    /**
     * @test
     */
    public function transformDividendDataResult_csvGiven_returnArrayOfDividendData(): void
    {
        $returnedResult = $this->resultDecoder->transformDividendDataResult(file_get_contents(__DIR__.'/fixtures/dividendData.csv'));

        $this->assertIsArray($returnedResult);
        $this->assertContainsOnlyInstancesOf(DividendData::class, $returnedResult);

        $expectedExchangeRate = new DividendData(
            new \DateTime('2017-07-11'),
            0.205
        );
        $this->assertEquals($expectedExchangeRate, $returnedResult[0]);
    }

    /**
     * @test
     */
    public function transformDividendDataResult_invalidColumnsCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('CSV did not contain correct number of columns');

        $this->resultDecoder->transformDividendDataResult(file_get_contents(__DIR__.'/fixtures/invalidColumnsDividendData.csv'));
    }

    /**
     * @test
     */
    public function transformDividendDataResult_unexpectedHeaderLineCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('CSV header line did not match expected header line, given: 12345	1234567, expected: Date,Dividends');

        $invalidCsvString = "12345\t1234567\t";
        $this->resultDecoder->transformDividendDataResult($invalidCsvString);
    }

    /**
     * @test
     */
    public function transformDividendDataResult_invalidDateTimeFormatCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a date in column "Date":2017-07');

        $this->resultDecoder->transformDividendDataResult(file_get_contents(__DIR__.'/fixtures/invalidDateTimeFormatDividendData.csv'));
    }

    /**
     * @test
     */
    public function transformDividendDataResult_invalidNumericStringCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a number in column Dividends: this_is_not_numeric_string');

        $this->resultDecoder->transformDividendDataResult(file_get_contents(__DIR__.'/fixtures/invalidNumericStringDividendData.csv'));
    }

    /**
     * @test
     */
    public function transformSplitDataResult_csvGiven_returnArrayOfSplitData(): void
    {
        $returnedResult = $this->resultDecoder->transformSplitDataResult(file_get_contents(__DIR__.'/fixtures/splitData.csv'));

        $this->assertIsArray($returnedResult);
        $this->assertContainsOnlyInstancesOf(SplitData::class, $returnedResult);

        $expectedExchangeRate = new SplitData(
            new \DateTime('2017-07-11'),
            '4:1'
        );
        $this->assertEquals($expectedExchangeRate, $returnedResult[0]);
    }

    /**
     * @test
     */
    public function transformSplitDataResult_invalidColumnsCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('CSV did not contain correct number of columns');

        $this->resultDecoder->transformSplitDataResult(file_get_contents(__DIR__.'/fixtures/invalidColumnsSplitData.csv'));
    }

    /**
     * @test
     */
    public function transformSplitDataResult_unexpectedHeaderLineCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('CSV header line did not match expected header line, given: 12345	1234567, expected: Date,Stock Splits');

        $invalidCsvString = "12345\t1234567\t";
        $this->resultDecoder->transformSplitDataResult($invalidCsvString);
    }

    /**
     * @test
     */
    public function transformSplitDataResult_invalidDateTimeFormatCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a date in column "Date":2017-07');

        $this->resultDecoder->transformSplitDataResult(file_get_contents(__DIR__.'/fixtures/invalidDateTimeFormatSplitData.csv'));
    }

    public function transformQuotesInvalidResult(): array
    {
        return [
            [
                [
                    'quoteResponse' => null,
                ],
            ],
            [
                [
                    [
                        'quoteResponse' => [
                            'result' => null,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider transformQuotesInvalidResult
     */
    public function transformQuotes_jsonGiven_createArrayOfInvalidResult($responseBody): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Yahoo Search API returned an invalid result');

        $this->resultDecoder->transformQuotes(json_encode($responseBody));
    }

    /**
     * @test
     */
    public function transformQuotes_jsonGiven_createArrayOfQuote(): void
    {
        $returnedResult = $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/quote.json'));

        $this->assertIsArray($returnedResult);
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
            'openInterest' => 8,
            'sourceInterval' => 15,
            'exchangeTimezoneName' => 'America/New_York',
            'exchangeTimezoneShortName' => 'EST',
            'gmtOffSetMilliseconds' => -18000000,
            'tradeable' => true,
            'priceHint' => 2,
            'preMarketChange' => -5.6799927,
            'preMarketChangePercent' => -1.6765032,
            'preMarketTime' => new \DateTime('@1592221718'),
            'preMarketPrice' => 333.12,
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
    public function transformQuotes_jsonWithNullGiven_createArrayOfQuote(): void
    {
        $returnedResult = $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/nullQuote.json'));

        $this->assertIsArray($returnedResult);
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
            'openInterest' => null,
            'sourceInterval' => 15,
            'exchangeTimezoneName' => 'America/New_York',
            'exchangeTimezoneShortName' => 'EST',
            'gmtOffSetMilliseconds' => -18000000,
            'tradeable' => true,
            'priceHint' => 2,
            'preMarketChange' => null,
            'preMarketChangePercent' => null,
            'preMarketTime' => null,
            'preMarketPrice' => null,
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
    public function transformQuotes_jsonWithInvalidFloatGiven_createArrayOfQuote(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a float in field "trailingPE": 19.45277%');

        $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/invalidFloatQuote.json'));
    }

    /**
     * @test
     */
    public function transformQuotes_jsonWithInvalidDateTimeGiven_createArrayOfQuote(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a date in field "postMarketTime": invalid_date_time');

        $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/invalidDateTimeQuote.json'));
    }

    /**
     * @test
     */
    public function transformQuotes_jsonWithInvalidIntegerGiven_createArrayOfQuote(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a int in field "priceHint": invalid_integer');

        $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/invalidIntegerQuote.json'));
    }

    /**
     * @test
     */
    public function transformSearchResult_jsonWithMissedFieldGiven_createSearchResultFromJson(): void
    {
        $jsonArray = [
            'data' => [
                'items' => [
                    ['quoteSourceName' => 'Nasdaq Real Time Price'],
                    ['currency' => 'USD'],
                ],
            ],
        ];

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Search result is missing fields: symbol, name, exch, type, exchDisp, typeDisp');

        $this->resultDecoder->transformSearchResult(json_encode($jsonArray));
    }
}
