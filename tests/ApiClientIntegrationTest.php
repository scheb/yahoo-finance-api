<?php
namespace Scheb\YahooFinanceApi\Tests;

use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;
use Scheb\YahooFinanceApi\Results\ExchangeRate;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;

class ApiClientIntegrationTest extends \PHPUnit_Framework_TestCase
{
    const APPLE_NAME = 'Apple';
    const APPLE_SYMBOL = 'AAPL';
    const GOOGLE_SYMBOL = 'GOOG';

    const CURRENCY_USD = 'USD';
    const CURRENCY_EUR = 'EUR';

    /**
     * @var ApiClient
     */
    private $client;

    public function setUp()
    {
        $this->client = ApiClientFactory::createApiClient();
    }

    /**
     * @test
     */
    public function search_withSearchTerm_returnSearchResults()
    {
        $returnValue = $this->client->search(self::APPLE_NAME);

        $this->assertInternalType('array', $returnValue);
        $this->assertCount(10, $returnValue);
        $this->assertContainsOnlyInstancesOf(SearchResult::class, $returnValue);

        $expectedItem = new SearchResult(
            self::APPLE_SYMBOL,
            "Apple Inc.",
            "NAS",
            "S",
            "NASDAQ",
            "Equity"
        );

        $this->assertContains($expectedItem, $returnValue, 'Search result must contain AAPL', false, false);
    }

    /**
     * @test
     * @dataProvider getTestDataForHistoricalData
     */
    public function getHistoricalData_valuesForInterval_returnHistoricalData($interval, \DateTime $startDate, \DateTime $endDate)
    {
        $returnValue = $this->client->getHistoricalData(self::APPLE_SYMBOL, $interval, $startDate, $endDate);

        $this->assertInternalType('array', $returnValue);
        $this->assertGreaterThan(0, count($returnValue));
        $this->assertContainsOnlyInstancesOf(HistoricalData::class, $returnValue);

        $historicalData = $returnValue[0];
        $this->assertInstanceOf(\DateTime::class, $historicalData->getDate());
        $this->assertInternalType('float', $historicalData->getOpen());
        $this->assertInternalType('float', $historicalData->getHigh());
        $this->assertInternalType('float', $historicalData->getLow());
        $this->assertInternalType('float', $historicalData->getClose());
        $this->assertInternalType('float', $historicalData->getAdjClose());
        $this->assertInternalType('int', $historicalData->getVolume());
    }

    public function getTestDataForHistoricalData()
    {
        return [
            [ApiClient::INTERVAL_1_DAY, new \DateTime('-7 days'), new \DateTime('today')],
            [ApiClient::INTERVAL_1_WEEK, new \DateTime('-8 weeks'), new \DateTime('today')],
            [ApiClient::INTERVAL_1_MONTH, new \DateTime('-12 months'), new \DateTime('today')],
        ];
    }

    /**
     * @test
     */
    public function getQuote_singleSymbol_returnQuote()
    {
        $returnValue = $this->client->getQuote(self::APPLE_SYMBOL);

        $this->assertInstanceOf(Quote::class, $returnValue);
        $this->assertAppleQuote($returnValue);
    }

    /**
     * @test
     */
    public function getQuotes_multipleSymbols_returnListOfQuotes()
    {
        $returnValue = $this->client->getQuotes([self::APPLE_SYMBOL, self::GOOGLE_SYMBOL]);

        $this->assertInternalType('array', $returnValue);
        $this->assertCount(2, $returnValue);
        $this->assertContainsOnlyInstancesOf(Quote::class, $returnValue);

        $appleQuote = $returnValue[0];
        $this->assertAppleQuote($appleQuote);
    }

    private function assertAppleQuote(Quote $quote)
    {
        $this->assertEquals('AAPL', $quote->getSymbol());
    }

    /**
     * @test
     */
    public function getExchangeRate_singleRate_returnExchangeRate()
    {
        $returnValue = $this->client->getExchangeRate(self::CURRENCY_USD, self::CURRENCY_EUR);

        $this->assertInstanceOf(ExchangeRate::class, $returnValue);
        $this->assertUsdEurExchangeRate($returnValue);
    }

    /**
     * @test
     */
    public function getExchangeRates_multipleOnes_returnListOfExchangeRates()
    {
        $query = [
            [self::CURRENCY_USD, self::CURRENCY_EUR],
            [self::CURRENCY_EUR, self::CURRENCY_USD],
        ];
        $returnValue = $this->client->getExchangeRates($query);

        $this->assertInternalType('array', $returnValue);
        $this->assertCount(2, $returnValue);
        $this->assertContainsOnlyInstancesOf(ExchangeRate::class, $returnValue);

        $exchangeRate = $returnValue[0];
        $this->assertUsdEurExchangeRate($exchangeRate);
    }

    private function assertUsdEurExchangeRate(ExchangeRate $exchangeRate)
    {
        $this->assertEquals(self::CURRENCY_USD . self::CURRENCY_EUR, $exchangeRate->getId());
        $this->assertEquals(self::CURRENCY_USD . '/' . self::CURRENCY_EUR, $exchangeRate->getName());
        $this->assertInstanceOf(\DateTime::class, $exchangeRate->getDateTime());
        $this->assertInternalType('float', $exchangeRate->getRate());
        $this->assertInternalType('float', $exchangeRate->getAsk());
        $this->assertInternalType('float', $exchangeRate->getBid());
    }
}
