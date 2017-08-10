<?php
namespace Scheb\YahooFinanceApi\Tests;

use Scheb\YahooFinanceApi\ResultDecoder;
use Scheb\YahooFinanceApi\Results\ExchangeRate;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;

class ResultDecoderTest extends \PHPUnit_Framework_TestCase
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
        $returnedResult = $this->resultDecoder->transformSearchResult(file_get_contents(__DIR__ . '/fixtures/searchResult.json'));

        $this->assertInternalType('array', $returnedResult);
        $this->assertContainsOnlyInstancesOf(SearchResult::class, $returnedResult);
        $this->assertCount(10, $returnedResult);

        $expectedItem = new SearchResult(
            "AAPL",
            "Apple Inc.",
            "NAS",
            "S",
            "NASDAQ",
            "Equity"
        );
        $this->assertEquals($expectedItem, $returnedResult[0]);
    }

    /**
     * @test
     */
    public function extractCrumb_lookupPage_returnCrumbValue()
    {
        $returnedResult = $this->resultDecoder->extractCrumb(file_get_contents(__DIR__ . '/fixtures/lookupPage.html'));
        $this->assertEquals('kWZQDiqqBck', $returnedResult);
    }

    /**
     * @test
     */
    public function transformHistoricalDataResult_csvGiven_returnArrayOfHistoricalData()
    {
        $returnedResult = $this->resultDecoder->transformHistoricalDataResult(file_get_contents(__DIR__ . '/fixtures/historicalData.csv'));

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
     */
    public function transformExchangeRates_jsonGiven_returnListOfExchangeRate()
    {
        $returnedResult = $this->resultDecoder->transformExchangeRates(file_get_contents(__DIR__ . '/fixtures/exchangeRate.json'));

        $this->assertInternalType('array', $returnedResult);
        $this->assertCount(1, $returnedResult);
        $this->assertContainsOnlyInstancesOf(ExchangeRate::class, $returnedResult);

        $expectedExchangeRate = new ExchangeRate(
            'EURUSD',
            'EUR/USD',
            1.1730,
            new \DateTime('2017-08-10 06:34:00'),
            1.1731,
            1.1730
        );
        $this->assertEquals($expectedExchangeRate, $returnedResult[0]);
    }

    /**
     * @test
     */
    public function transformQuotes_jsonGiven_createArrayOfQuote()
    {
        $returnedResult = $this->resultDecoder->transformQuotes(file_get_contents(__DIR__ . '/fixtures/quote.json'));

        $this->assertInternalType('array', $returnedResult);
        $this->assertCount(1, $returnedResult);
        $this->assertContainsOnlyInstancesOf(Quote::class, $returnedResult);

        $expectedQuoteData = [
            'averageDailyVolume' => 27365600,
            'bookValue' => 25.76,
            'change' => 7.09,
            'currency' => 'USD',
            'dividendShare' => 2.52,
            'earningsShare' => 8.52,
            'epsEstimateCurrentYear' => 8.87,
            'epsEstimateNextYear' => 10.67,
            'epsEstimateNextQuarter' => 1.81,
            'dayLow' => 156.16,
            'dayHigh' => 159.75,
            'yearLow' => 102.53,
            'yearHigh' => 159.75,
            'marketCapitalization' => '819.30B',
            'ebitda' => '69.72B',
            'changeFromYearLow' => 54.61,
            'percentChangeFromYearLow' => 53.26,
            'changeFromYearHigh' => -2.61,
            'percentChangeFromYearHigh' => -1.63,
            'lastTradePrice' => 157.14,
            'fiftyDayMovingAverage' => 147.04,
            'twoHundredDayMovingAverage' => 142.6,
            'changeFromTwoHundredDayMovingAverage' => 14.54,
            'percentChangeFromTwoHundredDayMovingAverage' => 10.2,
            'changeFromFiftyDayMovingAverage' => 10.1,
            'percentChangeFromFiftyDayMovingAverage' => 6.87,
            'name' => 'Apple Inc.',
            'open' => 159.28,
            'previousClose' => 150.05,
            'changeInPercent' => 4.73,
            'priceSales' => 3.55,
            'priceBook' => 5.83,
            'exDividendDate' => new \DateTime('2017-05-11 00:00:00'),
            'peRatio' => 18.44,
            'dividendPayDate' => new \DateTime('2017-05-18 00:00:00'),
            'pegRatio' => 1.48,
            'priceEpsEstimateCurrentYear' => 17.72,
            'priceEpsEstimateNextYear' => 14.73,
            'symbol' => 'AAPL',
            'shortRatio' => 1.38,
            'oneYearTargetPrice' => 160.75,
            'volume' => 69936800,
            'stockExchange' => 'NMS',
            'dividendYield' => 1.69,
            'percentChange' => 4.73,
            'lastTradeDateTime' => new \DateTime('2017-08-02 16:00:00'),
        ];

        $expectedQuote = new Quote($expectedQuoteData);
        $this->assertEquals($expectedQuote, $returnedResult[0]);
    }
}
