<?php
namespace Scheb\YahooFinanceApi\Tests;

use PHPUnit\Framework\TestCase;
use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\SearchResult;

class ApiClientIntegrationTest extends TestCase
{
    const APPLE_NAME = 'Apple';
    const APPLE_SYMBOL = 'AAPL';

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
}
