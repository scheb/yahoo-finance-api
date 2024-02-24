<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests;

use GuzzleHttp\Exception\TransferException;
use PHPUnit\Framework\TestCase;
use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;
use Scheb\YahooFinanceApi\Results\DividendData;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;
use Scheb\YahooFinanceApi\Results\SplitData;

class ApiClientIntegrationTest extends TestCase
{
    private const APPLE_NAME = 'Apple';
    private const APPLE_SYMBOL = 'AAPL';
    private const APPLE_SYMBOL_FRANKFURT = 'APC.F';
    private const GOOGLE_SYMBOL = 'GOOG';

    private const CURRENCY_USD = 'USD';
    private const CURRENCY_EUR = 'EUR';
    private const TRY_COUNT = 3;
    private const RETRY_SLEEP_SECONDS = 1;

    private const REGION_US = 'en_US';
    private const REGION_GERMANY = 'de_DE';

    /**
     * @var ApiClient
     */
    private $client;

    public function setUp(): void
    {
        $this->client = ApiClientFactory::createApiClient();
    }

    /**
     * @test
     */
    public function search_withSearchTerm_returnSearchResults(): void
    {
        $returnValue = $this->client->search(self::APPLE_NAME);

        $this->assertIsArray($returnValue);
        $this->assertContainsOnlyInstancesOf(SearchResult::class, $returnValue);

        $aaplStock = $this->findApple($returnValue);
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
     * @test
     */
    public function search_withSearchTermAndRegion_returnSearchResults(): void
    {
        $returnValue = $this->client->search(self::APPLE_NAME, self::REGION_GERMANY);

        $this->assertIsArray($returnValue);
        $this->assertContainsOnlyInstancesOf(SearchResult::class, $returnValue);

        $aaplStock = $this->findApple($returnValue, self::APPLE_SYMBOL_FRANKFURT);
        $this->assertNotNull($aaplStock, 'Search result must contain APC.F');

        $this->assertEquals('Apple Inc.', $aaplStock->getName());
        $this->assertEquals('S', $aaplStock->getType());
        $this->assertEquals('Frankfurt', $aaplStock->getExchDisp());
        $this->assertEquals('Aktie', $aaplStock->getTypeDisp());

        // Can be either NAS or NMS
        $this->assertThat(
            $aaplStock->getExch(),
            $this->equalTo('FRA')
        );
    }

    /**
     * @param SearchResult[] $searchResult
     */
    private function findApple($searchResult, $symbol = self::APPLE_SYMBOL): ?SearchResult
    {
        foreach ($searchResult as $result) {
            if ($symbol === $result->getSymbol()) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @test
     * @dataProvider getTestDataForHistoricalData
     */
    public function getHistoricalQuoteData_valuesForInterval_returnHistoricalData($interval, \DateTime $startDate, \DateTime $endDate): void
    {
        $returnValue = $this->client->getHistoricalQuoteData(self::APPLE_SYMBOL, $interval, $startDate, $endDate);

        $this->assertIsArray($returnValue);
        $this->assertGreaterThan(0, \count($returnValue));
        $this->assertContainsOnlyInstancesOf(HistoricalData::class, $returnValue);

        $historicalData = $returnValue[0];
        $this->assertInstanceOf(\DateTime::class, $historicalData->getDate());
        $this->assertIsFloat($historicalData->getOpen());
        $this->assertIsFloat($historicalData->getHigh());
        $this->assertIsFloat($historicalData->getLow());
        $this->assertIsFloat($historicalData->getClose());
        $this->assertIsFloat($historicalData->getAdjClose());
        $this->assertIsInt($historicalData->getVolume());
    }

    /**
     * @test
     */
    public function getHistoricalQuoteData_valuesForInvalidInterval_throwInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Interval must be one of: 1d, 1wk, 1mo');
        $this->client->getHistoricalQuoteData(self::APPLE_SYMBOL, 'invalid_interval', new \DateTime('-7 days'), new \DateTime('today'));
    }

    /**
     * @test
     */
    public function getHistoricalQuoteData_startDateIsGreaterThanEndDate_throwInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Start date must be before end date');
        $this->client->getHistoricalQuoteData(self::APPLE_SYMBOL, ApiClient::INTERVAL_1_DAY, new \DateTime('7 days'), new \DateTime('today'));
    }

    public function getTestDataForHistoricalData(): array
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
    public function getHistoricalDividendData_valuesForInterval_returnHistoricalData(): void
    {
        $returnValue = $this->client->getHistoricalDividendData(self::APPLE_SYMBOL, new \DateTime('2020-01-01'), new \DateTime());

        $this->assertIsArray($returnValue);
        $this->assertGreaterThanOrEqual(5, \count($returnValue));
        $this->assertContainsOnlyInstancesOf(DividendData::class, $returnValue);

        // Assert the dividend from Feb 2020
        $historicalData = $returnValue[0];
        $this->assertInstanceOf(\DateTime::class, $historicalData->getDate());
        $this->assertEquals('2020-02-07', $historicalData->getDate()->format('Y-m-d'));
        $this->assertIsFloat($historicalData->getDividends());
        $this->assertEquals(0.1925, $historicalData->getDividends());
    }

    /**
     * @test
     */
    public function getHistoricalDividendData_startDateIsGreaterThanEndDate_throwInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Start date must be before end date');
        $this->client->getHistoricalDividendData(self::APPLE_SYMBOL, new \DateTime('7 days'), new \DateTime('today'));
    }

