scheb/yahoo-finance-api
=======================

This is a PHP client for Yahoo Finance API. It provides easy access to stock quotes via Yahoo's [YQL API] (http://developer.yahoo.com/yql/).

## Installation

Download via Composer:

```bash
php composer.phar require scheb/yahoo-finance-api
```

When being asked for the version use dev-master or any different version you want.

Alternatively you can also add the bundle directly to composer.json:

```js
{
    "require": {
        "scheb/yahoo-finance-api": "^2.0"
    }
}
```

and then tell Composer to install the bundle:

```bash
php composer.phar update scheb/yahoo-finance-api
```

## Usage

```php
$client = \Scheb\YahooFinanceApi\ApiClientFactory::createApiClient();

// Or use your own Guzzle client and pass it in
// $guzzleClient = new \GuzzleHttp\Client($options);
// $client = \Scheb\YahooFinanceApi\ApiClientFactory::createApiClient($guzzleClient);

// TDB
```
