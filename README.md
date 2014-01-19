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
        "scheb/yahoo-finance-api": "dev-master"
    }
}
```

and then tell Composer to install the bundle:

```bash
php composer.phar update scheb/yahoo-finance-api
```

## Usage

```php
$client = new \Scheb\YahooFinanceApi\ApiClient();

//Fetch basic data
$data = $client->getQuotesList("YHOO"); //Single stock
$data = $client->getQuotesList(array("YHOO", "GOOG")); //Multiple stocks at once

//Fetch full data set
$data = $client->getQuotes("YHOO"); //Single stock
$data = $client->getQuotes(array("YHOO", "GOOG")); //Multiple stocks at once

//Get historical data
$data = $client->getHistoricalData("YHOO");

//Search stocks
$data = $client->search("Yahoo");
```

Each function returns the decoded JSON response as an associative array. See the following examples:

  - [getQuotesList](http://query.yahooapis.com/v1/public/yql?env=http%3A%2F%2Fdatatables.org%2Falltables.env&format=json&q=select+*+from+yahoo.finance.quoteslist+where+symbol+in+%28%27YHOO%27,%27GOOG%27%29) for Yahoo and Google
  - [getQuotes](http://query.yahooapis.com/v1/public/yql?env=http%3A%2F%2Fdatatables.org%2Falltables.env&format=json&q=select+*+from+yahoo.finance.quotes+where+symbol+in+%28%27YHOO%27,%27GOOG%27%29) for Yahoo and Google
  - [getHistoricalData](http://query.yahooapis.com/v1/public/yql?env=http%3A%2F%2Fdatatables.org%2Falltables.env&format=json&q=select%20*%20from%20yahoo.finance.historicaldata%20where%20startDate=%272014-01-01%27%20and%20endDate=%272014-01-10%27%20and%20symbol=%27YHOO%27) for Yahoo
  - [search](http://autoc.finance.yahoo.com/autoc?query=Yahoo&callback=YAHOO.Finance.SymbolSuggest.ssCallback) for Yahoo
