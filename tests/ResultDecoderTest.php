<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Scheb\YahooFinanceApi\Exception\ApiException;
use Scheb\YahooFinanceApi\ResultDecoder;
use Scheb\YahooFinanceApi\Results\DividendData;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\OptionChain;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;
use Scheb\YahooFinanceApi\Results\SplitData;
use Scheb\YahooFinanceApi\ValueMapper;

class ResultDecoderTest extends TestCase
{
    private ResultDecoder $resultDecoder;

    protected function setUp(): void
    {
        $this->resultDecoder = new ResultDecoder(new ValueMapper());
    }

    public static function provideTransformInvalidResponse(): array
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

    #[Test]
    #[DataProvider('provideTransformInvalidResponse')]
    public function transformSearchResult_jsonGiven_createArrayOfInvalidResponse(array $responseBody): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Yahoo Search API returned an invalid response');

        $this->resultDecoder->transformSearchResult(json_encode($responseBody));
    }

    #[Test]
    public function transformSearchResult_jsonGiven_createArrayOfSearchResult(): void
    {
        $returnedResult = $this->resultDecoder->transformSearchResult(file_get_contents(__DIR__.'/fixtures/searchResult.json'));

        $this->assertIsArray($returnedResult);
        $this->assertContainsOnlyInstancesOf(SearchResult::class, $returnedResult);
        $this->assertCount(7, $returnedResult);

        $expectedItem = new SearchResult(
            'AAPL',
            'Apple Inc.',
            'NMS',
            'EQUITY',
            'NASDAQ',
            'Equity'
        );
        $this->assertEquals($expectedItem, $returnedResult[0]);
    }

    #[Test]
    public function extractCrumb_lookupPage_returnCrumbValue(): void
    {
        $returnedResult = $this->resultDecoder->extractCrumb(file_get_contents(__DIR__.'/fixtures/lookupPage.html'));
        $this->assertEquals('kWZQDiqqBck', $returnedResult);
    }

    #[Test]
    public function extractCrumb_invalidStringGiven_throwApiException(): void
    {
        $invalidHtmlString = '<html><head></head><body>The CrumbStore is not existed.</body></html>';

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Could not extract crumb from response');

        $this->resultDecoder->extractCrumb($invalidHtmlString);
    }

    #[Test]
    public function transformHistoricalDataResult_csvGiven_returnArrayOfHistoricalData(): void
    {
        $returnedResult = $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/historicalData.json'));

        $this->assertIsArray($returnedResult);
        $this->assertContainsOnlyInstancesOf(HistoricalData::class, $returnedResult);

        $expectedExchangeRate = new HistoricalData(
            new \DateTime('2024-09-30', new \DateTimeZone('UTC')),
            230.0399932861328,
            233.0,
            229.64999389648438,
            233.0,
            233.0,
            54541900
        );
        $this->assertEquals($expectedExchangeRate, $returnedResult[0]);
    }

    #[Test]
    public function transformHistoricalDataResult_noData(): void
    {
        $returnedResult = $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/noData.json'));

        $this->assertIsArray($returnedResult);
        $this->assertCount(0, $returnedResult);
    }

    #[Test]
    public function transformHistoricalDataResult_invalidColumnsCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Response is not a valid JSON');

        $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/invalidColumnsHistoricalData.csv'));
    }

    #[Test]
    public function transformHistoricalDataResult_unexpectedHeaderLineCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Response is not a valid JSON');

        $invalidCsvString = "12345\t1234567\t";
        $this->resultDecoder->transformHistoricalDataResult($invalidCsvString);
    }

    #[Test]
    public function transformHistoricalDataResult_invalidDateTimeFormatCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Response is not a valid JSON');

        $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/invalidDateTimeFormatHistoricalData.csv'));
    }

    #[Test]
    public function transformHistoricalDataResult_invalidNumericStringCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Response is not a valid JSON');

        $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__.'/fixtures/invalidNumericStringHistoricalData.csv'));
    }

    #[Test]
    public function transformDividendDataResult_csvGiven_returnArrayOfDividendData(): void
    {
        $returnedResult = $this->resultDecoder->transformDividendDataResult(file_get_contents(__DIR__.'/fixtures/dividendData.json'));

        $this->assertIsArray($returnedResult);
        $this->assertContainsOnlyInstancesOf(DividendData::class, $returnedResult);
        $firstResult = array_shift($returnedResult);

        $expectedExchangeRate = new DividendData(
            new \DateTime('2019-11-07', new \DateTimeZone('UTC')),
            0.1925
        );
        $this->assertEquals($expectedExchangeRate, $firstResult);
    }

    #[Test]
    public function transformDividendDataResult_noData(): void
    {
        $returnedResult = $this->resultDecoder->transformDividendDataResult(file_get_contents(__DIR__.'/fixtures/noData.json'));

        $this->assertIsArray($returnedResult);
        $this->assertCount(0, $returnedResult);
    }

    #[Test]
    public function transformDividendDataResult_invalidColumnsCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Response is not a valid JSON');

        $this->resultDecoder->transformDividendDataResult(file_get_contents(__DIR__.'/fixtures/invalidColumnsDividendData.csv'));
    }

    #[Test]
    public function transformDividendDataResult_unexpectedHeaderLineCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Response is not a valid JSON');

        $invalidCsvString = "12345\t1234567\t";
        $this->resultDecoder->transformDividendDataResult($invalidCsvString);
    }

    #[Test]
    public function transformDividendDataResult_invalidDateTimeFormatCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Response is not a valid JSON');

        $this->resultDecoder->transformDividendDataResult(file_get_contents(__DIR__.'/fixtures/invalidDateTimeFormatDividendData.csv'));
    }

    #[Test]
    public function transformDividendDataResult_invalidNumericStringCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Response is not a valid JSON');

        $this->resultDecoder->transformDividendDataResult(file_get_contents(__DIR__.'/fixtures/invalidNumericStringDividendData.csv'));
    }

    #[Test]
    public function transformSplitDataResult_csvGiven_returnArrayOfSplitData(): void
    {
        $returnedResult = $this->resultDecoder->transformSplitDataResult(file_get_contents(__DIR__.'/fixtures/splitData.json'));

        $this->assertIsArray($returnedResult);
        $this->assertContainsOnlyInstancesOf(SplitData::class, $returnedResult);
        $firstResult = array_shift($returnedResult);

        $expectedExchangeRate = new SplitData(
            new \DateTime('2020-08-31', new \DateTimeZone('UTC')),
            '4:1'
        );
        $this->assertEquals($expectedExchangeRate, $firstResult);
    }

    #[Test]
    public function transformSplitDataResult_noData(): void
    {
        $returnedResult = $this->resultDecoder->transformSplitDataResult(file_get_contents(__DIR__.'/fixtures/noData.json'));

        $this->assertIsArray($returnedResult);
        $this->assertCount(0, $returnedResult);
    }

    #[Test]
    public function transformSplitDataResult_invalidColumnsCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Response is not a valid JSON');

        $this->resultDecoder->transformSplitDataResult(file_get_contents(__DIR__.'/fixtures/invalidColumnsSplitData.csv'));
    }

    #[Test]
    public function transformSplitDataResult_unexpectedHeaderLineCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Response is not a valid JSON');

        $invalidCsvString = "12345\t1234567\t";
        $this->resultDecoder->transformSplitDataResult($invalidCsvString);
    }

    #[Test]
    public function transformSplitDataResult_invalidDateTimeFormatCsvGiven_throwApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Response is not a valid JSON');

        $this->resultDecoder->transformSplitDataResult(file_get_contents(__DIR__.'/fixtures/invalidDateTimeFormatSplitData.csv'));
    }

    public static function provideTransformQuotesInvalidResult(): array
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

    #[Test]
    #[DataProvider('provideTransformQuotesInvalidResult')]
    public function transformQuotes_jsonGiven_createArrayOfInvalidResult(array $responseBody): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Yahoo Search API returned an invalid result');

        $this->resultDecoder->transformQuotes(json_encode($responseBody));
    }

    #[Test]
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

    #[Test]
    public function transformQuotes_jsonWithNullGiven_createArrayOfQuote(): void
    {
        $returnedResult = $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/nullQuote.json'));

        $this->assertIsArray($returnedResult);
        $this->assertCount(1, $returnedResult);
        $this->assertContainsOnlyInstancesOf(Quote::class, $returnedResult);

        // Values undefined in the JSON are not to be initialized with null
        // Being null and "not set" is no longer the same under PHP 8
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

    #[Test]
    public function transformQuotes_jsonWithInvalidFloatGiven_createArrayOfQuote(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a float in field "trailingPE": 19.45277%');

        $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/invalidFloatQuote.json'));
    }

    #[Test]
    public function transformQuotes_jsonWithInvalidDateTimeGiven_createArrayOfQuote(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a date in field "postMarketTime": invalid_date_time');

        $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/invalidDateTimeQuote.json'));
    }

    #[Test]
    public function transformQuotes_jsonWithInvalidIntegerGiven_createArrayOfQuote(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a int in field "priceHint": invalid_integer');

        $this->resultDecoder->transformQuotes(file_get_contents(__DIR__.'/fixtures/invalidIntegerQuote.json'));
    }

    #[Test]
    public function transformSearchResult_jsonWithMissedFieldGiven_createSearchResultFromJson(): void
    {
        $jsonArray = [
            'quotes' => [
                ['shortname' => 'Test'],
            ],
        ];

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Search result is missing fields: symbol, exchange, quoteType, exchDisp, typeDisp');

        $this->resultDecoder->transformSearchResult(json_encode($jsonArray));
    }

    #[Test]
    #[DataProvider('provideTransformQuotesInvalidResult')]
    public function transformOptionChains_jsonGiven_createArrayOfInvalidResult(array $responseBody): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Yahoo Search API returned an invalid result');

        $this->resultDecoder->transformOptionChains(json_encode($responseBody));
    }

    #[Test]
    public function transformOptionChains_jsonGiven_createArrayOfOptionChain(): void
    {
        $returnedResult = $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/optionChain.json'));

        $this->assertIsArray($returnedResult);
        $this->assertCount(1, $returnedResult);
        $this->assertContainsOnlyInstancesOf(OptionChain::class, $returnedResult);

        $expectedOptionChainData = [
            [
                'underlyingSymbol' => 'AAPL',
                'expirationDates' => [
                    new \DateTime('@1711065600'),
                    new \DateTime('@1711584000'),
                    new \DateTime('@1781740800'),
                ],
                'strikes' => [
                    100.0,
                    105.0,
                    265.0,
                ],
                'hasMiniOptions' => false,
                'options' => [
                    [
                        'expirationDate' => new \DateTime('@1711065600'),
                        'hasMiniOptions' => false,
                        'calls' => [
                            [
                                'contractSymbol' => 'AAPL240322P00265000',
                                'strike' => 256.0,
                                'currency' => 'USD',
                                'lastPrice' => 93.65,
                                'change' => 6.7699966,
                                'percentChange' => 7.7744565,
                                'volume' => 3,
                                'openInterest' => 0,
                                'bid' => 90.65,
                                'ask' => 94.8,
                                'contractSize' => 'REGULAR',
                                'expiration' => new \DateTime('@1598590800'),
                                'lastTradeDate' => new \DateTime('@1597899600'),
                                'impliedVolatility' => 1.642579912109375,
                                'inTheMoney' => false,
                            ],
                        ],
                        'puts' => [
                            [
                                'contractSymbol' => 'AAPL240322P00265000',
                                'strike' => 265.0,
                                'currency' => 'USD',
                                'lastPrice' => 93.65,
                                'change' => 6.7699966,
                                'percentChange' => 7.7744565,
                                'volume' => 3,
                                'openInterest' => 0,
                                'bid' => 90.65,
                                'ask' => 94.8,
                                'contractSize' => 'REGULAR',
                                'expiration' => new \DateTime('@1598590800'),
                                'lastTradeDate' => new \DateTime('@1597899600'),
                                'impliedVolatility' => 1.642579912109375,
                                'inTheMoney' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedOptionChainData[0], $returnedResult[0]->jsonSerialize());
    }

    #[Test]
    public function transformOptionChains_jsonWithNullGiven_createArrayOfOptionChain(): void
    {
        $returnedResult = $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/nullOptionChain.json'));

        $this->assertIsArray($returnedResult);
        $this->assertCount(1, $returnedResult);
        $this->assertContainsOnlyInstancesOf(OptionChain::class, $returnedResult);

        $expectedOptionChainData = [
            [
                'underlyingSymbol' => null,
                'expirationDates' => [],
                'strikes' => [],
                'hasMiniOptions' => false,
                'options' => [
                    [
                        'expirationDate' => null,
                        'hasMiniOptions' => false,
                        'calls' => [],
                        'puts' => [],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedOptionChainData[0], $returnedResult[0]->jsonSerialize());
    }

    #[Test]
    public function transformOptionChains_jsonWithInvalidFloatGivenInOptionChain_apiExceptionThrown(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a float in field "strikes": ["invalid_float",105,265]');

        $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/invalidFloatOptionChain.json'));
    }

    #[Test]
    public function transformOptionChains_jsonWithInvalidDateTimeGivenInOptionsChain_apiExceptionThrown(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a date in field "expirationDates": ["invalid_date_time",1711584000,1781740800]');

        $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/invalidDateTimeOptionChain.json'));
    }

    #[Test]
    public function transformOptionChains_jsonWithInvalidBooleanGivenInOptionsChain_apiExceptionThrown(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a bool in field "hasMiniOptions": "invalid_boolean"');

        $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/invalidBooleanOptionChain.json'));
    }

    #[Test]
    public function transformOptionChains_jsonWithInvalidArrayGivenInOptionsChain_apiExceptionThrownForInvalidData(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a array in field "options": "invalid_array"');

        $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/invalidArrayOptionChain.json'));
    }

    #[Test]
    public function transformOptionChains_jsonGiven_createArrayOfOptions(): void
    {
        $returnedResult = $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/optionChain.json'));

        $this->assertIsArray($returnedResult);
        $this->assertCount(1, $returnedResult);
        $this->assertContainsOnlyInstancesOf(OptionChain::class, $returnedResult);

        $expectedOptionChainData = [
            [
                'underlyingSymbol' => 'AAPL',
                'expirationDates' => [
                    new \DateTime('@1711065600'),
                    new \DateTime('@1711584000'),
                    new \DateTime('@1781740800'),
                ],
                'strikes' => [
                    100.0,
                    105.0,
                    265.0,
                ],
                'hasMiniOptions' => false,
                'options' => [
                    [
                        'expirationDate' => new \DateTime('@1711065600'),
                        'hasMiniOptions' => false,
                        'calls' => [
                            [
                                'contractSymbol' => 'AAPL240322P00265000',
                                'strike' => 256.0,
                                'currency' => 'USD',
                                'lastPrice' => 93.65,
                                'change' => 6.7699966,
                                'percentChange' => 7.7744565,
                                'volume' => 3,
                                'openInterest' => 0,
                                'bid' => 90.65,
                                'ask' => 94.8,
                                'contractSize' => 'REGULAR',
                                'expiration' => new \DateTime('@1598590800'),
                                'lastTradeDate' => new \DateTime('@1597899600'),
                                'impliedVolatility' => 1.642579912109375,
                                'inTheMoney' => false,
                            ],
                        ],
                        'puts' => [
                            [
                                'contractSymbol' => 'AAPL240322P00265000',
                                'strike' => 265.0,
                                'currency' => 'USD',
                                'lastPrice' => 93.65,
                                'change' => 6.7699966,
                                'percentChange' => 7.7744565,
                                'volume' => 3,
                                'openInterest' => 0,
                                'bid' => 90.65,
                                'ask' => 94.8,
                                'contractSize' => 'REGULAR',
                                'expiration' => new \DateTime('@1598590800'),
                                'lastTradeDate' => new \DateTime('@1597899600'),
                                'impliedVolatility' => 1.642579912109375,
                                'inTheMoney' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedOptionChainData[0], $returnedResult[0]->jsonSerialize());
    }

    #[Test]
    public function transformOptionChains_jsonWithNullGiven_HandleNullResult(): void
    {
        $returnedResult = $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/nullOptionChain.json'));

        $this->assertIsArray($returnedResult);
        $this->assertCount(1, $returnedResult);
        $this->assertContainsOnlyInstancesOf(OptionChain::class, $returnedResult);

        $expectedOptionChainData = [
            [
                'underlyingSymbol' => null,
                'expirationDates' => [],
                'strikes' => [],
                'hasMiniOptions' => false,
                'options' => [
                    [
                        'expirationDate' => null,
                        'hasMiniOptions' => false,
                        'calls' => [],
                        'puts' => [],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedOptionChainData[0], $returnedResult[0]->jsonSerialize());
    }

    #[Test]
    public function transformOptionChains_jsonWithInvalidArrayGivenInOption_apiExceptionThrown(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a array in field "calls": ""');

        $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/invalidArrayOption.json'));
    }

    #[Test]
    public function transformOptionChains_jsonWithInvalidDateTimeGivenInOption_apiExceptionThrown(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a date in field "expirationDate": "invalid_date_time"');

        $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/invalidDateTimeOption.json'));
    }

    #[Test]
    public function transformOptionChains_jsonWithInvalidBooleanGivenInOption_apiExceptionThrown(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a bool in field "hasMiniOptions": "invalid_boolean"');

        $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/invalidBooleanOption.json'));
    }

    #[Test]
    public function transformOptionChains_jsonGiven_createArrayOfOptionContracts(): void
    {
        $returnedResult = $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/optionChain.json'));

        $this->assertIsArray($returnedResult);
        $this->assertCount(1, $returnedResult);
        $this->assertContainsOnlyInstancesOf(OptionChain::class, $returnedResult);

        $expectedOptionChainData = [
            [
                'underlyingSymbol' => 'AAPL',
                'expirationDates' => [
                    new \DateTime('@1711065600'),
                    new \DateTime('@1711584000'),
                    new \DateTime('@1781740800'),
                ],
                'strikes' => [
                    100.0,
                    105.0,
                    265.0,
                ],
                'hasMiniOptions' => false,
                'options' => [
                    [
                        'expirationDate' => new \DateTime('@1711065600'),
                        'hasMiniOptions' => false,
                        'calls' => [
                            [
                                'contractSymbol' => 'AAPL240322P00265000',
                                'strike' => 256.0,
                                'currency' => 'USD',
                                'lastPrice' => 93.65,
                                'change' => 6.7699966,
                                'percentChange' => 7.7744565,
                                'volume' => 3,
                                'openInterest' => 0,
                                'bid' => 90.65,
                                'ask' => 94.8,
                                'contractSize' => 'REGULAR',
                                'expiration' => new \DateTime('@1598590800'),
                                'lastTradeDate' => new \DateTime('@1597899600'),
                                'impliedVolatility' => 1.642579912109375,
                                'inTheMoney' => false,
                            ],
                        ],
                        'puts' => [
                            [
                                'contractSymbol' => 'AAPL240322P00265000',
                                'strike' => 265.0,
                                'currency' => 'USD',
                                'lastPrice' => 93.65,
                                'change' => 6.7699966,
                                'percentChange' => 7.7744565,
                                'volume' => 3,
                                'openInterest' => 0,
                                'bid' => 90.65,
                                'ask' => 94.8,
                                'contractSize' => 'REGULAR',
                                'expiration' => new \DateTime('@1598590800'),
                                'lastTradeDate' => new \DateTime('@1597899600'),
                                'impliedVolatility' => 1.642579912109375,
                                'inTheMoney' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedOptionChainData[0], $returnedResult[0]->jsonSerialize());
    }

    #[Test]
    public function transformOptionChains_jsonWithNullGiven_createArrayOfOptionContracts(): void
    {
        $returnedResult = $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/nullOptionChain.json'));

        $this->assertIsArray($returnedResult);
        $this->assertCount(1, $returnedResult);
        $this->assertContainsOnlyInstancesOf(OptionChain::class, $returnedResult);

        $expectedOptionChainData = [
            [
                'underlyingSymbol' => null,
                'expirationDates' => [],
                'strikes' => [],
                'hasMiniOptions' => false,
                'options' => [
                    [
                        'expirationDate' => null,
                        'hasMiniOptions' => false,
                        'calls' => [],
                        'puts' => [],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedOptionChainData[0], $returnedResult[0]->jsonSerialize());
    }

    #[Test]
    public function transformOptionChains_jsonWithInvalidFloatGiven_apiExceptionThrown(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a float in field "percentChange": "7.7744565%"');

        $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/invalidFloatOptionContract.json'));
    }

    #[Test]
    public function transformOptionChains_jsonWithInvalidDateTimeGiven_apiExceptionThrown(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a date in field "expiration": "invalid_date_time"');

        $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/invalidDateTimeOptionContract.json'));
    }

    #[Test]
    public function transformOptionChains_jsonWithInvalidBooleanGiven_apiExceptionThrown(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not a bool in field "inTheMoney": "invalid_boolean"');

        $this->resultDecoder->transformOptionChains(file_get_contents(__DIR__.'/fixtures/invalidBooleanOptionContract.json'));
    }
}
