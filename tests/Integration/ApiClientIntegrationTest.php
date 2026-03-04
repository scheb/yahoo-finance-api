<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;
use Scheb\YahooFinanceApi\Results\DividendData;
use Scheb\YahooFinanceApi\Results\HistoricalData;
use Scheb\YahooFinanceApi\Results\Option;
use Scheb\YahooFinanceApi\Results\OptionChain;
use Scheb\YahooFinanceApi\Results\OptionContract;
use Scheb\YahooFinanceApi\Results\Quote;
use Scheb\YahooFinanceApi\Results\SearchResult;
use Scheb\YahooFinanceApi\Results\SplitData;
use Scheb\YahooFinanceApi\Tests\TestCase;

class ApiClientIntegrationTest extends TestCase
{
    protected const APPLE_NAME = 'Apple';
    protected const APPLE_SYMBOL = 'AAPL';
    protected const GOOGLE_SYMBOL = 'GOOG';
    protected const CURRENCY_USD = 'USD';
    protected const CURRENCY_EUR = 'EUR';

    protected ApiClient $client;

    protected function setUp(): void
    {
        $this->client = ApiClientFactory::createApiClient(retries: 1);
    }

    #[Test]
    public function search_withSearchTerm_returnSearchResults(): void
    {
        $returnValue = $this->client->search(self::APPLE_NAME);

        $this->assertIsArray($returnValue);
        $this->assertContainsOnlyInstancesOf(SearchResult::class, $returnValue);

        $aaplStock = $this->findApple($returnValue);
        $this->assertNotNull($aaplStock, 'Search result must contain AAPL');

        $this->assertStringStartsWith('Apple Inc', $aaplStock->getName());
        $this->assertEquals('EQUITY', $aaplStock->getType());
        $this->assertEquals('NASDAQ', $aaplStock->getExchDisp());
        $this->assertEqualsIgnoringCase('Equity', $aaplStock->getTypeDisp());

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
     */
    private function findApple(array $searchResult, $symbol = self::APPLE_SYMBOL): ?SearchResult
    {
        foreach ($searchResult as $result) {
            if ($symbol === $result->getSymbol()) {
                return $result;
            }
        }

        return null;
    }

    #[Test]
    #[DataProvider('getTestDataForHistoricalData')]
    public function getHistoricalQuoteData_valuesForInterval_returnHistoricalData(string $interval, \DateTime $startDate, \DateTime $endDate): void
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

    #[Test]
    public function getHistoricalQuoteData_valuesForInvalidInterval_throwInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Interval must be one of: 1d, 1wk, 1mo');
        $this->client->getHistoricalQuoteData(self::APPLE_SYMBOL, 'invalid_interval', new \DateTime('-7 days'), new \DateTime('today'));
    }

    #[Test]
    public function getHistoricalQuoteData_startDateIsGreaterThanEndDate_throwInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Start date must be before end date');
        $this->client->getHistoricalQuoteData(self::APPLE_SYMBOL, ApiClient::INTERVAL_1_DAY, new \DateTime('7 days'), new \DateTime('today'));
    }

    public static function getTestDataForHistoricalData(): array
    {
        return [
            [ApiClient::INTERVAL_1_DAY, new \DateTime('-7 days'), new \DateTime('today')],
            [ApiClient::INTERVAL_1_WEEK, new \DateTime('-8 weeks'), new \DateTime('today')],
            [ApiClient::INTERVAL_1_MONTH, new \DateTime('-12 months'), new \DateTime('today')],
        ];
    }

    #[Test]
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

    #[Test]
    public function getHistoricalDividendData_startDateIsGreaterThanEndDate_throwInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Start date must be before end date');
        $this->client->getHistoricalDividendData(self::APPLE_SYMBOL, new \DateTime('7 days'), new \DateTime('today'));
    }

    #[Test]
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

    #[Test]
    public function getHistoricalSplitData_startDateIsGreaterThanEndDate_throwInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Start date must be before end date');
        $this->client->getHistoricalSplitData(self::APPLE_SYMBOL, new \DateTime('7 days'), new \DateTime('today'));
    }

    #[Test]
    public function getQuote_singleSymbol_returnQuote(): void
    {
        $returnValue = $this->client->getQuote(self::APPLE_SYMBOL);

        $this->assertInstanceOf(Quote::class, $returnValue);
        $this->assertAppleQuote($returnValue);
    }

    #[Test]
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

    #[Test]
    public function getExchangeRate_singleRate_returnExchangeRate(): void
    {
        $returnValue = $this->client->getExchangeRate(self::CURRENCY_EUR, self::CURRENCY_USD);

        $this->assertInstanceOf(Quote::class, $returnValue);
        $this->assertEurUsdExchangeRate($returnValue);
    }

    #[Test]
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

    #[Test]
    public function stockSummary_modulesGiven_returnsModulesData(): void
    {
        $returnValue = $this->client->getStockSummary(self::APPLE_SYMBOL, [
            'summaryDetail',
            'quoteType',
            'assetProfile',
            'defaultKeyStatistics',
            'financialData',
            'recommendationTrend',
            'upgradeDowngradeHistory',
            'majorHoldersBreakdown',
            'insiderHolders',
            'netSharePurchaseActivity',
            'earnings',
            'earningsHistory',
            'earningsTrend',
            'industryTrend',
            'indexTrend',
            'sectorTrend',
        ]);

        $this->assertIsArray($returnValue);
        $this->assertEquals(self::APPLE_SYMBOL, $returnValue[0]['quoteType']['symbol']);
    }

    #[Test]
    public function getOptionChain_symbolGiven_returnsContracts(): void
    {
        $returnValue = $this->client->getOptionChain(self::APPLE_SYMBOL);

        $this->assertIsArray($returnValue);
        $this->assertGreaterThan(0, \count($returnValue));
        $this->assertContainsOnlyInstancesOf(OptionChain::class, $returnValue);
        foreach ($returnValue as $optionChain) {
            $options = $optionChain->getOptions();
            $this->assertContainsOnlyInstancesOf(Option::class, $options);
            foreach ($options as $option) {
                $calls = $option->getCalls();
                $this->assertContainsOnlyInstancesOf(OptionContract::class, $calls);
                $puts = $option->getPuts();
                $this->assertContainsOnlyInstancesOf(OptionContract::class, $puts);
            }
        }
    }

    #[Test]
    public function getStockOptions_historicExpiryDate_returnsContracts(): void
    {
        $returnValue = $this->client->getOptionChain(self::APPLE_SYMBOL, new \DateTime('2024-01-04'));

        $this->assertIsArray($returnValue);
        $this->assertGreaterThan(0, \count($returnValue));
        $this->assertContainsOnlyInstancesOf(OptionChain::class, $returnValue);

        foreach ($returnValue as $optionChain) {
            $options = $optionChain->getOptions();
            $this->assertContainsOnlyInstancesOf(Option::class, $options);
            foreach ($options as $option) {
                $calls = $option->getCalls();
                $this->assertContainsOnlyInstancesOf(OptionContract::class, $calls);
                $puts = $option->getPuts();
                $this->assertContainsOnlyInstancesOf(OptionContract::class, $puts);
            }
        }
    }
}
