scheb/yahoo-finance-api
=======================

**This is a PHP client for Yahoo Finance API.**

[![Build Status](https://github.com/scheb/yahoo-finance-api/workflows/CI/badge.svg?branch=4.x)](https://github.com/scheb/yahoo-finance-api/actions?query=workflow%3ACI+branch%3A4.x)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/scheb/yahoo-finance-api/badges/quality-score.png?b=4.x)](https://scrutinizer-ci.com/g/scheb/yahoo-finance-api/?branch=4.x)
[![Code Coverage](https://scrutinizer-ci.com/g/scheb/yahoo-finance-api/badges/coverage.png?b=4.x)](https://scrutinizer-ci.com/g/scheb/yahoo-finance-api/?branch=4.x)
[![Latest Stable Version](https://img.shields.io/packagist/v/scheb/yahoo-finance-api)](https://packagist.org/packages/scheb/yahoo-finance-api)
[![Total Downloads](https://img.shields.io/packagist/dt/scheb/yahoo-finance-api)](https://packagist.org/packages/scheb/yahoo-finance-api/stats)
[![License](https://poser.pugx.org/scheb/yahoo-finance-api/license.svg)](https://packagist.org/packages/scheb/yahoo-finance-api)

<p align="center"><img alt="Logo" src="doc/logo.svg" width="180" /></p>

Since YQL APIs have been discontinued in November 2017, this client is using non-official API endpoints for quotes, search and historical data.

⚠️ **WARNING:** These non-official APIs cannot be assumed stable and might break any time. Also, you might violate Yahoo's terms of service. So use them at your own risk.

## Installation

Download via Composer:

```bash
composer require scheb/yahoo-finance-api
```

Alternatively you can also add the package directly to composer.json:

```json
{
    "require": {
        "scheb/yahoo-finance-api": "^4.0"
    }
}
```

and then tell Composer to install the package:

```bash
composer update scheb/yahoo-finance-api
```

## Usage

```php
use Elminson\YahooFinanceApi\ApiClient;
use Elminson\YahooFinanceApi\ApiClientFactory;
use GuzzleHttp\Client;

// Create a new client from the factory
$client = ApiClientFactory::createApiClient();

// Or use your own Guzzle client and pass it in
$options = [/* ... */];
$guzzleClient = new Client($options);
$client = ApiClientFactory::createApiClient($guzzleClient);

// Returns an array of Scheb\YahooFinanceApi\Results\SearchResult
$searchResult = $client->search("Apple");

// Returns an array of Scheb\YahooFinanceApi\Results\HistoricalData
$historicalData = $client->getHistoricalQuoteData(
    "AAPL",
    ApiClient::INTERVAL_1_DAY,
    new \DateTime("-14 days"),
    new \DateTime("today")
);

// Retrieve dividends history, returns an array of Scheb\YahooFinanceApi\Results\DividendData
$dividendData = $client->getHistoricalDividendData(
    "AAPL",
    new \DateTime("-5 years"),
    new \DateTime("today")
);

// Retrieve stock split history, returns an array of Scheb\YahooFinanceApi\Results\SplitData
$splitData = $client->getHistoricalSplitData(
    "AAPL",
    new \DateTime("-5 years"),
    new \DateTime("today")
);

// Returns Scheb\YahooFinanceApi\Results\Quote
$exchangeRate = $client->getExchangeRate("USD", "EUR");

// Returns an array of Scheb\YahooFinanceApi\Results\Quote
$exchangeRates = $client->getExchangeRates([
    ["USD", "EUR"],
    ["EUR", "USD"],
]);

// Returns Scheb\YahooFinanceApi\Results\Quote
$quote = $client->getQuote("AAPL");

// Returns an array of Scheb\YahooFinanceApi\Results\Quote
$quotes = $client->getQuotes(["AAPL", "GOOG"]);
```

Version Guidance
----------------

| Version        | Status     | PHP Version |
|----------------|------------|-------------|
| [1.x][v1-repo] | EOL        |>= 5.3.0     |
| [2.x][v2-repo] | EOL        |>= 5.6.0     |
| [3.x][v3-repo] | EOL        |>= 5.6.0     |
| [4.x][v4-repo] | Maintained |>= 7.1.3     |

[v1-repo]: https://github.com/scheb/yahoo-finance-api/tree/1.x
[v2-repo]: https://github.com/scheb/yahoo-finance-api/tree/2.x
[v3-repo]: https://github.com/scheb/yahoo-finance-api/tree/3.x
[v4-repo]: https://github.com/scheb/yahoo-finance-api/tree/4.x

License
-------
This library is available under the [MIT license](LICENSE).

Contributing
------------
Want to contribute to this project? See [CONTRIBUTING.md](CONTRIBUTING.md).

Support Me
----------
I'm developing this library since 2014. I love to hear from people using it, giving me the motivation to keep working
on my open source projects.

If you want to let me know you're finding it useful, please consider giving it a star ⭐ on GitHub.

If you love my work and want to say thank you, you can help me out for a beer 🍻️
[via PayPal](https://paypal.me/ChristianScheb).
