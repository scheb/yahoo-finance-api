Upgrade
=======

## From 4.x to 5.x

Minimum required PHP version is now 8.1.

`stockSummary()` has been removed. Use `getStockSummary()` instead. The new method requires a list of modules to be
fetched. See method description for a list of known modules. Feel free to explore what kind of data you can retrieve
with that function. An example dataset for the Apple stock can be found in [stockSummaryExample.json](doc/stockSummaryExample.json).

`getHistoricalData()` has been removed. Use `getHistoricalQuoteData()` instead.

Instead of passing a Guzzle client to `ApiClientFactory`, now just pass the client `$options`. The Guzzle client
instance will be automatically created with these options.

Before:

```
$guzzleClientOptions = [/* ... */];
$guzzleClient = new Client($guzzleClientOptions);
$client = ApiClientFactory::createApiClient($guzzleClient);
```

After:

```
$guzzleClientOptions = [/* ... */];
$client = ApiClientFactory::createApiClient($guzzleClientOptions);
```

When using [curl-impersonate)](https://github.com/lexiforest/curl-impersonate), you no longer need to set the
`User-Agent` as the library will automatically make sure the correct one is used.

## From 3.x to 4.x

Minimum required PHP version is now 7.1.3.

The library now has PHP7.1 type-hints and `declare(strict_types=1)` everywhere. Please make sure the values
passed to the library match the type.

## From 2.x to 3.x

Since Yahoo Finance YQL API stopped working in November 2017, this client switched to a different endpoint.
Because of that the `Quote` entity changed according to the fields available from the new API endpoint.
Also, exchange rates are now represented as `Quote` entities.
