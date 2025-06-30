<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Quote implements \JsonSerializable
{
    private ?float $ask = null;
    private ?int $askSize = null;
    private ?int $averageDailyVolume10Day = null;
    private ?int $averageDailyVolume3Month = null;
    private ?float $bid = null;
    private ?int $bidSize = null;
    private ?float $bookValue = null;
    private ?string $currency = null;
    private ?\DateTimeInterface $dividendDate = null;
    private ?\DateTimeInterface $earningsTimestamp = null;
    private ?\DateTimeInterface $earningsTimestampStart = null;
    private ?\DateTimeInterface $earningsTimestampEnd = null;
    private ?float $epsForward = null;
    private ?float $epsTrailingTwelveMonths = null;
    private ?string $exchange = null;
    private ?int $exchangeDataDelayedBy = null;
    private ?string $exchangeTimezoneName = null;
    private ?string $exchangeTimezoneShortName = null;
    private ?float $fiftyDayAverage = null;
    private ?float $fiftyDayAverageChange = null;
    private ?float $fiftyDayAverageChangePercent = null;
    private ?float $fiftyTwoWeekHigh = null;
    private ?float $fiftyTwoWeekHighChange = null;
    private ?float $fiftyTwoWeekHighChangePercent = null;
    private ?float $fiftyTwoWeekLow = null;
    private ?float $fiftyTwoWeekLowChange = null;
    private ?float $fiftyTwoWeekLowChangePercent = null;
    private ?string $financialCurrency = null;
    private ?float $forwardPE = null;
    private ?string $fullExchangeName = null;
    private ?int $gmtOffSetMilliseconds = null;
    private ?string $language = null;
    private ?string $longName = null;
    private ?string $market = null;
    private ?int $marketCap = null;
    private ?string $marketState = null;
    private ?string $messageBoardId = null;
    private ?float $postMarketChange = null;
    private ?float $postMarketChangePercent = null;
    private ?float $postMarketPrice = null;
    private ?\DateTimeInterface $postMarketTime = null;
    private ?float $preMarketChange = null;
    private ?float $preMarketChangePercent = null;
    private ?float $preMarketPrice = null;
    private ?\DateTimeInterface $preMarketTime = null;
    private ?int $priceHint = null;
    private ?float $priceToBook = null;
    private ?float $openInterest = null;
    private ?string $quoteSourceName = null;
    private ?string $quoteType = null;
    private ?float $regularMarketChange = null;
    private ?float $regularMarketChangePercent = null;
    private ?float $regularMarketDayHigh = null;
    private ?float $regularMarketDayLow = null;
    private ?float $regularMarketOpen = null;
    private ?float $regularMarketPreviousClose = null;
    private ?float $regularMarketPrice = null;
    private ?\DateTimeInterface $regularMarketTime = null;
    private ?int $regularMarketVolume = null;
    private ?int $sharesOutstanding = null;
    private ?string $shortName = null;
    private ?int $sourceInterval = null;
    private ?string $symbol = null;
    private ?bool $tradeable = null;
    private ?float $trailingAnnualDividendRate = null;
    private ?float $trailingAnnualDividendYield = null;
    private ?float $trailingPE = null;
    private ?float $twoHundredDayAverage = null;
    private ?float $twoHundredDayAverageChange = null;
    private ?float $twoHundredDayAverageChangePercent = null;

    public function __construct(array $values)
    {
        foreach ($values as $property => $value) {
            $this->{$property} = $value;
        }
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            get_class_vars(self::class),
            get_object_vars($this)
        );
    }

    public function getAsk(): ?float
    {
        return $this->ask;
    }

    public function getAskSize(): ?int
    {
        return $this->askSize;
    }

    public function getAverageDailyVolume10Day(): ?int
    {
        return $this->averageDailyVolume10Day;
    }

    public function getAverageDailyVolume3Month(): ?int
    {
        return $this->averageDailyVolume3Month;
    }

    public function getBid(): ?float
    {
        return $this->bid;
    }

    public function getBidSize(): ?int
    {
        return $this->bidSize;
    }

    public function getBookValue(): ?float
    {
        return $this->bookValue;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getDividendDate(): ?\DateTimeInterface
    {
        return $this->dividendDate;
    }

    public function getEarningsTimestamp(): ?\DateTimeInterface
    {
        return $this->earningsTimestamp;
    }

    public function getEarningsTimestampStart(): ?\DateTimeInterface
    {
        return $this->earningsTimestampStart;
    }

    public function getEarningsTimestampEnd(): ?\DateTimeInterface
    {
        return $this->earningsTimestampEnd;
    }

    public function getEpsForward(): ?float
    {
        return $this->epsForward;
    }

    public function getEpsTrailingTwelveMonths(): ?float
    {
        return $this->epsTrailingTwelveMonths;
    }

    public function getExchange(): ?string
    {
        return $this->exchange;
    }

    public function getExchangeDataDelayedBy(): ?int
    {
        return $this->exchangeDataDelayedBy;
    }

    public function getExchangeTimezoneName(): ?string
    {
        return $this->exchangeTimezoneName;
    }

    public function getExchangeTimezoneShortName(): ?string
    {
        return $this->exchangeTimezoneShortName;
    }

    public function getFiftyDayAverage(): ?float
    {
        return $this->fiftyDayAverage;
    }

    public function getFiftyDayAverageChange(): ?float
    {
        return $this->fiftyDayAverageChange;
    }

    public function getFiftyDayAverageChangePercent(): ?float
    {
        return $this->fiftyDayAverageChangePercent;
    }

    public function getFiftyTwoWeekHigh(): ?float
    {
        return $this->fiftyTwoWeekHigh;
    }

    public function getFiftyTwoWeekHighChange(): ?float
    {
        return $this->fiftyTwoWeekHighChange;
    }

    public function getFiftyTwoWeekHighChangePercent(): ?float
    {
        return $this->fiftyTwoWeekHighChangePercent;
    }

    public function getFiftyTwoWeekLow(): ?float
    {
        return $this->fiftyTwoWeekLow;
    }

    public function getFiftyTwoWeekLowChange(): ?float
    {
        return $this->fiftyTwoWeekLowChange;
    }

    public function getFiftyTwoWeekLowChangePercent(): ?float
    {
        return $this->fiftyTwoWeekLowChangePercent;
    }

    public function getFinancialCurrency(): ?string
    {
        return $this->financialCurrency;
    }

    public function getForwardPE(): ?float
    {
        return $this->forwardPE;
    }

    public function getFullExchangeName(): ?string
    {
        return $this->fullExchangeName;
    }

    public function getGmtOffSetMilliseconds(): ?int
    {
        return $this->gmtOffSetMilliseconds;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getLongName(): ?string
    {
        return $this->longName;
    }

    public function getMarket(): ?string
    {
        return $this->market;
    }

    public function getMarketCap(): ?int
    {
        return $this->marketCap;
    }

    public function getMarketState(): ?string
    {
        return $this->marketState;
    }

    public function getMessageBoardId(): ?string
    {
        return $this->messageBoardId;
    }

    public function getPostMarketChange(): ?float
    {
        return $this->postMarketChange;
    }

    public function getPostMarketChangePercent(): ?float
    {
        return $this->postMarketChangePercent;
    }

    public function getPostMarketPrice(): ?float
    {
        return $this->postMarketPrice;
    }

    public function getPostMarketTime(): ?\DateTimeInterface
    {
        return $this->postMarketTime;
    }

    public function getPreMarketChange(): ?float
    {
        return $this->preMarketChange;
    }

    public function getPreMarketChangePercent(): ?float
    {
        return $this->preMarketChangePercent;
    }

    public function getPreMarketPrice(): ?float
    {
        return $this->preMarketPrice;
    }

    public function getPreMarketTime(): ?\DateTimeInterface
    {
        return $this->preMarketTime;
    }

    public function getPriceHint(): ?int
    {
        return $this->priceHint;
    }

    public function getPriceToBook(): ?float
    {
        return $this->priceToBook;
    }

    public function getOpenInterest(): ?float
    {
        return $this->openInterest;
    }

    public function getQuoteSourceName(): ?string
    {
        return $this->quoteSourceName;
    }

    public function getQuoteType(): ?string
    {
        return $this->quoteType;
    }

    public function getRegularMarketChange(): ?float
    {
        return $this->regularMarketChange;
    }

    public function getRegularMarketChangePercent(): ?float
    {
        return $this->regularMarketChangePercent;
    }

    public function getRegularMarketDayHigh(): ?float
    {
        return $this->regularMarketDayHigh;
    }

    public function getRegularMarketDayLow(): ?float
    {
        return $this->regularMarketDayLow;
    }

    public function getRegularMarketOpen(): ?float
    {
        return $this->regularMarketOpen;
    }

    public function getRegularMarketPreviousClose(): ?float
    {
        return $this->regularMarketPreviousClose;
    }

    public function getRegularMarketPrice(): ?float
    {
        return $this->regularMarketPrice;
    }

    public function getRegularMarketTime(): ?\DateTimeInterface
    {
        return $this->regularMarketTime;
    }

    public function getRegularMarketVolume(): ?int
    {
        return $this->regularMarketVolume;
    }

    public function getSharesOutstanding(): ?int
    {
        return $this->sharesOutstanding;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function getSourceInterval(): ?int
    {
        return $this->sourceInterval;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function getTradeable(): ?bool
    {
        return $this->tradeable;
    }

    public function getTrailingAnnualDividendRate(): ?float
    {
        return $this->trailingAnnualDividendRate;
    }

    public function getTrailingAnnualDividendYield(): ?float
    {
        return $this->trailingAnnualDividendYield;
    }

    public function getTrailingPE(): ?float
    {
        return $this->trailingPE;
    }

    public function getTwoHundredDayAverage(): ?float
    {
        return $this->twoHundredDayAverage;
    }

    public function getTwoHundredDayAverageChange(): ?float
    {
        return $this->twoHundredDayAverageChange;
    }

    public function getTwoHundredDayAverageChangePercent(): ?float
    {
        return $this->twoHundredDayAverageChangePercent;
    }
}
