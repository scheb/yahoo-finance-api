<?php
namespace Scheb\YahooFinanceApi\Tests;

use Scheb\YahooFinanceApi\ResultDecoder;
use Scheb\YahooFinanceApi\Results\Quote;

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
    public function transformQuotes_singleResult_createQuote()
    {
        $returnedResult = $this->resultDecoder->transformQuotes(file_get_contents(__DIR__ . '/fixtures/quote.json'));

        $this->assertInternalType('array', $returnedResult);
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
        $returnedQuote = $returnedResult[0];
        $this->assertEquals($expectedQuote, $returnedQuote);
    }
}
