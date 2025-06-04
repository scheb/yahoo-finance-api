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
    private ?float $ask;
    private ?int $askSize;
    private ?int $averageDailyVolume10Day;
    private ?int $averageDailyVolume3Month;
    private ?float $bid;
    private ?int $bidSize;
    private ?float $bookValue;
    private ?string $currency;
    private ?\DateTimeInterface $dividendDate;
    private ?\DateTimeInterface $earningsTimestamp;
    private ?\DateTimeInterface $earningsTimestampStart;
    private ?\DateTimeInterface $earningsTimestampEnd;
    private ?float $epsForward;
    private ?float $epsTrailingTwelveMonths;
    private ?string $exchange;
    private ?int $exchangeDataDelayedBy;
    private ?string $exchangeTimezoneName;
    private ?string $exchangeTimezoneShortName;
    private ?float $fiftyDayAverage;
    private ?float $fiftyDayAverageChange;
    private ?float $fiftyDayAverageChangePercent;
    private ?float $fiftyTwoWeekHigh;
    private ?float $fiftyTwoWeekHighChange;
    private ?float $fiftyTwoWeekHighChangePercent;
    private ?float $fiftyTwoWeekLow;
    private ?float $fiftyTwoWeekLowChange;
    private ?float $fiftyTwoWeekLowChangePercent;
    private ?string $financialCurrency;
    private ?float $forwardPE;
    private ?string $fullExchangeName;
    private ?int $gmtOffSetMilliseconds;
    private ?string $language;
    private ?string $longName;
    private ?string $market;
    private ?int $marketCap;
    private ?string $marketState;
    private ?string $messageBoardId;
    private ?float $postMarketChange;
    private ?float $postMarketChangePercent;
    private ?float $postMarketPrice;
    private ?\DateTimeInterface $postMarketTime;
    private ?float $preMarketChange;
    private ?float $preMarketChangePercent;
    private ?float $preMarketPrice;
    private ?\DateTimeInterface $preMarketTime;
    private ?int $priceHint;
    private ?float $priceToBook;
    private ?float $openInterest;
    private ?string $quoteSourceName;
    private ?string $quoteType;
    private ?float $regularMarketChange;
    private ?float $regularMarketChangePercent;
    private ?float $regularMarketDayHigh;
    private ?float $regularMarketDayLow;
    private ?float $regularMarketOpen;
    private ?float $regularMarketPreviousClose;
    private ?float $regularMarketPrice;
    private ?\DateTimeInterface $regularMarketTime;
    private ?int $regularMarketVolume;
    private ?int $sharesOutstanding;
    private ?string $shortName;
    private ?int $sourceInterval;
    private ?string $symbol;
    private ?bool $tradeable;
    private ?float $trailingAnnualDividendRate;
    private ?float $trailingAnnualDividendYield;
    private ?float $trailingPE;
    private ?float $twoHundredDayAverage;
    private ?float $twoHundredDayAverageChange;
    private ?float $twoHundredDayAverageChangePercent;

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
