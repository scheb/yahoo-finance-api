<?php

namespace Scheb\YahooFinanceApi\Tests;

use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;
use PHPUnit\Framework\TestCase;

class ApiClientIntegrationTest extends TestCase
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

        $aaplStock = $this->findAAPL($returnValue);
        $this->assertNotNull($aaplStock, 'Search result must contain AAPL');

        $this->assertEquals('Apple Inc.', $aaplStock->getName());
        $this->assertEquals('S', $aaplStock->getType());
        $this->assertEquals('NASDAQ', $aaplStock->getExchDisp());
        $this->assertEquals('Equity', $aaplStock->getTypeDisp());

        // Can be either NAS or NMS
        $this->assertThat(
            $aaplStock->getExch(),
            $this->logicalOr(
                $this->equalTo('NAS'),
                $this->equalTo('NMS')
            )
        );
    }

    /**
     * @param SearchResult[] $searchResult
     *
     * @return SearchResult|null
     */
    private function findAAPL($searchResult)
    {
        foreach ($searchResult as $result) {
            if (self::APPLE_SYMBOL === $result->getSymbol()) {
                return $result;
            }
        }

        return null;
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

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Interval must be one of: 1d, 1wk, 1mo
     */
    public function getHistoricalData_valuesForInvalidInterval_throwInvalidArgumentException()
    {
        $this->client->getHistoricalData(self::APPLE_SYMBOL, 'invalid_interval', new \DateTime('-7 days'), new \DateTime('today'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Start date must be before end date
     */
    public function getHistoricalData_startDateIsGreaterThanEndDate_throwInvalidArgumentException()
    {
        $this->client->getHistoricalData(self::APPLE_SYMBOL, ApiClient::INTERVAL_1_DAY, new \DateTime('7 days'), new \DateTime('today'));
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

        $this->assertInstanceOf(Quote::class, $returnValue);
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
        $this->assertContainsOnlyInstancesOf(Quote::class, $returnValue);

        $exchangeRate = $returnValue[0];
        $this->assertUsdEurExchangeRate($exchangeRate);
    }

    private function assertUsdEurExchangeRate(Quote $exchangeRate)
    {
        $expectedSymbol = self::CURRENCY_USD.self::CURRENCY_EUR.ApiClient::CURRENCY_SYMBOL_SUFFIX;
        $expectedName = self::CURRENCY_USD.'/'.self::CURRENCY_EUR;

        $this->assertEquals($expectedSymbol, $exchangeRate->getSymbol());
        $this->assertEquals($expectedName, $exchangeRate->getShortName());
        $this->assertInstanceOf(\DateTime::class, $exchangeRate->getRegularMarketTime());
        $this->assertInternalType('float', $exchangeRate->getRegularMarketPrice());
        $this->assertInternalType('float', $exchangeRate->getAsk());
        $this->assertInternalType('float', $exchangeRate->getBid());
    }
}
