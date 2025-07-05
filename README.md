scheb/yahoo-finance-api
=======================

**This is a PHP client for Yahoo Finance API.**

[![Build Status](https://github.com/scheb/yahoo-finance-api/actions/workflows/ci.yaml/badge.svg?branch=5.x)](https://github.com/scheb/yahoo-finance-api/actions?query=workflow%3ACI+branch%3A5.x)
[![Code Coverage](https://codecov.io/gh/scheb/yahoo-finance-api/branch/5.x/graph/badge.svg)](https://app.codecov.io/gh/scheb/yahoo-finance-api/tree/5.x)
[![Latest Stable Version](https://img.shields.io/packagist/v/scheb/yahoo-finance-api)](https://packagist.org/packages/scheb/yahoo-finance-api)
[![Total Downloads](https://img.shields.io/packagist/dt/scheb/yahoo-finance-api)](https://packagist.org/packages/scheb/yahoo-finance-api/stats)
[![License](https://poser.pugx.org/scheb/yahoo-finance-api/license.svg)](https://packagist.org/packages/scheb/yahoo-finance-api)

<p align="center"><img alt="Logo" src="doc/logo.svg" width="180" /></p>

Since YQL APIs have been discontinued in November 2017, this client is using non-official API endpoints for quotes, search and historical data.

> [!WARNING]
> These non-official APIs cannot be assumed stable and might break any time. Also, you might violate Yahoo's terms of
> service. So use them at your own risk.

- [Installation](#installation)
- [Usage](#usage)
- [Configuration](#configuration)
- [Version Guidance](#version-guidance)
- [License](#license)
- [Contributing](#contributing)
- [Support Me](#support-me)

## Installation

Download via Composer:

```bash
composer require scheb/yahoo-finance-api
```

Alternatively, you can also add the package directly to composer.json:

```json
{
    "require": {
        "scheb/yahoo-finance-api": "^5"
    }
}
```

and then tell Composer to install the package:

```bash
composer update scheb/yahoo-finance-api
```

## Usage

```php
use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;
use GuzzleHttp\Client;

// Create a new client from the factory
$client = ApiClientFactory::createApiClient();

// Or configure with options
$client = ApiClientFactory::createApiClient(
    clientOptions: [/* ... */], // Guzzle client options
    retries: 3,
    retryDelay: 1000, // milliseconds
);

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

// Returns an array of Scheb\YahooFinanceApi\Results\OptionChain
$optionChain = $client->getOptionChain("AAPL");
$optionChain = $client->getOptionChain("AAPL", new \DateTime("2021-01-01"));
```

## Configuration

### User Agent

You can customize the User-Agent header by passing it in the Guzzle client options:

```php
$guzzleClientOptions = ['headers' => ['User-Agent' => 'MyApp/1.0']];
$client = ApiClientFactory::createApiClient($guzzleClientOptions);
```

### Using curl-impersonate

This library supports [curl-impersonate](https://github.com/lexiforest/curl-impersonate) to mimic real browser behavior.
When using curl-impersonate, the library automatically removes the default User-Agent header to let curl-impersonate
handle it.

To use curl-impersonate:

1. Install curl-impersonate on your system
   * Typically, you'd want to download the `libcurl-*` package for the respective system architecture and extract it
   * Follow the [Using libcurl-impersonate in PHP scripts](https://github.com/lexiforest/curl-impersonate/blob/main/docs/07_with_php.md) guide
2. When execution your application, set the required environment variables:

```bash
export LD_PRELOAD=/path/to/curl-impersonate/libcurl-impersonate.so
export CURL_IMPERSONATE=chrome136  # or another browser version
```

3. Create your API client normally - no additional configuration needed:

```php
$client = ApiClientFactory::createApiClient();
```

The library will automatically detect the `CURL_IMPERSONATE` environment variable and configure the HTTP client
accordingly.

### Retry Feature

The library includes a built-in retry mechanism that can automatically retry failed requests. Configure it when creating
the client:

```php
$client = ApiClientFactory::createApiClient(
    retries: 3,        // Number of retry attempts (default: 0)
    retryDelay: 1000,  // Delay between retries in milliseconds (default: 0)
);
```

When a request fails, the library will:

1. Wait for the specified delay
2. Renew the session context (fetch new set of cookies and crumb value)
3. Retry the request
4. Repeat until success or max retries reached

### Context Cache Feature

The library supports caching session contexts (cookies and crumb value) to improve performance and reduce HTTP requests.
This is especially useful in high-traffic applications or when making multiple requests.

To use caching, you need a PSR-6 cache implementation, e.g. you could use `symfony/cache`:

```bash
composer require symfony/cache
```

Then configure the cache. In this example, a simple file-based cache is used:

```php
use Scheb\YahooFinanceApi\ApiClientFactory;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

// File-based cache from symfony/cache
$cache = new FilesystemAdapter();

// Create API client with caching
$client = ApiClientFactory::createApiClient(
    cache: $cache,              // PSR-6 cache implementation
    cacheTtl: 3600,             // Cache TTL in seconds (optional, default: 3600)
    cacheKey: 'my_cache_key'    // Custom cache key (optional)
);
```

Version Guidance
----------------

| Version            | Status     | PHP Version |
|--------------------|------------|-------------|
| [1.x][v1-repo]     | EOL        | >= 5.3.0    |
| [2.x][v2-repo]     | EOL        | >= 5.6.0    |
| [3.x][v3-repo]     | EOL        | >= 5.6.0    |
| [4.x][v4-repo]     | EOL        | >= 7.1.3    |
| **[5.x][v5-repo]** | Maintained | >= 8.1.0    |

[v1-repo]: https://github.com/scheb/yahoo-finance-api/tree/1.x
[v2-repo]: https://github.com/scheb/yahoo-finance-api/tree/2.x
[v3-repo]: https://github.com/scheb/yahoo-finance-api/tree/3.x
[v4-repo]: https://github.com/scheb/yahoo-finance-api/tree/4.x
[v5-repo]: https://github.com/scheb/yahoo-finance-api/tree/5.x

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