    /**
     * @test
     */
    public function getHistoricalSplitData_valuesForInterval_returnHistoricalData(): void
    {
        $returnValue = $this->client->getHistoricalSplitData(self::APPLE_SYMBOL, new \DateTime('2005-01-01'), new \DateTime());

        $this->assertIsArray($returnValue);
        $this->assertGreaterThanOrEqual(3, \count($returnValue));
        $this->assertContainsOnlyInstancesOf(SplitData::class, $returnValue);

        // Assert the stop split from Feb 2005
        $historicalData = $returnValue[0];
        $this->assertInstanceOf(\DateTime::class, $historicalData->getDate());
        $this->assertEquals('2005-02-28', $historicalData->getDate()->format('Y-m-d'));
        $this->assertEquals('2:1', $historicalData->getStockSplits());
    }

    /**
     * @test
     */
    public function getHistoricalSplitData_startDateIsGreaterThanEndDate_throwInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Start date must be before end date');
        $this->client->getHistoricalSplitData(self::APPLE_SYMBOL, new \DateTime('7 days'), new \DateTime('today'));
    }

    /**
     * @test
     */
    public function getQuote_singleSymbol_returnQuote(): void
    {
        $returnValue = $this->client->getQuote(self::APPLE_SYMBOL);

        $this->assertInstanceOf(Quote::class, $returnValue);
        $this->assertAppleQuote($returnValue);
    }

    /**
     * @test
     */
    public function getQuotes_multipleSymbols_returnListOfQuotes(): void
    {
        $returnValue = $this->client->getQuotes([self::APPLE_SYMBOL, self::GOOGLE_SYMBOL]);

        $this->assertIsArray($returnValue);
        $this->assertCount(2, $returnValue);
        $this->assertContainsOnlyInstancesOf(Quote::class, $returnValue);

        $appleQuote = $returnValue[0];
        $this->assertAppleQuote($appleQuote);
    }

    private function assertAppleQuote(Quote $quote): void
    {
        $this->assertEquals('AAPL', $quote->getSymbol());
    }

    /**
     * @test
     */
    public function getExchangeRate_singleRate_returnExchangeRate(): void
    {
        $returnValue = $this->client->getExchangeRate(self::CURRENCY_EUR, self::CURRENCY_USD);

        $this->assertInstanceOf(Quote::class, $returnValue);
        $this->assertEurUsdExchangeRate($returnValue);
    }

    /**
     * @test
     */
    public function getExchangeRates_multipleOnes_returnListOfExchangeRates(): void
    {
        $query = [
            [self::CURRENCY_EUR, self::CURRENCY_USD],
            [self::CURRENCY_USD, self::CURRENCY_EUR],
        ];
        $returnValue = $this->client->getExchangeRates($query);

        $this->assertIsArray($returnValue);
        $this->assertCount(2, $returnValue);
        $this->assertContainsOnlyInstancesOf(Quote::class, $returnValue);

        $exchangeRate = $returnValue[0];
        $this->assertEurUsdExchangeRate($exchangeRate);
    }

    private function assertEurUsdExchangeRate(Quote $exchangeRate): void
    {
        $expectedSymbol = self::CURRENCY_EUR.self::CURRENCY_USD.ApiClient::CURRENCY_SYMBOL_SUFFIX;
        $expectedName = self::CURRENCY_EUR.'/'.self::CURRENCY_USD;

        $this->assertEquals($expectedSymbol, $exchangeRate->getSymbol());
        $this->assertEquals($expectedName, $exchangeRate->getShortName());
        $this->assertInstanceOf(\DateTime::class, $exchangeRate->getRegularMarketTime());
        $this->assertIsFloat($exchangeRate->getRegularMarketPrice());
        $this->assertIsFloat($exchangeRate->getAsk());
        $this->assertIsFloat($exchangeRate->getBid());
    }

    public function runBare(): void
    {
        // I'll leave this part to you. PHPUnit supplies methods for parsing annotations.
        for ($i = 0; $i < self::TRY_COUNT; ++$i) {
            try {
                parent::runBare();

                return;
            } catch (TransferException $e) {
                // Catch all Guzzle network exceptions for retry
                if ($i < self::TRY_COUNT - 1) {
                    sleep(self::RETRY_SLEEP_SECONDS);
                }
            }
        }

        if ($e) {
            throw $e; // Throw the last exception
        }
    }

	public function testStockSummary()
	{
		$returnValue = $this->client->stockSummary(self::APPLE_SYMBOL);

		$this->assertIsArray($returnValue);
		$this->assertEquals(self::APPLE_SYMBOL, $returnValue[0]['quoteType']['symbol']);

	}
}
